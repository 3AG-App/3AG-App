<?php

use App\Models\User;
use App\Models\UserPreference;
use Inertia\Testing\AssertableInertia as Assert;

it('shares the default locale to inertia', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('locale', config('app.locale'))
        );
});

it('allows a guest to change locale via cookie', function () {
    $this->from('/')
        ->post('/locale', ['locale' => 'de'])
        ->assertRedirect('/')
        ->assertCookie('locale');

    $this->withCookie('locale', 'de')
        ->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('locale', 'de')
        );
});

it('uses request preferred language when no user or cookie locale is set', function () {
    $this->withHeader('Accept-Language', 'fr-CA,fr;q=0.9,en;q=0.8')
        ->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('locale', 'fr')
        );
});

it('persists locale to the user preference when authenticated and uses it over the cookie', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/')
        ->post('/locale', ['locale' => 'fr'])
        ->assertRedirect('/')
        ->assertCookie('locale');

    expect(UserPreference::query()->where('user_id', $user->id)->value('locale'))
        ->toBe('fr');

    $this->actingAs($user)
        ->withCookie('locale', 'de')
        ->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('locale', 'fr')
        );
});

it('can change locale from the dashboard settings page and persists it to the user preference', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/dashboard/settings')
        ->post('/locale', ['locale' => 'de'])
        ->assertRedirect('/dashboard/settings')
        ->assertCookie('locale');

    expect(UserPreference::query()->where('user_id', $user->id)->value('locale'))
        ->toBe('de');

    $this->actingAs($user)
        ->get('/dashboard/settings')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard/settings')
            ->where('locale', 'de')
        );
});

it('translates backend toast messages using the active locale', function () {
    $user = User::factory()->unverified()->create();

    UserPreference::query()->updateOrCreate(
        ['user_id' => $user->id],
        ['locale' => 'de'],
    );

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect();

    $this->actingAs($user)
        ->get('/email/verify')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/verify-email')
            ->hasFlash('toast.type', 'success')
            ->hasFlash('toast.message', 'Bestätigungslink gesendet!')
        );
});

it('applies locale preference middleware to filament routes', function () {
    $adminEmail = 'admin@test.com';
    config(['admin.emails' => [$adminEmail]]);

    $admin = User::factory()->create(['email' => $adminEmail]);

    app()->setLocale('en');

    $this->actingAs($admin)
        ->withCookie('locale', 'fr')
        ->get('/admin')
        ->assertOk();

    expect(app()->getLocale())->toBe('fr');
});

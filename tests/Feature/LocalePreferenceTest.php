<?php

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\CookieValuePrefix;
use Inertia\Testing\AssertableInertia as Assert;

function encryptedCookie(string $name, string $value): string
{
    /** @var Encrypter $encrypter */
    $encrypter = app(Encrypter::class);

    $key = method_exists($encrypter, 'getKey')
        ? $encrypter->getKey()
        : config('app.key');

    return $encrypter->encrypt(CookieValuePrefix::create($name, $key).$value, false);
}

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

    $this->withCookie('locale', encryptedCookie('locale', 'de'))
        ->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('home')
            ->where('locale', 'de')
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
        ->withCookie('locale', encryptedCookie('locale', 'de'))
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

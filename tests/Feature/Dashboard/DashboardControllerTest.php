<?php

use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Package;
use App\Models\Product;
use App\Models\Release;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Cashier\Subscription;

it('redirects guests to login', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});

it('shows the dashboard to authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/index'));
});

it('shows dashboard stats for user with licenses', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->active()
        ->create();

    LicenseActivation::factory()
        ->for($license)
        ->active()
        ->count(2)
        ->create();

    Subscription::query()->create([
        'user_id' => $user->id,
        'type' => 'product_package',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'trialing',
        'stripe_price' => 'price_test',
        'quantity' => 1,
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/index')
            ->where('stats.active_subscriptions', 1)
            ->has('recent_licenses')
            ->has('subscriptions')
        );
});

it('shows the subscriptions page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/subscriptions')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/subscriptions/index'));
});

it('shows the licenses page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/licenses')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/licenses/index'));
});

it('shows a specific license', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->create();

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/licenses/show'));
});

it('prevents viewing another users license', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($otherUser)
        ->for($product)
        ->for($package)
        ->create();

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}")
        ->assertForbidden();
});

it('shows the profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/profile')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/profile'));
});

it('shows the settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/settings')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('dashboard/settings'));
});

it('updates user profile', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $this->actingAs($user)
        ->put('/dashboard/profile', [
            'name' => 'New Name',
            'email' => $user->email,
        ])
        ->assertRedirect();

    expect($user->fresh()->name)->toBe('New Name');
});

it('can deactivate a license activation', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->create();

    $activation = LicenseActivation::factory()
        ->for($license)
        ->active()
        ->create();

    $this->actingAs($user)
        ->delete("/dashboard/licenses/{$license->id}/activations/{$activation->id}")
        ->assertRedirect();

    expect($activation->fresh()->deactivated_at)->not->toBeNull();
});

it('allows downloading the latest release zip for an active owned license', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->active()
        ->create();

    $olderRelease = Release::factory()->for($product)->create([
        'version' => '1.0.0',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $olderRelease->addMedia(UploadedFile::fake()->createWithContent('release-v1.zip', 'v1'))
        ->toMediaCollection('zip');

    $latestRelease = Release::factory()->for($product)->create([
        'version' => '1.1.0',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $latestRelease->addMedia(UploadedFile::fake()->createWithContent('release-v2.zip', 'v2'))
        ->toMediaCollection('zip');

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}/download-latest-release")
        ->assertOk()
        ->assertDownload('release-v2.zip');
});

it('forbids downloading release files for licenses owned by another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($otherUser)
        ->for($product)
        ->for($package)
        ->active()
        ->create();

    $release = Release::factory()->for($product)->create(['version' => '2.0.0']);
    $release->addMedia(UploadedFile::fake()->createWithContent('release.zip', 'content'))
        ->toMediaCollection('zip');

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}/download-latest-release")
        ->assertForbidden();
});

it('forbids downloading release files for inactive licenses', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->suspended()
        ->create();

    $release = Release::factory()->for($product)->create(['version' => '2.0.0']);
    $release->addMedia(UploadedFile::fake()->createWithContent('release.zip', 'content'))
        ->toMediaCollection('zip');

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}/download-latest-release")
        ->assertForbidden();
});

it('returns not found when no latest release zip exists for a license product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->active()
        ->create();

    Release::factory()->for($product)->create(['version' => '3.0.0']);

    $this->actingAs($user)
        ->get("/dashboard/licenses/{$license->id}/download-latest-release")
        ->assertNotFound();
});

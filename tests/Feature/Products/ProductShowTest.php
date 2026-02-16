<?php

use App\Models\Package;
use App\Models\Product;
use App\Models\Release;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Cashier\Subscription;

it('shows the product detail with ordered packages', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'short_description' => ['en' => 'Short product description.'],
        'long_description' => ['en' => '<p>Long product description.</p>'],
    ]);

    Package::factory()->for($product)->create([
        'monthly_price' => '29.00',
        'yearly_price' => '290.00',
        'sort_order' => 2,
    ]);

    Package::factory()->for($product)->create([
        'monthly_price' => '9.00',
        'yearly_price' => '90.00',
        'sort_order' => 1,
    ]);

    $this->get("/products/{$product->slug}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/show')
            ->has('product', fn (Assert $productPage) => $productPage
                ->where('id', $product->id)
                ->where('short_description', 'Short product description.')
                ->where('long_description', '<p>Long product description.</p>')
                ->has('packages', 2)
                ->where('packages.0.monthly_price', '9.00')
                ->where('packages.1.monthly_price', '29.00')
                ->etc()
            )
        );
});

it('includes current subscription when user is on trial', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'is_active' => true,
    ]);

    $package = Package::factory()->for($product)->create([
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Subscription::query()->create([
        'user_id' => $user->id,
        'type' => $package->getSubscriptionName(),
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'trialing',
        'stripe_price' => $package->stripe_monthly_price_id,
        'quantity' => 1,
        'trial_ends_at' => now()->addDays(7),
    ]);

    $this->actingAs($user)
        ->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/show')
            ->where('currentSubscription.package_id', $package->id)
            ->where('currentSubscription.package_name', $package->name)
            ->where('currentSubscription.is_yearly', false)
        );
});

it('includes latest download info when user has a valid license and product has a release zip', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    $license = \App\Models\License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->active()
        ->create();

    $oldRelease = Release::factory()->for($product)->create([
        'version' => '1.0.0',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $oldRelease->addMedia(UploadedFile::fake()->createWithContent('old.zip', 'old'))
        ->toMediaCollection('zip');

    $latestRelease = Release::factory()->for($product)->create([
        'version' => '1.1.0',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $latestRelease->addMedia(UploadedFile::fake()->createWithContent('latest.zip', 'latest'))
        ->toMediaCollection('zip');

    $this->actingAs($user)
        ->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/show')
            ->where('latestDownload.version', '1.1.0')
            ->where('latestDownload.url', route('dashboard.licenses.download-latest-release', ['license' => $license]))
        );
});

it('does not include latest download info when license is not valid', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_active' => true]);
    $package = Package::factory()->for($product)->create(['is_active' => true]);

    \App\Models\License::factory()
        ->for($user)
        ->for($product)
        ->for($package)
        ->suspended()
        ->create();

    $release = Release::factory()->for($product)->create(['version' => '2.0.0']);
    $release->addMedia(UploadedFile::fake()->createWithContent('latest.zip', 'latest'))
        ->toMediaCollection('zip');

    $this->actingAs($user)
        ->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/show')
            ->where('latestDownload', null)
        );
});

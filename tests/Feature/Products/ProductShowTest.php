<?php

use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Cashier\Subscription;

it('shows the product detail with ordered packages', function () {
    $product = Product::factory()->create([
        'is_active' => true,
        'short_description' => 'Short product description.',
        'long_description' => '<p>Long product description.</p>',
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

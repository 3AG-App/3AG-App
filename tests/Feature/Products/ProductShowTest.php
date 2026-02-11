<?php

use App\Models\Package;
use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

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

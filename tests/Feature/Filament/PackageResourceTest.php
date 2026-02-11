<?php

use App\Filament\Resources\Products\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Products\Resources\Packages\Pages\EditPackage;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $adminEmail = 'admin@test.com';
    config(['admin.emails' => [$adminEmail]]);

    $this->admin = User::factory()->create(['email' => $adminEmail]);
    $this->actingAs($this->admin);

    $this->product = Product::factory()->create();
});

describe('Create Package Page', function () {
    it('can create a package', function () {
        Livewire::test(CreatePackage::class, [
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'name' => 'Basic Plan',
                'slug' => 'basic-plan',
                'description' => 'A basic subscription plan',
                'domain_limit' => 3,
                'monthly_price' => 9.99,
                'yearly_price' => 99.99,
                'stripe_monthly_price_id' => 'price_monthly_test',
                'stripe_yearly_price_id' => 'price_yearly_test',
                'is_active' => true,
                'sort_order' => 1,
                'features' => ['Feature A', 'Feature B'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Package::class, [
            'product_id' => $this->product->id,
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'domain_limit' => 3,
        ]);
    });

    it('validates required fields', function () {
        Livewire::test(CreatePackage::class, [
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'name' => null,
                'slug' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'slug']);
    });

    it('auto-generates slug from name', function () {
        Livewire::test(CreatePackage::class, [
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'name' => 'Premium Plan',
            ])
            ->assertFormSet([
                'slug' => 'premium-plan',
            ]);
    });

    it('validates unique Stripe price IDs', function () {
        Package::factory()->create([
            'product_id' => $this->product->id,
            'stripe_monthly_price_id' => 'price_monthly_unique',
            'stripe_yearly_price_id' => 'price_yearly_unique',
        ]);

        Livewire::test(CreatePackage::class, [
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'name' => 'Duplicate Stripe Plan',
                'slug' => 'duplicate-stripe-plan',
                'stripe_monthly_price_id' => 'price_monthly_unique',
                'stripe_yearly_price_id' => 'price_yearly_unique',
            ])
            ->call('create')
            ->assertHasFormErrors(['stripe_monthly_price_id', 'stripe_yearly_price_id']);
    });
});

describe('Edit Package Page', function () {
    it('can retrieve package data', function () {
        $package = Package::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Pro Plan',
            'domain_limit' => 10,
            'monthly_price' => '29.99',
            'is_active' => true,
        ]);

        Livewire::test(EditPackage::class, [
            'record' => $package->getRouteKey(),
            'parentRecord' => $this->product,
        ])
            ->assertFormSet([
                'name' => 'Pro Plan',
                'domain_limit' => 10,
                'monthly_price' => '29.99',
                'is_active' => true,
            ]);
    });

    it('can update a package', function () {
        $package = Package::factory()->create([
            'product_id' => $this->product->id,
            'name' => 'Old Plan',
            'domain_limit' => 5,
        ]);

        Livewire::test(EditPackage::class, [
            'record' => $package->getRouteKey(),
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'name' => 'Updated Plan',
                'domain_limit' => 15,
                'monthly_price' => 49.99,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $package->refresh();

        expect($package->name)->toBe('Updated Plan')
            ->and($package->domain_limit)->toBe(15);
    });

    it('can delete a package', function () {
        $package = Package::factory()->create([
            'product_id' => $this->product->id,
        ]);

        Livewire::test(EditPackage::class, [
            'record' => $package->getRouteKey(),
            'parentRecord' => $this->product,
        ])
            ->callAction('delete');

        $this->assertModelMissing($package);
    });

    it('can set unlimited domains by leaving domain_limit empty', function () {
        $package = Package::factory()->create([
            'product_id' => $this->product->id,
            'domain_limit' => 5,
        ]);

        Livewire::test(EditPackage::class, [
            'record' => $package->getRouteKey(),
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'domain_limit' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $package->refresh();

        expect($package->domain_limit)->toBeNull();
    });

    it('validates unique Stripe price IDs on edit', function () {
        $existing = Package::factory()->create([
            'product_id' => $this->product->id,
            'stripe_monthly_price_id' => 'price_monthly_existing',
            'stripe_yearly_price_id' => 'price_yearly_existing',
        ]);

        $package = Package::factory()->create([
            'product_id' => $this->product->id,
        ]);

        Livewire::test(EditPackage::class, [
            'record' => $package->getRouteKey(),
            'parentRecord' => $this->product,
        ])
            ->fillForm([
                'stripe_monthly_price_id' => $existing->stripe_monthly_price_id,
                'stripe_yearly_price_id' => $existing->stripe_yearly_price_id,
            ])
            ->call('save')
            ->assertHasFormErrors(['stripe_monthly_price_id', 'stripe_yearly_price_id']);
    });
});

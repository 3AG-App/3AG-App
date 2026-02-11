<?php

use App\Enums\ProductType;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\PackagesRelationManager;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

beforeEach(function () {
    $adminEmail = 'admin@test.com';
    config(['admin.emails' => [$adminEmail]]);

    $this->admin = User::factory()->create(['email' => $adminEmail]);
    $this->actingAs($this->admin);
});

describe('List Products Page', function () {
    it('can render the index page', function () {
        $this->get(ProductResource::getUrl('index'))
            ->assertSuccessful();
    });

    it('can list products', function () {
        $products = Product::factory()->count(3)->create();

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords($products);
    });

    it('can search products by name', function () {
        $product = Product::factory()->create(['name' => 'Unique Plugin Pro']);
        $otherProduct = Product::factory()->create(['name' => 'Other Theme Basic']);

        Livewire::test(ListProducts::class)
            ->searchTable('Unique Plugin')
            ->assertCanSeeTableRecords([$product])
            ->assertCanNotSeeTableRecords([$otherProduct]);
    });

    it('can filter products by type', function () {
        $plugin = Product::factory()->plugin()->create();
        $theme = Product::factory()->theme()->create();

        Livewire::test(ListProducts::class)
            ->filterTable('type', ProductType::Plugin->value)
            ->assertCanSeeTableRecords([$plugin])
            ->assertCanNotSeeTableRecords([$theme]);
    });

    it('can filter products by active status', function () {
        $activeProduct = Product::factory()->create(['is_active' => true]);
        $inactiveProduct = Product::factory()->inactive()->create();

        Livewire::test(ListProducts::class)
            ->filterTable('is_active', true)
            ->assertCanSeeTableRecords([$activeProduct])
            ->assertCanNotSeeTableRecords([$inactiveProduct]);
    });

    it('can sort products by name', function () {
        $productA = Product::factory()->create(['name' => 'Alpha Product']);
        $productZ = Product::factory()->create(['name' => 'Zeta Product']);

        Livewire::test(ListProducts::class)
            ->sortTable('name')
            ->assertCanSeeTableRecords([$productA, $productZ], inOrder: true);
    });

    it('displays packages count', function () {
        $product = Product::factory()->create();
        Package::factory()->count(3)->create(['product_id' => $product->id]);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$product]);
    });
});

describe('Create Product Page', function () {
    it('can render the create page', function () {
        $this->get(ProductResource::getUrl('create'))
            ->assertSuccessful();
    });

    it('can create a product', function () {
        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'New Plugin',
                'slug' => 'new-plugin',
                'type' => ProductType::Plugin->value,
                'description' => 'A great new plugin',
                'is_active' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Product::class, [
            'name' => 'New Plugin',
            'slug' => 'new-plugin',
            'type' => ProductType::Plugin->value,
        ]);
    });

    it('validates required fields', function () {
        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => null,
                'slug' => null,
                'type' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'slug', 'type']);
    });

    it('validates slug uniqueness', function () {
        Product::factory()->create(['slug' => 'existing-slug']);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'New Product',
                'slug' => 'existing-slug',
                'type' => ProductType::Plugin->value,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    });

    it('auto-generates slug from name', function () {
        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'My Great Plugin',
            ])
            ->assertFormSet([
                'slug' => 'my-great-plugin',
            ]);
    });
});

describe('View Product Page', function () {
    it('can render the view page', function () {
        $product = Product::factory()->create();

        $this->get(ProductResource::getUrl('view', ['record' => $product]))
            ->assertSuccessful();
    });

    it('displays product information', function () {
        $product = Product::factory()->create([
            'name' => 'Test Plugin Pro',
            'slug' => 'test-plugin-pro',
        ]);

        Livewire::test(ViewProduct::class, ['record' => $product->getRouteKey()])
            ->assertSee('Test Plugin Pro')
            ->assertSee('test-plugin-pro');
    });
});

describe('Edit Product Page', function () {
    it('can render the edit page', function () {
        $product = Product::factory()->create();

        $this->get(ProductResource::getUrl('edit', ['record' => $product]))
            ->assertSuccessful();
    });

    it('can retrieve product data', function () {
        $product = Product::factory()->create([
            'name' => 'Existing Plugin',
            'type' => ProductType::Plugin,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertFormSet([
                'name' => 'Existing Plugin',
                'type' => ProductType::Plugin,
                'is_active' => true,
                'sort_order' => 5,
            ]);
    });

    it('can update a product', function () {
        $product = Product::factory()->create(['name' => 'Old Name']);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
                'type' => ProductType::Theme->value,
                'is_active' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $product->refresh();

        expect($product->name)->toBe('Updated Name')
            ->and($product->type)->toBe(ProductType::Theme)
            ->and($product->is_active)->toBeFalse();
    });

    it('can delete a product', function () {
        $product = Product::factory()->create();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->callAction('delete');

        $this->assertModelMissing($product);
    });

    it('shows packages relation manager records', function () {
        $product = Product::factory()->create();
        $packages = Package::factory()->count(2)->create(['product_id' => $product->id]);

        Livewire::test(PackagesRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->assertCanSeeTableRecords($packages);
    });
});

describe('Product Screenshots', function () {
    it('registers the screenshots media collection', function () {
        $product = Product::factory()->create();

        $product->addMedia(UploadedFile::fake()->image('screenshot.jpg'))
            ->toMediaCollection('screenshots');

        expect($product->getMedia('screenshots'))->toHaveCount(1)
            ->and($product->getMedia('screenshots')->first()->collection_name)->toBe('screenshots');
    });

    it('allows multiple screenshots', function () {
        $product = Product::factory()->create();

        $product->addMedia(UploadedFile::fake()->image('screenshot1.jpg'))
            ->toMediaCollection('screenshots');
        $product->addMedia(UploadedFile::fake()->image('screenshot2.jpg'))
            ->toMediaCollection('screenshots');
        $product->addMedia(UploadedFile::fake()->image('screenshot3.jpg'))
            ->toMediaCollection('screenshots');

        expect($product->getScreenshots())->toHaveCount(3);
    });

    it('uses the product-screenshots disk', function () {
        $product = Product::factory()->create();

        $product->addMedia(UploadedFile::fake()->image('screenshot.jpg'))
            ->toMediaCollection('screenshots');

        expect($product->getMedia('screenshots')->first()->disk)->toBe('product-screenshots');
    });

    it('displays screenshot upload field in the create form', function () {
        Livewire::test(CreateProduct::class)
            ->assertFormFieldExists('screenshots');
    });

    it('displays screenshot upload field in the edit form', function () {
        $product = Product::factory()->create();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertFormFieldExists('screenshots');
    });

    it('preserves the original screenshot filename', function () {
        $file = UploadedFile::fake()->image('original-screenshot.png');

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Screenshot Product',
                'slug' => 'screenshot-product',
                'type' => ProductType::Plugin->value,
                'screenshots' => [$file],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::where('slug', 'screenshot-product')->firstOrFail();
        $media = $product->getMedia('screenshots')->first();

        expect($media)->not->toBeNull()
            ->and($media->file_name)->toBe('original-screenshot.png');
    });
});

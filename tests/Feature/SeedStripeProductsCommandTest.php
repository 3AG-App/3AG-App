<?php

use App\Console\Commands\SeedStripeProducts;
use App\Models\Package;
use App\Models\Product;

it('creates two products and two packages each with stripe price ids', function () {
    $fakeStripe = new class
    {
        public object $products;

        public object $prices;

        public int $productSequence = 0;

        public int $priceSequence = 0;

        public function __construct()
        {
            $this->products = new class($this)
            {
                public function __construct(private object $parent) {}

                public function create(array $payload): object
                {
                    $this->parent->productSequence++;

                    return (object) [
                        'id' => 'prod_test_'.$this->parent->productSequence,
                        'name' => $payload['name'],
                    ];
                }
            };

            $this->prices = new class($this)
            {
                public function __construct(private object $parent) {}

                public function create(array $payload): object
                {
                    $this->parent->priceSequence++;

                    return (object) [
                        'id' => 'price_test_'.$this->parent->priceSequence,
                        'unit_amount' => $payload['unit_amount'],
                        'product' => $payload['product'],
                    ];
                }
            };
        }
    };

    app()->bind(SeedStripeProducts::class, function () use ($fakeStripe) {
        return new class($fakeStripe) extends SeedStripeProducts
        {
            public function __construct(private object $fakeStripe)
            {
                parent::__construct();
            }

            protected function stripe(): object
            {
                return $this->fakeStripe;
            }
        };
    });

    $this->artisan('products:seed-stripe-catalog')
        ->assertSuccessful();

    expect(Product::count())->toBe(2)
        ->and(Package::count())->toBe(4)
        ->and(Package::whereNull('stripe_monthly_price_id')->count())->toBe(0)
        ->and(Package::whereNull('stripe_yearly_price_id')->count())->toBe(0)
        ->and(Package::query()->pluck('stripe_monthly_price_id')->every(fn (string $id): bool => str_starts_with($id, 'price_test_')))->toBeTrue()
        ->and(Package::query()->pluck('stripe_yearly_price_id')->every(fn (string $id): bool => str_starts_with($id, 'price_test_')))->toBeTrue();
});

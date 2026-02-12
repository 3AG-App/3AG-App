<?php

use App\Http\Controllers\ProductController;
use App\Http\Requests\SubscribeRequest;
use App\Models\Package;
use App\Models\Product;

it('applies a 10 day trial when creating a checkout subscription', function () {
    $fakeCheckoutBuilder = new class
    {
        public ?int $trialDays = null;

        /**
         * @var array<string, string>
         */
        public array $checkoutPayload = [];

        /**
         * @var array<string, mixed>
         */
        public array $metadata = [];

        public function withMetadata(array $metadata): self
        {
            $this->metadata = $metadata;

            return $this;
        }

        public function trialDays(int $days): self
        {
            $this->trialDays = $days;

            return $this;
        }

        /**
         * @param  array<string, string>  $payload
         */
        public function checkout(array $payload): object
        {
            $this->checkoutPayload = $payload;

            return (object) [
                'url' => 'https://checkout.stripe.test/session',
                'payload' => $payload,
            ];
        }
    };

    $fakeUser = new class($fakeCheckoutBuilder)
    {
        public function __construct(public object $builder) {}

        public function subscriptions(): object
        {
            return new class
            {
                public function whereIn(string $column, array $values): self
                {
                    return $this;
                }

                public function get(): \Illuminate\Support\Collection
                {
                    return collect();
                }
            };
        }

        public function newSubscription(string $name, string $priceId): object
        {
            return $this->builder;
        }
    };

    $product = new Product([
        'id' => 1,
        'name' => 'WooCommerce Booster',
        'slug' => 'woocommerce-booster',
        'is_active' => true,
    ]);

    $package = new Package([
        'id' => 11,
        'product_id' => 1,
        'name' => 'Starter',
        'slug' => 'starter',
        'is_active' => true,
        'stripe_monthly_price_id' => 'price_monthly_test',
        'stripe_yearly_price_id' => 'price_yearly_test',
    ]);

    $product->setRelation('activePackages', collect([$package]));
    $package->setRelation('product', $product);

    $request = mock(SubscribeRequest::class);
    $request->shouldReceive('validated')->once()->with('billing_interval')->andReturn('monthly');
    $request->shouldReceive('user')->once()->andReturn($fakeUser);

    $controller = new ProductController;

    $response = $controller->subscribe($request, $package);

    expect($fakeCheckoutBuilder->trialDays)->toBe(10)
        ->and($fakeCheckoutBuilder->checkoutPayload['payment_method_collection'])->toBe('if_required')
        ->and($response->headers->get('Location'))->toBe('https://checkout.stripe.test/session');
});

it('does not create a new checkout when a trial subscription already exists', function () {
    $fakeCheckoutBuilder = new class
    {
        public ?int $trialDays = null;

        public function withMetadata(array $metadata): self
        {
            return $this;
        }

        public function trialDays(int $days): self
        {
            $this->trialDays = $days;

            return $this;
        }

        public function checkout(array $payload): object
        {
            return (object) ['url' => 'https://checkout.stripe.test/session'];
        }
    };

    $existingTrialSubscription = new class extends \Laravel\Cashier\Subscription
    {
        public function valid(): bool
        {
            return true;
        }

        public function incomplete(): bool
        {
            return false;
        }
    };

    $fakeUser = new class($fakeCheckoutBuilder, $existingTrialSubscription)
    {
        public bool $newSubscriptionCalled = false;

        public function __construct(public object $builder, public object $subscription) {}

        public function subscriptions(): object
        {
            return new class($this->subscription)
            {
                public function __construct(public object $subscription) {}

                public function whereIn(string $column, array $values): self
                {
                    return $this;
                }

                public function get(): \Illuminate\Support\Collection
                {
                    return collect([$this->subscription]);
                }
            };
        }

        public function newSubscription(string $name, string $priceId): object
        {
            $this->newSubscriptionCalled = true;

            return $this->builder;
        }
    };

    $product = new Product([
        'id' => 1,
        'name' => 'WooCommerce Booster',
        'slug' => 'woocommerce-booster',
        'is_active' => true,
    ]);

    $package = new Package([
        'id' => 11,
        'product_id' => 1,
        'name' => 'Starter',
        'slug' => 'starter',
        'is_active' => true,
        'stripe_monthly_price_id' => 'price_monthly_test',
        'stripe_yearly_price_id' => 'price_yearly_test',
    ]);

    $product->setRelation('activePackages', collect([$package]));
    $package->setRelation('product', $product);

    $request = mock(SubscribeRequest::class);
    $request->shouldReceive('validated')->once()->with('billing_interval')->andReturn('monthly');
    $request->shouldReceive('user')->once()->andReturn($fakeUser);

    $controller = new ProductController;
    $response = $controller->subscribe($request, $package);

    expect($fakeUser->newSubscriptionCalled)->toBeFalse()
        ->and($response->getTargetUrl())->toBe(route('dashboard.subscriptions.index'));
});

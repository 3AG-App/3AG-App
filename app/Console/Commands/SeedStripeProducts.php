<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;

class SeedStripeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:seed-stripe-catalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create 2 products with 2 packages each and real Stripe prices';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating products, packages, and Stripe prices...');

        DB::beginTransaction();

        try {
            $catalog = $this->catalogDefinition();

            foreach ($catalog as $productIndex => $productData) {
                $product = Product::query()->updateOrCreate(
                    ['slug' => $productData['slug']],
                    [
                        'name' => $productData['name'],
                        'short_description' => $productData['short_description'],
                        'long_description' => $productData['long_description'],
                        'type' => $productData['type'],
                        'is_active' => true,
                        'sort_order' => $productIndex + 1,
                    ]
                );

                foreach ($productData['packages'] as $packageIndex => $packageData) {
                    $stripeProduct = $this->stripe()->products->create([
                        'name' => "{$product->name} - {$packageData['name']}",
                        'description' => $packageData['description'],
                    ]);

                    $monthlyPrice = $this->stripe()->prices->create([
                        'currency' => 'usd',
                        'unit_amount' => $packageData['monthly_price_cents'],
                        'recurring' => ['interval' => 'month'],
                        'product' => $stripeProduct->id,
                    ]);

                    $yearlyPrice = $this->stripe()->prices->create([
                        'currency' => 'usd',
                        'unit_amount' => $packageData['yearly_price_cents'],
                        'recurring' => ['interval' => 'year'],
                        'product' => $stripeProduct->id,
                    ]);

                    Package::query()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'slug' => $packageData['slug'],
                        ],
                        [
                            'name' => $packageData['name'],
                            'description' => $packageData['description'],
                            'domain_limit' => $packageData['domain_limit'],
                            'stripe_monthly_price_id' => $monthlyPrice->id,
                            'stripe_yearly_price_id' => $yearlyPrice->id,
                            'monthly_price' => $packageData['monthly_price_cents'] / 100,
                            'yearly_price' => $packageData['yearly_price_cents'] / 100,
                            'is_active' => true,
                            'sort_order' => $packageIndex + 1,
                            'features' => $packageData['features'],
                        ]
                    );

                    $this->line("Created Stripe prices for {$product->slug}/{$packageData['slug']}");
                }
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            $this->error("Failed: {$throwable->getMessage()}");

            return self::FAILURE;
        }

        $this->info('Catalog seeded successfully.');

        return self::SUCCESS;
    }

    protected function stripe(): object
    {
        return Cashier::stripe();
    }

    /**
     * @return array<int, array{
     *     name: string,
     *     slug: string,
     *     short_description: string,
     *     long_description: string,
     *     type: ProductType,
     *     packages: array<int, array{
     *         name: string,
     *         slug: string,
     *         description: string,
     *         domain_limit: int|null,
     *         monthly_price_cents: int,
     *         yearly_price_cents: int,
     *         features: array<int, string>
     *     }>
     * }>
     */
    protected function catalogDefinition(): array
    {
        return [
            [
                'name' => 'WooCommerce Booster',
                'slug' => 'woocommerce-booster',
                'short_description' => 'Supercharge WooCommerce performance and checkout.',
                'long_description' => '<p>Conversion-focused plugin with performance and checkout enhancements.</p>',
                'type' => ProductType::Plugin,
                'packages' => [
                    [
                        'name' => 'Starter',
                        'slug' => 'starter',
                        'description' => 'Best for single-site stores.',
                        'domain_limit' => 1,
                        'monthly_price_cents' => 1900,
                        'yearly_price_cents' => 14900,
                        'features' => ['1 site license', 'Email support', 'Core premium features'],
                    ],
                    [
                        'name' => 'Professional',
                        'slug' => 'professional',
                        'description' => 'Best for growing stores.',
                        'domain_limit' => 5,
                        'monthly_price_cents' => 4900,
                        'yearly_price_cents' => 39900,
                        'features' => ['5 site licenses', 'Priority support', 'All premium features'],
                    ],
                ],
            ],
            [
                'name' => 'Developer Theme',
                'slug' => 'developer-theme',
                'short_description' => 'Fast and customizable theme for modern websites.',
                'long_description' => '<p>Developer-first theme with flexible layouts and performance in mind.</p>',
                'type' => ProductType::Theme,
                'packages' => [
                    [
                        'name' => 'Personal',
                        'slug' => 'personal',
                        'description' => 'For individuals launching one site.',
                        'domain_limit' => 1,
                        'monthly_price_cents' => 900,
                        'yearly_price_cents' => 7900,
                        'features' => ['1 site license', 'Theme updates', 'Email support'],
                    ],
                    [
                        'name' => 'Business',
                        'slug' => 'business',
                        'description' => 'For teams managing multiple sites.',
                        'domain_limit' => 3,
                        'monthly_price_cents' => 2900,
                        'yearly_price_cents' => 24900,
                        'features' => ['3 site licenses', 'Priority updates', 'Priority support'],
                    ],
                ],
            ],
        ];
    }
}

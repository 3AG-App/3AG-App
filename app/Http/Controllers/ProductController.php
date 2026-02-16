<?php

namespace App\Http\Controllers;

use App\Enums\LicenseStatus;
use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\SwapSubscriptionRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\License;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Subscription as CashierSubscription;
use Stripe\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ProductController extends Controller
{
    private const SUBSCRIPTION_TRIAL_DAYS = 10;

    public function index(): Response
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate(12)
            ->through(fn (Product $product) => (new ProductResource($product))->resolve());

        return Inertia::render('products/index', [
            'products' => $products,
        ]);
    }

    public function show(Product $product): Response
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load([
            'activePackages' => fn ($query) => $query->orderBy('sort_order'),
            'latestRelease.media',
        ]);

        // Get current user's subscription for this product (if any)
        $currentSubscription = null;
        $latestDownload = null;
        $user = Auth::user();

        if ($user) {
            // Build list of possible subscription names for this product
            $subscriptionNames = $product->activePackages->map(fn ($pkg) => $pkg->getSubscriptionName())->toArray();

            $subscription = $this->findUsableProductSubscription($user, $subscriptionNames);

            if ($subscription) {
                // Find the package by stripe_price
                $subscribedPackage = Package::findByStripePrice($subscription->stripe_price)
                    ?? $product->activePackages->first();

                $currentSubscription = [
                    'id' => $subscription->id,
                    'package_id' => $subscribedPackage->id,
                    'package_slug' => $subscribedPackage->slug,
                    'package_name' => $subscribedPackage->name,
                    'stripe_price' => $subscription->stripe_price,
                    'is_yearly' => $subscribedPackage->isYearlyPrice($subscription->stripe_price),
                    'ends_at' => $subscription->ends_at?->toISOString(),
                    'on_grace_period' => $subscription->onGracePeriod(),
                    'requires_payment' => $subscription->hasIncompletePayment(),
                ];
            }

            $validLicense = License::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('status', LicenseStatus::Active->value)
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->latest()
                ->first();

            $latestRelease = $product->latestRelease;
            $latestReleaseZip = $latestRelease?->getZipFile();

            if ($validLicense !== null && $latestReleaseZip !== null) {
                $latestDownload = [
                    'version' => $latestRelease->version,
                    'url' => route('dashboard.licenses.download-latest-release', ['license' => $validLicense]),
                ];
            }
        }

        return Inertia::render('products/show', [
            'product' => (new ProductDetailResource($product))->resolve(),
            'currentSubscription' => $currentSubscription,
            'latestDownload' => $latestDownload,
        ]);
    }

    public function subscribe(SubscribeRequest $request, Package $package): SymfonyResponse
    {
        // Ensure the package belongs to an active product
        if (! $package->product || ! $package->product->is_active || ! $package->is_active) {
            abort(404);
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $billingInterval = $request->validated('billing_interval');

        // Get the appropriate Stripe price ID based on billing interval
        $priceId = $billingInterval === 'yearly'
            ? $package->stripe_yearly_price_id
            : $package->stripe_monthly_price_id;

        if (! $priceId) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.product.pricing_not_available.message'),
                'description' => __('toast.product.pricing_not_available.description'),
            ]);

            return back();
        }

        // Create a unique subscription name using product and package slug to avoid collisions
        $subscriptionName = $package->getSubscriptionName();

        // Check if user already has an active subscription for this product (single query)
        $subscriptionNames = $package->product->activePackages->map(fn ($pkg) => $pkg->getSubscriptionName())->toArray();
        $hasExistingSubscription = $this->findUsableProductSubscription($user, $subscriptionNames) !== null;

        if ($hasExistingSubscription) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => __('toast.product.already_subscribed.message'),
                'description' => __('toast.product.already_subscribed.description'),
            ]);

            return redirect()->route('dashboard.subscriptions.index');
        }

        // Create Stripe Checkout session with metadata on the subscription
        try {
            $checkout = $user->newSubscription($subscriptionName, $priceId)
                ->withMetadata([
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'product_id' => $package->product_id,
                    'product_name' => $package->product->name,
                ])
                ->trialDays(self::SUBSCRIPTION_TRIAL_DAYS)
                ->checkout([
                    'success_url' => route('dashboard.subscriptions.index').'?checkout=success',
                    'cancel_url' => route('products.show', $package->product->slug).'?checkout=cancelled',
                    'payment_method_collection' => 'if_required',
                ]);
        } catch (InvalidRequestException $e) {
            Log::error('Stripe checkout failed', [
                'package_id' => $package->id,
                'price_id' => $priceId,
                'error' => $e->getMessage(),
            ]);

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.product.unable_to_process_subscription.message'),
                'description' => __('toast.product.unable_to_process_subscription.description'),
            ]);

            return back();
        }

        // Use Inertia::location() for external redirect to Stripe
        return Inertia::location($checkout->url);
    }

    public function swap(SwapSubscriptionRequest $request, Package $package): RedirectResponse
    {
        // Ensure the package belongs to an active product
        if (! $package->product || ! $package->product->is_active || ! $package->is_active) {
            abort(404);
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $billingInterval = $request->validated('billing_interval');

        // Get the appropriate Stripe price ID based on billing interval
        $newPriceId = $billingInterval === 'yearly'
            ? $package->stripe_yearly_price_id
            : $package->stripe_monthly_price_id;

        if (! $newPriceId) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.product.pricing_not_available.message'),
                'description' => __('toast.product.pricing_not_available.description'),
            ]);

            return back();
        }

        // Find the user's current subscription for this product (single query)
        $product = $package->product;
        $subscriptionNames = $product->activePackages->map(fn ($pkg) => $pkg->getSubscriptionName())->toArray();

        $currentSubscription = $this->findUsableProductSubscription($user, $subscriptionNames);

        if (! $currentSubscription) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.product.no_active_subscription.message'),
                'description' => __('toast.product.no_active_subscription.description'),
            ]);

            return back();
        }

        // Check if they're trying to swap to the same package and same billing interval
        if ($currentSubscription->stripe_price === $newPriceId) {
            Inertia::flash('toast', [
                'type' => 'info',
                'message' => __('toast.product.no_change_needed.message'),
                'description' => __('toast.product.no_change_needed.description'),
            ]);

            return back();
        }

        // Find the current license (or prepare to create one)
        $currentLicense = License::where('subscription_id', $currentSubscription->id)->first();

        // Check if downgrading would exceed new domain limit
        if ($package->domain_limit !== null && $currentLicense) {
            $activeActivationsCount = $currentLicense->activeActivations()->count();

            if ($activeActivationsCount > $package->domain_limit) {
                Inertia::flash('toast', [
                    'type' => 'error',
                    'message' => __('toast.product.cannot_downgrade.message'),
                    'description' => __('toast.product.cannot_downgrade.description', [
                        'active' => $activeActivationsCount,
                        'limit' => $package->domain_limit,
                    ]),
                ]);

                return back();
            }
        }

        try {
            DB::transaction(function () use ($currentSubscription, $newPriceId, $package, $product, $user, $currentLicense) {
                // Swap the subscription to the new price
                $currentSubscription->swap($newPriceId);

                // Update subscription type to new package name
                $newSubscriptionName = $package->getSubscriptionName();
                $currentSubscription->update(['type' => $newSubscriptionName]);

                // Update or create the license with new package info
                if ($currentLicense) {
                    $currentLicense->update([
                        'package_id' => $package->id,
                        'domain_limit' => $package->domain_limit,
                    ]);
                } else {
                    // License doesn't exist (webhook may have failed) - create it now
                    License::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'package_id' => $package->id,
                        'subscription_id' => $currentSubscription->id,
                        'domain_limit' => $package->domain_limit,
                        'status' => LicenseStatus::Active,
                    ]);

                    Log::warning('ProductController::swap: Created missing license during swap', [
                        'user_id' => $user->id,
                        'subscription_id' => $currentSubscription->id,
                        'package_id' => $package->id,
                    ]);
                }
            });

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('toast.product.subscription_updated.message'),
                'description' => __('toast.product.subscription_updated.description', ['plan' => $package->name]),
            ]);
        } catch (IncompletePayment $e) {
            // Handle SCA/3D Secure - redirect to Cashier's payment confirmation page
            Log::warning('Subscription swap requires payment confirmation', [
                'subscription_id' => $currentSubscription->id,
                'user_id' => $user->id,
                'payment_id' => $e->payment->id,
            ]);

            return redirect()->route('cashier.payment', [
                'id' => $e->payment->id,
                'redirect' => route('dashboard.subscriptions.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to swap subscription', [
                'subscription_id' => $currentSubscription->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.product.subscription_update_failed.message'),
                'description' => __('toast.product.subscription_update_failed.description'),
            ]);
        }

        return back();
    }

    /**
     * @param  array<int, string>  $subscriptionNames
     */
    private function findUsableProductSubscription($user, array $subscriptionNames): ?CashierSubscription
    {
        return $user->subscriptions()
            ->whereIn('type', $subscriptionNames)
            ->get()
            ->first(fn (CashierSubscription $subscription): bool => $this->isUsableProductSubscription($subscription));
    }

    private function isUsableProductSubscription(CashierSubscription $subscription): bool
    {
        return $subscription->valid() || $subscription->incomplete();
    }
}

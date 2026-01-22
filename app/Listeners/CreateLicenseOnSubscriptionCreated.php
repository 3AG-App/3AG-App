<?php

namespace App\Listeners;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription;

class CreateLicenseOnSubscriptionCreated implements ShouldBeUnique, ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * The number of seconds the unique lock should be maintained.
     */
    public int $uniqueFor = 300;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60, 120];

    /**
     * The unique ID of the job (Stripe subscription ID).
     */
    public function uniqueId(): string
    {
        return 'license_creation_'.($this->event->payload['data']['object']['id'] ?? 'unknown');
    }

    /**
     * Store the event for uniqueId access.
     */
    public function __construct(
        public ?WebhookReceived $event = null
    ) {}

    public function handle(WebhookReceived $event): void
    {
        $this->event = $event;

        if ($event->payload['type'] !== 'customer.subscription.created') {
            return;
        }

        $stripeSubscription = $event->payload['data']['object'];
        $stripeSubscriptionId = $stripeSubscription['id'];

        // Find the local subscription record
        $subscription = Subscription::where('stripe_id', $stripeSubscriptionId)->first();

        if (! $subscription) {
            Log::warning('CreateLicenseOnSubscriptionCreated: Subscription not found, will retry', [
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);

            // Throw exception to trigger retry - subscription record may not exist yet
            throw new \RuntimeException("Subscription {$stripeSubscriptionId} not found yet, will retry");
        }

        // Get stripe_price from subscription items
        $stripePriceId = $stripeSubscription['items']['data'][0]['price']['id'] ?? null;

        // Primary: Find package by stripe_price
        $package = $stripePriceId ? Package::findByStripePrice($stripePriceId) : null;

        // Fallback: Use metadata from checkout session
        if (! $package) {
            $metadata = $stripeSubscription['metadata'] ?? [];
            $packageId = $metadata['package_id'] ?? null;

            if ($packageId) {
                $package = Package::find($packageId);
            }
        }

        // Fallback: Try to find package by subscription name (slug)
        if (! $package) {
            $package = Package::where('slug', $subscription->type)->first();
        }

        if (! $package) {
            Log::warning('CreateLicenseOnSubscriptionCreated: Package not found', [
                'subscription_id' => $subscription->id,
                'subscription_type' => $subscription->type,
                'stripe_price_id' => $stripePriceId,
            ]);

            return;
        }

        // Find the user
        $user = User::find($subscription->user_id);

        if (! $user) {
            Log::warning('CreateLicenseOnSubscriptionCreated: User not found', [
                'user_id' => $subscription->user_id,
            ]);

            return;
        }

        // Get subscription expiry from current_period_end (subscription level, not item level)
        $currentPeriodEnd = $stripeSubscription['current_period_end']
            ?? $stripeSubscription['items']['data'][0]['current_period_end']
            ?? null;
        $expiresAt = $currentPeriodEnd ? Carbon::createFromTimestamp($currentPeriodEnd) : null;

        // Use firstOrCreate to prevent race conditions from duplicate webhooks
        $license = License::firstOrCreate(
            ['subscription_id' => $subscription->id],
            [
                'user_id' => $user->id,
                'product_id' => $package->product_id,
                'package_id' => $package->id,
                'domain_limit' => $package->domain_limit,
                'status' => LicenseStatus::Active,
                'expires_at' => $expiresAt,
            ]
        );

        if ($license->wasRecentlyCreated) {
            Log::info('CreateLicenseOnSubscriptionCreated: License created', [
                'license_id' => $license->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'package_id' => $package->id,
            ]);
        } else {
            Log::info('CreateLicenseOnSubscriptionCreated: License already exists, skipped creation', [
                'license_id' => $license->id,
                'subscription_id' => $subscription->id,
            ]);
        }
    }
}

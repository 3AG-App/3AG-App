<?php

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Subscription;
use Stripe\StripeClient;

beforeEach(function () {
    Notification::fake();
});

it('sends subscription renewal reminders to users with active subscriptions', function () {
    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
        'quantity' => 1,
    ]);

    $this->artisan('notifications:send-subscription-reminders', ['--days' => 3])
        ->assertSuccessful();
});

it('skips users with notifications disabled', function () {
    $user = User::factory()->create();
    UserPreference::factory()->create([
        'user_id' => $user->id,
        'notifications_enabled' => false,
    ]);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
        'quantity' => 1,
    ]);

    $this->artisan('notifications:send-subscription-reminders', ['--days' => 3])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips users with subscription reminders disabled', function () {
    $user = User::factory()->create();
    UserPreference::factory()->create([
        'user_id' => $user->id,
        'notifications_enabled' => true,
        'subscription_reminders' => false,
    ]);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
        'quantity' => 1,
    ]);

    $this->artisan('notifications:send-subscription-reminders', ['--days' => 3])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('ignores cancelled subscriptions', function () {
    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'canceled',
        'stripe_price' => 'price_test',
        'quantity' => 1,
        'ends_at' => now()->addDays(5),
    ]);

    $this->artisan('notifications:send-subscription-reminders', ['--days' => 3])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('includes trialing subscriptions in reminder candidates', function () {
    $user = User::factory()->create();

    $activeSubscription = Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test_active',
        'quantity' => 1,
    ]);

    $trialingSubscription = Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'trialing',
        'stripe_price' => 'price_test_trial',
        'quantity' => 1,
        'trial_ends_at' => now()->addDays(7),
    ]);

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'canceled',
        'stripe_price' => 'price_test_canceled',
        'quantity' => 1,
        'ends_at' => now()->addDays(1),
    ]);

    $candidateIds = Subscription::query()
        ->whereNull('ends_at')
        ->whereNotNull('stripe_status')
        ->active()
        ->pluck('id');

    expect($candidateIds)
        ->toContain($activeSubscription->id)
        ->toContain($trialingSubscription->id)
        ->toHaveCount(2);
});

it('skips subscriptions with missing stripe period end', function () {
    $user = User::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->uuid(),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
        'quantity' => 1,
    ]);

    app()->instance(StripeClient::class, new class
    {
        public object $subscriptions;

        public function __construct()
        {
            $this->subscriptions = new class
            {
                public function retrieve(string $stripeId, array $options = []): object
                {
                    return (object) ['current_period_end' => null];
                }
            };
        }
    });

    $this->artisan('notifications:send-subscription-reminders', ['--days' => 3])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

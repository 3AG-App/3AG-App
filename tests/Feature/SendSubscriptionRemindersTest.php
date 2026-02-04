<?php

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Notification;
use Laravel\Cashier\Subscription;

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

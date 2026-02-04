<?php

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\User;
use App\Models\UserPreference;
use App\Notifications\LicenseExpiryAlert;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('sends license expiry alerts for licenses expiring within the specified days', function () {
    $user = User::factory()->create();
    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->addDays(5),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertSentTo($user, LicenseExpiryAlert::class);
});

it('does not send alerts for licenses expiring after the threshold', function () {
    $user = User::factory()->create();
    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->addDays(10),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not send alerts for already expired licenses', function () {
    $user = User::factory()->create();
    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->subDays(1),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips users with notifications disabled', function () {
    $user = User::factory()->create();
    UserPreference::factory()->create([
        'user_id' => $user->id,
        'notifications_enabled' => false,
    ]);

    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->addDays(3),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips users with license expiry alerts disabled', function () {
    $user = User::factory()->create();
    UserPreference::factory()->create([
        'user_id' => $user->id,
        'notifications_enabled' => true,
        'license_expiry_alerts' => false,
    ]);

    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->addDays(3),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('only sends alerts for active licenses', function () {
    $user = User::factory()->create();

    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Suspended,
        'expires_at' => now()->addDays(3),
    ]);

    License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Cancelled,
        'expires_at' => now()->addDays(3),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('sends alert with correct days until expiry', function () {
    $user = User::factory()->create();
    $license = License::factory()->create([
        'user_id' => $user->id,
        'status' => LicenseStatus::Active,
        'expires_at' => now()->addDays(3)->startOfDay(),
    ]);

    $this->artisan('notifications:send-license-expiry-alerts', ['--days' => 7])
        ->assertSuccessful();

    Notification::assertSentTo($user, LicenseExpiryAlert::class, function ($notification) use ($license) {
        return $notification->license->id === $license->id
            && $notification->daysUntilExpiry >= 2
            && $notification->daysUntilExpiry <= 3;
    });
});

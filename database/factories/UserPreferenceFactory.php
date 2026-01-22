<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'notifications_enabled' => true,
            'subscription_reminders' => true,
            'license_expiry_alerts' => true,
            'timezone' => fake()->optional()->timezone(),
        ];
    }

    public function withNotifications(bool $enabled = true): static
    {
        return $this->state(fn (array $attributes) => [
            'notifications_enabled' => $enabled,
        ]);
    }

    public function withSubscriptionReminders(bool $enabled = true): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_reminders' => $enabled,
        ]);
    }

    public function withLicenseExpiryAlerts(bool $enabled = true): static
    {
        return $this->state(fn (array $attributes) => [
            'license_expiry_alerts' => $enabled,
        ]);
    }
}

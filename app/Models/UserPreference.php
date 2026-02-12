<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserPreferenceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notifications_enabled',
        'subscription_reminders',
        'license_expiry_alerts',
        'timezone',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'notifications_enabled' => 'boolean',
            'subscription_reminders' => 'boolean',
            'license_expiry_alerts' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

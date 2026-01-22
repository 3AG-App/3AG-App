<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();
        $preference = $user->getOrCreatePreference();

        return Inertia::render('dashboard/settings', [
            'user' => UserResource::make($user)->resolve(),
            'preference' => [
                'notifications_enabled' => $preference->notifications_enabled,
                'subscription_reminders' => $preference->subscription_reminders,
                'license_expiry_alerts' => $preference->license_expiry_alerts,
                'timezone' => $preference->timezone,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notifications_enabled' => ['sometimes', 'boolean'],
            'subscription_reminders' => ['sometimes', 'boolean'],
            'license_expiry_alerts' => ['sometimes', 'boolean'],
            'timezone' => ['sometimes', 'nullable', 'string', 'timezone'],
        ]);

        $user = $request->user();
        $preference = $user->getOrCreatePreference();
        $preference->update($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Settings saved',
            'description' => 'Your preferences have been updated.',
        ]);

        return back();
    }
}

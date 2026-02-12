<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateSettingsRequest;
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

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $preference = $user->getOrCreatePreference();
        $preference->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('toast.dashboard.settings_saved.message'),
            'description' => __('toast.dashboard.settings_saved.description'),
        ]);

        return back();
    }
}

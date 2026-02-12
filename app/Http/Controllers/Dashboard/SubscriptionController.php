<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $subscriptions = $user->subscriptions()->latest()->get();

        // Get Stripe billing portal URL
        $billingPortalUrl = null;
        if ($user->hasStripeId()) {
            try {
                $billingPortalUrl = $user->billingPortalUrl(route('dashboard.subscriptions.index'));
            } catch (\Exception $e) {
                Log::warning('Failed to get billing portal URL', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return Inertia::render('dashboard/subscriptions/index', [
            'subscriptions' => SubscriptionResource::collection($subscriptions)->resolve(),
            'billing_portal_url' => $billingPortalUrl,
        ]);
    }

    public function cancel(Request $request, int $subscriptionId): RedirectResponse
    {
        $user = $request->user();

        $subscription = $user->subscriptions()->findOrFail($subscriptionId);

        try {
            $subscription->cancel();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('toast.dashboard.subscription_cancelled.message'),
                'description' => __('toast.dashboard.subscription_cancelled.description'),
            ]);
        } catch (\Exception $e) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.dashboard.subscription_cancel_failed.message'),
                'description' => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function resume(Request $request, int $subscriptionId): RedirectResponse
    {
        $user = $request->user();

        $subscription = $user->subscriptions()->findOrFail($subscriptionId);

        try {
            $subscription->resume();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('toast.dashboard.subscription_resumed.message'),
                'description' => __('toast.dashboard.subscription_resumed.description'),
            ]);
        } catch (\Exception $e) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('toast.dashboard.subscription_resume_failed.message'),
                'description' => $e->getMessage(),
            ]);
        }

        return back();
    }
}

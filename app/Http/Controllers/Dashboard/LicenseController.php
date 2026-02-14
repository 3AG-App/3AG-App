<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseDetailResource;
use App\Http\Resources\LicenseResource;
use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LicenseController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $licenses = License::query()
            ->where('user_id', $user->id)
            ->with(['product.latestRelease.media', 'package'])
            ->withCount(['activations', 'activeActivations'])
            ->latest()
            ->get();

        return Inertia::render('dashboard/licenses/index', [
            'licenses' => LicenseResource::collection($licenses)->resolve(),
        ]);
    }

    public function show(Request $request, License $license): Response
    {
        $user = $request->user();

        // Ensure the license belongs to the user
        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $license->load(['product.latestRelease.media', 'package', 'activations']);

        return Inertia::render('dashboard/licenses/show', [
            'license' => LicenseDetailResource::make($license)->resolve(),
        ]);
    }

    public function deactivateAll(Request $request, License $license): RedirectResponse
    {
        $user = $request->user();

        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $count = $license->activeActivations()->count();

        $license->activeActivations()->update([
            'deactivated_at' => now(),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('toast.dashboard.domains_deactivated.message'),
            'description' => __('toast.dashboard.domains_deactivated.description', ['count' => $count]),
        ]);

        return back();
    }

    public function deactivateActivation(Request $request, License $license, LicenseActivation $activation): RedirectResponse
    {
        $user = $request->user();

        if ($license->user_id !== $user->id) {
            abort(403);
        }

        if ($activation->license_id !== $license->id) {
            abort(404);
        }

        $domain = $activation->domain;

        $activation->update([
            'deactivated_at' => now(),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('toast.dashboard.domain_deactivated.message'),
            'description' => __('toast.dashboard.domain_deactivated.description', ['domain' => $domain]),
        ]);

        return back();
    }

    public function downloadLatestRelease(Request $request, License $license): BinaryFileResponse
    {
        $user = $request->user();

        if ($license->user_id !== $user->id) {
            abort(403);
        }

        if (! $license->isActive()) {
            abort(403);
        }

        $license->loadMissing('product.latestRelease.media');

        $latestRelease = $license->product->latestRelease;
        $zipFile = $latestRelease?->getZipFile();

        if ($zipFile === null || ! is_file($zipFile->getPath())) {
            abort(404);
        }

        return response()->download(
            file: $zipFile->getPath(),
            name: $zipFile->file_name,
        );
    }
}

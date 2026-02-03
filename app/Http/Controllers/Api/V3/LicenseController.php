<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\V3\Concerns\NormalizesDomain;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\ActivateLicenseRequest;
use App\Http\Requests\Api\V3\DeactivateLicenseRequest;
use App\Http\Requests\Api\V3\ValidateLicenseRequest;
use App\Http\Resources\Api\V3\LicenseValidationResource;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LicenseController extends Controller
{
    use NormalizesDomain;

    public function validate(ValidateLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug')
        );

        if (! $license) {
            return response()->json(['message' => 'Invalid license key.'], 401);
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        $isActivated = $license->isActive() && $license->activations()
            ->where('domain', $domain)
            ->whereNull('deactivated_at')
            ->exists();

        if ($isActivated) {
            $license->activations()
                ->where('domain', $domain)
                ->whereNull('deactivated_at')
                ->update(['last_checked_at' => now()]);
        }

        $license->update(['last_validated_at' => now()]);

        return response()->json([
            'data' => (new LicenseValidationResource($license))->withActivated($isActivated),
        ]);
    }

    public function activate(ActivateLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug')
        );

        if (! $license) {
            return response()->json(['message' => 'Invalid license key.'], 401);
        }

        if (! $license->isActive()) {
            return response()->json(['message' => 'License is not active.'], 403);
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        $existingActivation = $license->activations()
            ->where('domain', $domain)
            ->first();

        if ($existingActivation) {
            if ($existingActivation->isActive()) {
                $existingActivation->updateLastChecked();

                return response()->json([
                    'data' => new LicenseValidationResource($license),
                ]);
            }

            $existingActivation->reactivate();

            return response()->json([
                'data' => new LicenseValidationResource(
                    $license->fresh(['product', 'package'])
                        ->loadCount(['activations as domains_used' => fn ($q) => $q->whereNull('deactivated_at')])
                ),
            ]);
        }

        // Use pessimistic locking to prevent race conditions when checking domain limits
        // and creating activations concurrently
        try {
            DB::transaction(function () use ($license, $domain, $request) {
                // Re-fetch the license with a lock to ensure accurate domain count
                $lockedLicense = License::query()
                    ->where('id', $license->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedLicense->canActivateMoreDomains()) {
                    throw new \App\Exceptions\DomainLimitReachedException($lockedLicense->domain_limit);
                }

                $lockedLicense->activations()->create([
                    'domain' => $domain,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_checked_at' => now(),
                ]);

                $lockedLicense->update(['last_validated_at' => now()]);
            });
        } catch (\App\Exceptions\DomainLimitReachedException $e) {
            return response()->json([
                'message' => "Domain limit reached. Maximum {$e->limit} domain(s) allowed.",
            ], 403);
        }

        return response()->json([
            'data' => new LicenseValidationResource(
                $license->fresh(['product', 'package'])
                    ->loadCount(['activations as domains_used' => fn ($q) => $q->whereNull('deactivated_at')])
            ),
        ], 201);
    }

    public function deactivate(DeactivateLicenseRequest $request): JsonResponse|Response
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug'),
            withRelations: false
        );

        if (! $license) {
            return response()->json(['message' => 'Invalid license key.'], 401);
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        $activation = $license->activations()
            ->where('domain', $domain)
            ->whereNull('deactivated_at')
            ->first();

        if (! $activation) {
            return response()->json(['message' => 'No active activation found for this domain.'], 404);
        }

        $activation->deactivate();

        return response()->noContent();
    }

    private function findLicense(string $licenseKey, string $productSlug, bool $withRelations = true): ?License
    {
        $query = License::query()
            ->where('license_key', $licenseKey)
            ->whereHas('product', fn ($q) => $q->where('slug', $productSlug)->where('is_active', true));

        if ($withRelations) {
            $query->with(['product', 'package'])
                ->withCount(['activations as domains_used' => fn ($q) => $q->whereNull('deactivated_at')]);
        }

        return $query->first();
    }
}

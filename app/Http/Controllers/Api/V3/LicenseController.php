<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V3\ActivateLicenseRequest;
use App\Http\Requests\Api\V3\CheckLicenseRequest;
use App\Http\Requests\Api\V3\DeactivateLicenseRequest;
use App\Http\Requests\Api\V3\ValidateLicenseRequest;
use App\Http\Resources\Api\V3\LicenseValidationResource;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LicenseController extends Controller
{
    /**
     * Validate a license key and return license information.
     */
    public function validate(ValidateLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug')
        );

        if (! $license) {
            return $this->errorResponse('Invalid license key.', 'license_invalid', 404);
        }

        $license->update(['last_validated_at' => now()]);

        return response()->json([
            'success' => true,
            'license' => new LicenseValidationResource($license),
        ]);
    }

    /**
     * Activate a license on a domain.
     */
    public function activate(ActivateLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug')
        );

        if (! $license) {
            return $this->errorResponse('Invalid license key.', 'license_invalid', 404);
        }

        if (! $license->isActive()) {
            return $this->errorResponse(
                'License is not active.',
                'license_inactive',
                403
            );
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        // Check if already activated on this domain
        $existingActivation = $license->activations()
            ->where('domain', $domain)
            ->first();

        if ($existingActivation) {
            // If already active, update last checked and return success
            if ($existingActivation->isActive()) {
                $existingActivation->updateLastChecked();

                return response()->json([
                    'success' => true,
                    'message' => 'License already activated on this domain.',
                    'license' => new LicenseValidationResource($license),
                ]);
            }

            // If deactivated, reactivate it
            $existingActivation->reactivate();

            return response()->json([
                'success' => true,
                'message' => 'License reactivated on this domain.',
                'license' => new LicenseValidationResource($license->fresh(['product', 'package'])->loadCount(['activations as domains_used' => fn ($q) => $q->whereNull('deactivated_at')])),
            ]);
        }

        // Check domain limit
        if (! $license->canActivateMoreDomains()) {
            return $this->errorResponse(
                'Domain limit reached. Maximum '.$license->domain_limit.' domain(s) allowed.',
                'domain_limit_reached',
                403
            );
        }

        // Create new activation within transaction
        DB::transaction(function () use ($license, $domain, $request) {
            $license->activations()->create([
                'domain' => $domain,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_checked_at' => now(),
            ]);

            $license->update(['last_validated_at' => now()]);
        });

        return response()->json([
            'success' => true,
            'message' => 'License activated successfully.',
            'license' => new LicenseValidationResource($license->fresh(['product', 'package'])->loadCount(['activations as domains_used' => fn ($q) => $q->whereNull('deactivated_at')])),
        ], 201);
    }

    /**
     * Deactivate a license from a domain.
     */
    public function deactivate(DeactivateLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug'),
            withRelations: false
        );

        if (! $license) {
            return $this->errorResponse('Invalid license key.', 'license_invalid', 404);
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        $activation = $license->activations()
            ->where('domain', $domain)
            ->whereNull('deactivated_at')
            ->first();

        if (! $activation) {
            return $this->errorResponse(
                'No active activation found for this domain.',
                'activation_not_found',
                404
            );
        }

        $activation->deactivate();

        return response()->json([
            'success' => true,
            'message' => 'License deactivated successfully.',
        ]);
    }

    /**
     * Check if a license is active on a specific domain.
     */
    public function check(CheckLicenseRequest $request): JsonResponse
    {
        $license = $this->findLicense(
            $request->validated('license_key'),
            $request->validated('product_slug')
        );

        if (! $license) {
            return $this->errorResponse('Invalid license key.', 'license_invalid', 404);
        }

        $domain = $this->normalizeDomain($request->validated('domain'));

        $activation = $license->activations()
            ->where('domain', $domain)
            ->whereNull('deactivated_at')
            ->first();

        if (! $activation) {
            return response()->json([
                'success' => true,
                'activated' => false,
                'license_valid' => $license->isActive(),
            ]);
        }

        // Update last checked timestamp
        $activation->updateLastChecked();
        $license->update(['last_validated_at' => now()]);

        return response()->json([
            'success' => true,
            'activated' => true,
            'license_valid' => $license->isActive(),
            'license' => new LicenseValidationResource($license),
        ]);
    }

    /**
     * Find a license by key and product slug.
     */
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

    /**
     * Normalize domain by removing protocol, www, port, and paths.
     */
    private function normalizeDomain(string $domain): string
    {
        // Remove protocol
        $domain = preg_replace('#^https?://#', '', $domain);

        // Remove paths and query strings
        $domain = explode('/', $domain)[0];
        $domain = explode('?', $domain)[0];

        // Remove port
        $domain = preg_replace('#:\d+$#', '', $domain);

        // Remove www prefix
        $domain = preg_replace('#^www\.#', '', $domain);

        return strtolower(trim($domain));
    }

    /**
     * Return a standardized error response.
     */
    private function errorResponse(string $message, string $code, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code,
            ],
        ], $status);
    }
}

<?php

namespace App\Http\Resources\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\License
 */
class LicenseValidationResource extends JsonResource
{
    protected ?bool $activated = null;

    public function withActivated(?bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'valid' => $this->isActive(),
            'status' => $this->status->value,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'activations' => [
                'limit' => $this->domain_limit,
                'used' => $this->domains_used ?? $this->activeActivations()->count(),
            ],
            'product' => $this->product->name,
            'package' => $this->package->name,
        ];

        if ($this->activated !== null) {
            $data['activated'] = $this->activated;
        }

        return $data;
    }
}

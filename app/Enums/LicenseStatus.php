<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum LicenseStatus: string implements HasColor, HasIcon, HasLabel
{
    case Active = 'active';
    case Paused = 'paused';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Paused => 'Paused',
            self::Suspended => 'Suspended',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Paused => 'info',
            self::Suspended => 'warning',
            self::Expired => 'gray',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string|Heroicon
    {
        return match ($this) {
            self::Active => Heroicon::CheckCircle,
            self::Paused => Heroicon::PauseCircle,
            self::Suspended => Heroicon::ExclamationTriangle,
            self::Expired => Heroicon::Clock,
            self::Cancelled => Heroicon::XCircle,
        };
    }

    public function isUsable(): bool
    {
        return $this === self::Active;
    }
}

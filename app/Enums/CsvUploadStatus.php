<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum CsvUploadStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => (string) __('admin.enums.csv_upload_status.pending'),
            self::Completed => (string) __('admin.enums.csv_upload_status.completed'),
            self::Failed => (string) __('admin.enums.csv_upload_status.failed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Completed => 'success',
            self::Failed => 'danger',
        };
    }

    public function getIcon(): string|Heroicon
    {
        return match ($this) {
            self::Pending => Heroicon::Clock,
            self::Completed => Heroicon::CheckCircle,
            self::Failed => Heroicon::XCircle,
        };
    }
}

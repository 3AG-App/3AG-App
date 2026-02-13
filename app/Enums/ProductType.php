<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductType: string implements HasColor, HasIcon, HasLabel
{
    case Plugin = 'plugin';
    case Theme = 'theme';
    case SourceCode = 'source_code';

    public function getLabel(): string
    {
        return match ($this) {
            self::Plugin => (string) __('admin.enums.product_type.plugin'),
            self::Theme => (string) __('admin.enums.product_type.theme'),
            self::SourceCode => (string) __('admin.enums.product_type.source_code'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Plugin => 'success',
            self::Theme => 'info',
            self::SourceCode => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Plugin => 'heroicon-o-puzzle-piece',
            self::Theme => 'heroicon-o-paint-brush',
            self::SourceCode => 'heroicon-o-code-bracket',
        };
    }
}

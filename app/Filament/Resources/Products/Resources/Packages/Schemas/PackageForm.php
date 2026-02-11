<?php

namespace App\Filament\Resources\Products\Resources\Packages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('domain_limit')
                    ->numeric(),
                TextInput::make('stripe_monthly_price_id'),
                TextInput::make('stripe_yearly_price_id'),
                TextInput::make('monthly_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('yearly_price')
                    ->numeric()
                    ->prefix('$'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('features'),
            ]);
    }
}

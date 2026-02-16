<?php

namespace App\Filament\Resources\LicenseActivations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LicenseActivationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('license_id')
                    ->label(__('admin.resources.license_activations.table.license'))
                    ->relationship('license', 'id')
                    ->required(),
                TextInput::make('domain')
                    ->label(__('admin.resources.licenses.relation_activations.columns.domain'))
                    ->required(),
                TextInput::make('ip_address')
                    ->label(__('admin.resources.licenses.relation_activations.columns.ip_address')),
                TextInput::make('user_agent')
                    ->label(__('admin.resources.licenses.relation_activations.columns.browser')),
                DateTimePicker::make('last_checked_at')
                    ->label(__('admin.common.last_check')),
                DateTimePicker::make('activated_at')
                    ->label(__('admin.common.activated'))
                    ->default(now()),
            ]);
    }
}

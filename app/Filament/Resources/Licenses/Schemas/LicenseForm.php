<?php

namespace App\Filament\Resources\Licenses\Schemas;

use App\Enums\LicenseStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LicenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.resources.licenses.form.sections.license_details'))
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Grid::make(2)
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn ($set) => $set('package_id', null)),
                                Select::make('package_id')
                                    ->relationship('package', 'name', fn ($query, $get) => $query->where('product_id', $get('product_id')))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                            ]),
                        TextInput::make('license_key')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('admin.resources.licenses.form.help.auto_generate'))
                            ->placeholder(__('admin.resources.licenses.form.placeholders.license_key_example')),
                    ]),
                Section::make(__('admin.resources.licenses.form.sections.subscription_limits'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('subscription_id')
                                    ->relationship('subscription', 'type')
                                    ->searchable()
                                    ->preload()
                                    ->helperText(__('admin.resources.licenses.form.help.optional_stripe_subscription'))
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->email.' - '.ucfirst($record->type).' ('.$record->stripe_status.')'
                                    ),
                                TextInput::make('domain_limit')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder(__('admin.resources.licenses.form.placeholders.unlimited'))
                                    ->helperText(__('admin.resources.licenses.form.help.copy_from_package')),
                            ]),
                    ]),
                Section::make(__('admin.resources.licenses.form.sections.status_expiry'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options(LicenseStatus::class)
                                    ->required()
                                    ->default(LicenseStatus::Active)
                                    ->native(false),
                                DateTimePicker::make('expires_at')
                                    ->helperText(__('admin.resources.licenses.form.help.no_expiration')),
                            ]),
                    ]),
            ]);
    }
}

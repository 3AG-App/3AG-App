<?php

namespace App\Filament\Resources\Products\Resources\Packages\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make(__('admin.resources.products.packages.form.sections.package_information'))
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set, $context) => $context === 'create'
                                    ? $set('slug', Str::slug($state))
                                    : null
                                ),
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->helperText(__('admin.resources.products.packages.form.help.slug')),
                            Textarea::make('description')
                                ->rows(3)
                                ->placeholder(__('admin.resources.products.packages.form.placeholders.description')),
                        ]),
                    Section::make(__('admin.resources.products.form.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('admin.common.active'))
                                ->helperText(__('admin.resources.products.packages.form.help.inactive_hidden'))
                                ->default(true),
                            TextInput::make('sort_order')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->helperText(__('admin.resources.products.form.help.sort_order')),
                            TextInput::make('domain_limit')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder(__('admin.resources.products.packages.form.placeholders.unlimited'))
                                ->helperText(__('admin.resources.products.packages.form.help.unlimited_domains')),
                        ])->grow(false),
                ])->from('md')->columnSpanFull(),
                Section::make(__('admin.resources.products.packages.form.sections.pricing'))
                    ->icon(Heroicon::CurrencyDollar)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('monthly_price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder(__('admin.resources.products.packages.form.placeholders.amount'))
                            ->helperText(__('admin.resources.products.packages.form.help.monthly_price')),
                        TextInput::make('yearly_price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder(__('admin.resources.products.packages.form.placeholders.amount'))
                            ->helperText(__('admin.resources.products.packages.form.help.yearly_price')),
                        TextInput::make('stripe_monthly_price_id')
                            ->label(__('admin.resources.products.packages.form.fields.stripe_monthly_price_id'))
                            ->placeholder(__('admin.resources.products.packages.form.placeholders.stripe_price'))
                            ->unique(ignoreRecord: true)
                            ->helperText(__('admin.resources.products.packages.form.help.stripe_monthly_price_id')),
                        TextInput::make('stripe_yearly_price_id')
                            ->label(__('admin.resources.products.packages.form.fields.stripe_yearly_price_id'))
                            ->placeholder(__('admin.resources.products.packages.form.placeholders.stripe_price'))
                            ->unique(ignoreRecord: true)
                            ->helperText(__('admin.resources.products.packages.form.help.stripe_yearly_price_id')),
                    ]),
                Section::make(__('admin.resources.products.packages.form.sections.features'))
                    ->icon(Heroicon::ListBullet)
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('features')
                            ->placeholder(__('admin.resources.products.packages.form.placeholders.add_feature'))
                            ->helperText(__('admin.resources.products.packages.form.help.features')),
                    ]),
            ]);
    }
}

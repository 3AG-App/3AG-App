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
                    Section::make('Package Information')
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
                                ->helperText('URL-friendly identifier. Auto-generated from name on creation.'),
                            Textarea::make('description')
                                ->rows(3)
                                ->placeholder('Brief description of this package tier...'),
                        ]),
                    Section::make('Settings')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Active')
                                ->helperText('Inactive packages are hidden from the storefront.')
                                ->default(true),
                            TextInput::make('sort_order')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->helperText('Lower numbers appear first.'),
                            TextInput::make('domain_limit')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('Unlimited')
                                ->helperText('Leave empty for unlimited domains.'),
                        ])->grow(false),
                ])->from('md')->columnSpanFull(),
                Section::make('Pricing')
                    ->icon(Heroicon::CurrencyDollar)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('monthly_price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Monthly subscription price.'),
                        TextInput::make('yearly_price')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->helperText('Yearly subscription price (typically discounted).'),
                        TextInput::make('stripe_monthly_price_id')
                            ->label('Stripe Monthly Price ID')
                            ->placeholder('price_...')
                            ->helperText('Stripe price ID for monthly billing.'),
                        TextInput::make('stripe_yearly_price_id')
                            ->label('Stripe Yearly Price ID')
                            ->placeholder('price_...')
                            ->helperText('Stripe price ID for yearly billing.'),
                    ]),
                Section::make('Features')
                    ->icon(Heroicon::ListBullet)
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('features')
                            ->placeholder('Add a feature...')
                            ->helperText('List the features included in this package. Press Enter after each feature.'),
                    ]),
            ]);
    }
}

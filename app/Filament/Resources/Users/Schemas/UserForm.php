<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.resources.users.sections.account_information'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('admin.resources.users.form.fields.email_address'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        DateTimePicker::make('email_verified_at')
                            ->label(__('admin.resources.users.form.fields.email_verified_at'))
                            ->native(false),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->revealable()
                            ->helperText(__('admin.resources.users.form.help.password_edit_blank')),
                    ]),

                Section::make(__('admin.resources.users.sections.subscription_billing'))
                    ->description(__('admin.resources.users.form.description.stripe_subscription_information'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        TextInput::make('stripe_id')
                            ->label(__('admin.resources.users.fields.stripe_customer_id'))
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false),
                        DateTimePicker::make('trial_ends_at')
                            ->label(__('admin.resources.users.form.fields.trial_ends_at'))
                            ->native(false),
                        Fieldset::make(__('admin.resources.users.fields.payment_method'))
                            ->columns(2)
                            ->columnSpanFull()
                            ->components([
                                TextInput::make('pm_type')
                                    ->label(__('admin.common.type'))
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('pm_last_four')
                                    ->label(__('admin.resources.users.form.fields.last_4_digits'))
                                    ->maxLength(4)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription')
                    ->icon(Heroicon::CreditCard)
                    ->columns(2)
                    ->components([
                        TextEntry::make('type')
                            ->label('Name')
                            ->badge(),
                        TextEntry::make('stripe_status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('stripe_id')
                            ->label('Stripe ID')
                            ->copyable()
                            ->icon(Heroicon::CreditCard),
                        TextEntry::make('stripe_price')
                            ->label('Stripe Price')
                            ->placeholder('Multiple prices'),
                        TextEntry::make('quantity')
                            ->placeholder('N/A'),
                    ]),
                Section::make('Customer')
                    ->icon(Heroicon::User)
                    ->columns(2)
                    ->components([
                        TextEntry::make('user.name')
                            ->label('Name')
                            ->placeholder('Unknown'),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable()
                            ->placeholder('Unknown')
                            ->icon(Heroicon::Envelope),
                    ]),
                Section::make('Dates')
                    ->icon(Heroicon::Calendar)
                    ->columns(3)
                    ->components([
                        TextEntry::make('trial_ends_at')
                            ->label('Trial Ends')
                            ->dateTime()
                            ->placeholder('No trial'),
                        TextEntry::make('ends_at')
                            ->label('Ends')
                            ->dateTime()
                            ->placeholder('Active'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since(),
                    ]),
            ]);
    }
}

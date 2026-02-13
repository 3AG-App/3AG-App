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
                Section::make(__('admin.resources.subscriptions.infolist.sections.subscription'))
                    ->icon(Heroicon::CreditCard)
                    ->columns(2)
                    ->components([
                        TextEntry::make('type')
                            ->label(__('admin.common.name'))
                            ->badge(),
                        TextEntry::make('stripe_status')
                            ->label(__('admin.common.status'))
                            ->badge(),
                        TextEntry::make('stripe_id')
                            ->label(__('admin.resources.subscriptions.table.stripe_id'))
                            ->copyable()
                            ->icon(Heroicon::CreditCard),
                        TextEntry::make('stripe_price')
                            ->label(__('admin.resources.subscriptions.table.stripe_price'))
                            ->placeholder(__('admin.resources.subscriptions.table.placeholders.multiple_prices')),
                        TextEntry::make('quantity')
                            ->placeholder(__('admin.common.na')),
                    ]),
                Section::make(__('admin.common.customer'))
                    ->icon(Heroicon::User)
                    ->columns(2)
                    ->components([
                        TextEntry::make('user.name')
                            ->label(__('admin.common.name'))
                            ->placeholder(__('admin.common.unknown')),
                        TextEntry::make('user.email')
                            ->label(__('admin.common.email'))
                            ->copyable()
                            ->placeholder(__('admin.common.unknown'))
                            ->icon(Heroicon::Envelope),
                    ]),
                Section::make(__('admin.resources.subscriptions.infolist.sections.dates'))
                    ->icon(Heroicon::Calendar)
                    ->columns(3)
                    ->components([
                        TextEntry::make('trial_ends_at')
                            ->label(__('admin.resources.subscriptions.table.trial_ends'))
                            ->dateTime()
                            ->placeholder(__('admin.resources.subscriptions.table.placeholders.no_trial')),
                        TextEntry::make('ends_at')
                            ->label(__('admin.resources.subscriptions.table.ends'))
                            ->dateTime()
                            ->placeholder(__('admin.common.active')),
                        TextEntry::make('created_at')
                            ->label(__('admin.common.created'))
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label(__('admin.common.last_updated'))
                            ->since(),
                    ]),
            ]);
    }
}

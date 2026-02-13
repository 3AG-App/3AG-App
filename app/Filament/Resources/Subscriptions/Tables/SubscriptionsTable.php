<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Laravel\Cashier\Subscription;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('admin.common.name'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('admin.common.customer'))
                    ->icon(Heroicon::User)
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email),
                TextColumn::make('stripe_status')
                    ->label(__('admin.common.status'))
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'past_due', 'unpaid', 'incomplete_expired' => 'danger',
                        'incomplete' => 'warning',
                        'canceled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('stripe_id')
                    ->label(__('admin.resources.subscriptions.table.stripe_id'))
                    ->copyable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('stripe_price')
                    ->label(__('admin.resources.subscriptions.table.stripe_price'))
                    ->copyable()
                    ->placeholder(__('admin.resources.subscriptions.table.placeholders.multiple_prices'))
                    ->toggleable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('trial_ends_at')
                    ->label(__('admin.resources.subscriptions.table.trial_ends'))
                    ->dateTime()
                    ->since()
                    ->placeholder(__('admin.resources.subscriptions.table.placeholders.no_trial'))
                    ->badge()
                    ->color(fn ($state): string => $state && $state->isFuture() ? 'warning' : 'gray')
                    ->toggleable(),
                TextColumn::make('ends_at')
                    ->label(__('admin.resources.subscriptions.table.ends'))
                    ->dateTime()
                    ->since()
                    ->placeholder(__('admin.common.active'))
                    ->badge()
                    ->color(fn ($state): string => $state ? ($state->isPast() ? 'danger' : 'warning') : 'success')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('stripe_status')
                    ->label(__('admin.common.status'))
                    ->options(fn () => Subscription::query()
                        ->distinct()
                        ->orderBy('stripe_status')
                        ->pluck('stripe_status', 'stripe_status')
                        ->all())
                    ->searchable(),
                SelectFilter::make('type')
                    ->label(__('admin.common.name'))
                    ->options(fn () => Subscription::query()
                        ->distinct()
                        ->orderBy('type')
                        ->pluck('type', 'type')
                        ->all())
                    ->searchable(),
                TernaryFilter::make('active')
                    ->label(__('admin.common.active'))
                    ->placeholder(__('admin.common.all'))
                    ->trueLabel(__('admin.common.active'))
                    ->falseLabel(__('admin.resources.subscriptions.table.ended'))
                    ->queries(
                        true: fn ($query) => $query->whereNull('ends_at'),
                        false: fn ($query) => $query->whereNotNull('ends_at'),
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->recordUrl(fn ($record): string => SubscriptionResource::getUrl('view', ['record' => $record]));
    }
}

<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record) => $record->licenses()->count().' licenses'),
                TextColumn::make('email')
                    ->label(__('admin.resources.users.table.email_address'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('admin.resources.users.table.email_copied'))
                    ->icon(Heroicon::Envelope),
                IconColumn::make('email_verified_at')
                    ->label(__('admin.resources.users.fields.verified'))
                    ->boolean()
                    ->sortable()
                    ->trueIcon(Heroicon::CheckBadge)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('stripe_id')
                    ->label(__('admin.resources.users.table.stripe_customer'))
                    ->searchable()
                    ->toggleable()
                    ->placeholder(__('admin.resources.users.table.placeholders.no_subscription'))
                    ->badge()
                    ->color('info')
                    ->icon(Heroicon::CreditCard),
                TextColumn::make('pm_last_four')
                    ->label(__('admin.resources.users.fields.payment_method'))
                    ->formatStateUsing(fn (?string $state, $record): ?string => $state ? "{$record->pm_type} •••• {$state}" : null
                    )
                    ->placeholder(__('admin.resources.users.table.placeholders.no_card'))
                    ->toggleable(),
                TextColumn::make('trial_ends_at')
                    ->label(__('admin.resources.users.fields.trial_ends'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('admin.resources.users.placeholders.no_trial'))
                    ->since()
                    ->badge()
                    ->color(fn ($state): string => $state && $state->isFuture() ? 'warning' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label(__('admin.resources.users.table.filters.email_verified'))
                    ->nullable(),
                TernaryFilter::make('stripe_id')
                    ->label(__('admin.resources.users.table.filters.has_subscription'))
                    ->nullable()
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('stripe_id'),
                        false: fn ($query) => $query->whereNull('stripe_id'),
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye),
                EditAction::make()
                    ->icon(Heroicon::Pencil),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->recordUrl(fn ($record): string => UserResource::getUrl('view', ['record' => $record]));
    }
}

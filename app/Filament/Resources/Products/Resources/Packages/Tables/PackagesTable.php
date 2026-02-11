<?php

namespace App\Filament\Resources\Products\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('monthly_price')
                    ->money()
                    ->sortable()
                    ->description(fn ($record) => $record->stripe_monthly_price_id
                        ? 'Stripe: '.$record->stripe_monthly_price_id
                        : null
                    ),
                TextColumn::make('yearly_price')
                    ->money()
                    ->sortable()
                    ->description(fn ($record) => $record->stripe_yearly_price_id
                        ? 'Stripe: '.$record->stripe_yearly_price_id
                        : null
                    ),
                TextColumn::make('domain_limit')
                    ->label('Domains')
                    ->formatStateUsing(fn ($state) => $state === null ? '∞ Unlimited' : $state)
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'success' : 'info')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                EditAction::make()
                    ->icon(Heroicon::Pencil),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped();
    }
}

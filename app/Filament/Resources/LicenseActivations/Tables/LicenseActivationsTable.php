<?php

namespace App\Filament\Resources\LicenseActivations\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LicenseActivationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('license.license_key')
                    ->label('License')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('license.user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('domain')
                    ->searchable()
                    ->icon('heroicon-o-globe-alt')
                    ->copyable(),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->icon('heroicon-o-server')
                    ->placeholder('N/A'),
                TextColumn::make('last_checked_at')
                    ->label('Last Check')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('Never')
                    ->color(fn ($state) => $state && $state->lt(now()->subDays(7)) ? 'danger' : 'success'),
                TextColumn::make('activated_at')
                    ->label('Activated')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('license')
                    ->relationship('license', 'license_key')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete())
                    ->modalHeading('Deactivate Domain')
                    ->modalDescription(fn ($record) => "Remove activation for {$record->domain}?"),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Deactivate Selected'),
                ]),
            ])
            ->defaultSort('last_checked_at', 'desc');
    }
}

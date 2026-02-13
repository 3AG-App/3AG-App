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
                    ->label(__('admin.resources.license_activations.table.license'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('license.user.name')
                    ->label(__('admin.resources.license_activations.table.user'))
                    ->searchable(),
                TextColumn::make('domain')
                    ->searchable()
                    ->icon('heroicon-o-globe-alt')
                    ->copyable(),
                TextColumn::make('ip_address')
                    ->searchable()
                    ->icon('heroicon-o-server')
                    ->placeholder(__('admin.common.na')),
                TextColumn::make('last_checked_at')
                    ->label(__('admin.common.last_check'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder(__('admin.common.never'))
                    ->color(fn ($state) => $state && $state->lt(now()->subDays(7)) ? 'danger' : 'success'),
                TextColumn::make('activated_at')
                    ->label(__('admin.common.activated'))
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
                    ->modalHeading(__('admin.resources.license_activations.table.modals.deactivate_domain'))
                    ->modalDescription(fn ($record) => __('admin.resources.license_activations.table.modals.remove_activation_for', ['domain' => $record->domain])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('admin.resources.license_activations.table.actions.deactivate_selected')),
                ]),
            ])
            ->defaultSort('last_checked_at', 'desc');
    }
}

<?php

namespace App\Filament\Resources\Licenses\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivationsRelationManager extends RelationManager
{
    protected static string $relationship = 'activations';

    protected static \BackedEnum|string|null $icon = Heroicon::OutlinedGlobeAlt;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.licenses.relation_activations.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('domain')
            ->columns([
                TextColumn::make('domain')
                    ->label(__('admin.resources.licenses.relation_activations.columns.domain'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('admin.resources.licenses.relation_activations.notifications.domain_copied'))
                    ->weight('bold')
                    ->icon(Heroicon::OutlinedGlobeAlt),
                IconColumn::make('deactivated_at')
                    ->label(__('admin.common.status'))
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->isActive())
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('ip_address')
                    ->label(__('admin.resources.licenses.relation_activations.columns.ip_address'))
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('user_agent')
                    ->label(__('admin.resources.licenses.relation_activations.columns.browser'))
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('activated_at')
                    ->label(__('admin.common.activated'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_checked_at')
                    ->label(__('admin.common.last_check'))
                    ->since()
                    ->placeholder(__('admin.common.never'))
                    ->sortable(),
                TextColumn::make('deactivated_at')
                    ->label(__('admin.common.deactivated'))
                    ->dateTime()
                    ->placeholder(__('admin.common.active'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('deactivated_at')
                    ->label(__('admin.common.status'))
                    ->nullable()
                    ->trueLabel(__('admin.common.deactivated'))
                    ->falseLabel(__('admin.common.active'))
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('deactivated_at'),
                        false: fn ($query) => $query->whereNull('deactivated_at'),
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.resources.licenses.relation_activations.modals.deactivate.heading'))
                    ->modalDescription(__('admin.resources.licenses.relation_activations.modals.deactivate.description'))
                    ->action(fn ($record) => $record->deactivate())
                    ->visible(fn ($record) => $record->isActive()),
                Action::make('reactivate')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.resources.licenses.relation_activations.modals.reactivate.heading'))
                    ->modalDescription(__('admin.resources.licenses.relation_activations.modals.reactivate.description'))
                    ->action(fn ($record) => $record->reactivate())
                    ->visible(fn ($record) => ! $record->isActive()),
            ])
            ->defaultSort('activated_at', 'desc')
            ->emptyStateHeading(__('admin.resources.licenses.relation_activations.empty.heading'))
            ->emptyStateDescription(__('admin.resources.licenses.relation_activations.empty.description'))
            ->emptyStateIcon(Heroicon::OutlinedGlobeAlt);
    }
}

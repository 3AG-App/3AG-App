<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\Products\Resources\Packages\PackageResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'Packages';

    protected static \BackedEnum|string|null $icon = Heroicon::OutlinedRectangleStack;

    protected static ?string $relatedResource = PackageResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('monthly_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('yearly_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('domain_limit')
                    ->label('Domains')
                    ->formatStateUsing(fn ($state) => $state === null ? '∞ Unlimited' : $state)
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'success' : 'info'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn ($record): string => PackageResource::getUrl('edit', [
                        'record' => $record,
                        'parent' => $this->getOwnerRecord(),
                    ]))
                    ->icon(Heroicon::OutlinedEye),
                Action::make('edit')
                    ->url(fn ($record): string => PackageResource::getUrl('edit', [
                        'record' => $record,
                        'parent' => $this->getOwnerRecord(),
                    ]))
                    ->icon(Heroicon::OutlinedPencil),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateHeading('No packages yet')
            ->emptyStateDescription('Create packages with pricing tiers for this product.')
            ->emptyStateIcon(Heroicon::OutlinedRectangleStack);
    }
}

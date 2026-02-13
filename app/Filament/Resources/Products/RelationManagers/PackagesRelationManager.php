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
use Illuminate\Database\Eloquent\Model;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static \BackedEnum|string|null $icon = Heroicon::OutlinedRectangleStack;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.products.relation_packages.title');
    }

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
                    ->label(__('admin.common.domains'))
                    ->formatStateUsing(fn ($state) => $state === null ? __('admin.resources.licenses.placeholders.unlimited') : $state)
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'success' : 'info'),
                IconColumn::make('is_active')
                    ->label(__('admin.common.active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('admin.common.order'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('admin.common.active'))
                    ->trueLabel(__('admin.resources.products.table.filters.active_only'))
                    ->falseLabel(__('admin.resources.products.table.filters.inactive_only')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn ($record): string => PackageResource::getUrl('edit', [
                        'record' => $record,
                        'product' => $this->getOwnerRecord(),
                    ]))
                    ->icon(Heroicon::OutlinedEye),
                Action::make('edit')
                    ->url(fn ($record): string => PackageResource::getUrl('edit', [
                        'record' => $record,
                        'product' => $this->getOwnerRecord(),
                    ]))
                    ->icon(Heroicon::OutlinedPencil),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateHeading(__('admin.resources.products.relation_packages.empty.heading'))
            ->emptyStateDescription(__('admin.resources.products.relation_packages.empty.description'))
            ->emptyStateIcon(Heroicon::OutlinedRectangleStack);
    }
}

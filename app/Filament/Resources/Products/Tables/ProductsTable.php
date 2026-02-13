<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductType;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('screenshots')
                    ->collection('screenshots')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('packages_count')
                    ->counts('packages')
                    ->label(__('admin.resources.products.table.packages'))
                    ->badge()
                    ->color('info')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('admin.common.active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('admin.common.order'))
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
                SelectFilter::make('type')
                    ->options(ProductType::class),
                TernaryFilter::make('is_active')
                    ->label(__('admin.common.active'))
                    ->trueLabel(__('admin.resources.products.table.filters.active_only'))
                    ->falseLabel(__('admin.resources.products.table.filters.inactive_only')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye),
                ActionGroup::make([
                    EditAction::make()
                        ->icon(Heroicon::Pencil),
                ])->icon(Heroicon::EllipsisVertical),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped()
            ->recordUrl(fn ($record): string => ProductResource::getUrl('view', ['record' => $record]));
    }
}

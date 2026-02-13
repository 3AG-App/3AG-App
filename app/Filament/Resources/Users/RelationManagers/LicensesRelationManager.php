<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\LicenseStatus;
use App\Filament\Resources\Licenses\LicenseResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    protected static \BackedEnum|string|null $icon = Heroicon::OutlinedKey;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.users.relation_licenses.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('license_key')
            ->columns([
                TextColumn::make('license_key')
                    ->label(__('admin.resources.users.relation_licenses.columns.license_key'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('admin.common.copied'))
                    ->weight('bold')
                    ->limit(25),
                TextColumn::make('product.name')
                    ->label(__('admin.common.product'))
                    ->badge()
                    ->color(fn ($record) => $record->product?->type?->getColor() ?? 'gray'),
                TextColumn::make('package.name')
                    ->label(__('admin.common.package')),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('domain_limit')
                    ->label(__('admin.common.domains'))
                    ->formatStateUsing(fn ($state, $record) => $record->domain_limit === null
                        ? $record->activeActivations()->count().' / ∞'
                        : $record->activeActivations()->count().' / '.$state
                    )
                    ->badge()
                    ->color(fn ($state, $record) => $record->domain_limit === null
                        ? 'success'
                        : ($record->activeActivations()->count() >= $record->domain_limit ? 'danger' : 'info')),
                TextColumn::make('expires_at')
                    ->label(__('admin.common.expires'))
                    ->dateTime()
                    ->placeholder(__('admin.common.never'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.common.created'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(LicenseStatus::class),
                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn () => LicenseResource::getUrl('create', [
                        'user_id' => $this->getOwnerRecord()->getKey(),
                    ]))
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn ($record): string => LicenseResource::getUrl('view', ['record' => $record]))
                    ->icon(Heroicon::OutlinedEye),
                Action::make('edit')
                    ->url(fn ($record): string => LicenseResource::getUrl('edit', ['record' => $record]))
                    ->icon(Heroicon::OutlinedPencil),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('admin.resources.users.relation_licenses.empty.heading'))
            ->emptyStateDescription(__('admin.resources.users.relation_licenses.empty.description'))
            ->emptyStateIcon(Heroicon::OutlinedKey);
    }
}

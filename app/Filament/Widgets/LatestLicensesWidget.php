<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Licenses\LicenseResource;
use App\Models\License;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLicensesWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(License::query()->with(['user', 'product', 'package'])->latest()->limit(10))
            ->heading(__('admin.widgets.latest_licenses.heading'))
            ->description(__('admin.widgets.latest_licenses.description'))
            ->columns([
                TextColumn::make('license_key')
                    ->label(__('admin.widgets.latest_licenses.columns.license_key'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('admin.common.copied'))
                    ->weight('bold')
                    ->limit(20),
                TextColumn::make('user.name')
                    ->label(__('admin.common.customer'))
                    ->searchable()
                    ->icon(Heroicon::OutlinedUser),
                TextColumn::make('user.email')
                    ->label(__('admin.common.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('product.name')
                    ->label(__('admin.common.product'))
                    ->badge()
                    ->color(fn ($record) => $record->product?->type?->getColor() ?? 'gray'),
                TextColumn::make('package.name')
                    ->label(__('admin.common.package'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.common.status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('admin.common.created'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->headerActions([
                CreateAction::make()
                    ->url(LicenseResource::getUrl('create'))
                    ->label(__('admin.resources.licenses.list.actions.new_license'))
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (License $record): string => LicenseResource::getUrl('edit', ['record' => $record]))
                    ->icon(Heroicon::OutlinedEye),
            ]);
    }
}

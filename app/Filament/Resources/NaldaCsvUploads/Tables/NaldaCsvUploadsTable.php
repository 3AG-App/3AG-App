<?php

namespace App\Filament\Resources\NaldaCsvUploads\Tables;

use App\Enums\CsvUploadStatus;
use App\Enums\NaldaCsvType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NaldaCsvUploadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('license.license_key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('License key copied!')
                    ->limit(20),
                TextColumn::make('domain')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('csv_type')
                    ->badge(),
                TextColumn::make('sftp_host')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('uploaded_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not uploaded'),
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
                SelectFilter::make('csv_type')
                    ->options(NaldaCsvType::class),
                SelectFilter::make('status')
                    ->options(CsvUploadStatus::class),
                SelectFilter::make('license')
                    ->relationship('license', 'license_key')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

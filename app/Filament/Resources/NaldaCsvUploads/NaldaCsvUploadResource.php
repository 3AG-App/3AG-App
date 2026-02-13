<?php

namespace App\Filament\Resources\NaldaCsvUploads;

use App\Filament\Resources\NaldaCsvUploads\Pages\CreateNaldaCsvUpload;
use App\Filament\Resources\NaldaCsvUploads\Pages\EditNaldaCsvUpload;
use App\Filament\Resources\NaldaCsvUploads\Pages\ListNaldaCsvUploads;
use App\Filament\Resources\NaldaCsvUploads\Schemas\NaldaCsvUploadForm;
use App\Filament\Resources\NaldaCsvUploads\Tables\NaldaCsvUploadsTable;
use App\Models\NaldaCsvUpload;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NaldaCsvUploadResource extends Resource
{
    protected static ?string $model = NaldaCsvUpload::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.navigation.license_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.nalda_csv_uploads.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.nalda_csv_uploads.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.nalda_csv_uploads.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return NaldaCsvUploadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NaldaCsvUploadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNaldaCsvUploads::route('/'),
            'create' => CreateNaldaCsvUpload::route('/create'),
            'edit' => EditNaldaCsvUpload::route('/{record}/edit'),
        ];
    }
}

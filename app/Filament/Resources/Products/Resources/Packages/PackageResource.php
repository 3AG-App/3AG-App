<?php

namespace App\Filament\Resources\Products\Resources\Packages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Products\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Products\Resources\Packages\Schemas\PackageForm;
use App\Filament\Resources\Products\Resources\Packages\Tables\PackagesTable;
use App\Models\Package;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $parentResource = ProductResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('admin.resources.products.packages.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.products.packages.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return PackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagesTable::configure($table);
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
            'create' => CreatePackage::route('/create'),
            'edit' => EditPackage::route('/{record}/edit'),
        ];
    }
}

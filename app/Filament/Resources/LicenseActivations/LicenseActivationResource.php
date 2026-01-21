<?php

namespace App\Filament\Resources\LicenseActivations;

use App\Filament\Resources\LicenseActivations\Pages\CreateLicenseActivation;
use App\Filament\Resources\LicenseActivations\Pages\EditLicenseActivation;
use App\Filament\Resources\LicenseActivations\Pages\ListLicenseActivations;
use App\Filament\Resources\LicenseActivations\Schemas\LicenseActivationForm;
use App\Filament\Resources\LicenseActivations\Tables\LicenseActivationsTable;
use App\Models\LicenseActivation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LicenseActivationResource extends Resource
{
    protected static ?string $model = LicenseActivation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static UnitEnum|string|null $navigationGroup = 'License Management';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Domain Activation';

    protected static ?string $pluralLabel = 'Domain Activations';

    public static function form(Schema $schema): Schema
    {
        return LicenseActivationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LicenseActivationsTable::configure($table);
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
            'index' => ListLicenseActivations::route('/'),
            'create' => CreateLicenseActivation::route('/create'),
            'edit' => EditLicenseActivation::route('/{record}/edit'),
        ];
    }
}

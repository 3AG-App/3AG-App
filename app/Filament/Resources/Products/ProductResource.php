<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\RelationManagers\PackagesRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static UnitEnum|string|null $navigationGroup = 'Product Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active products';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Type' => $record->type->getLabel(),
            'Packages' => $record->packages_count.' packages',
            'Status' => $record->is_active ? 'Active' : 'Inactive',
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->withCount('packages');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Product Details')
                    ->icon(Heroicon::Cube)
                    ->columnSpan(2)
                    ->columns(2)
                    ->components([
                        TextEntry::make('name')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('slug')
                            ->icon(Heroicon::Link)
                            ->copyable(),
                        TextEntry::make('type')
                            ->badge(),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->columnSpanFull()
                            ->placeholder('No description'),
                        SpatieMediaLibraryImageEntry::make('screenshots')
                            ->collection('screenshots')
                            ->columnSpanFull(),
                    ]),
                Section::make('Statistics')
                    ->icon(Heroicon::ChartBar)
                    ->columnSpan(1)
                    ->components([
                        TextEntry::make('packages_count')
                            ->label('Total Packages')
                            ->state(fn (Product $record): int => $record->packages()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('active_packages_count')
                            ->label('Active Packages')
                            ->state(fn (Product $record): int => $record->activePackages()->count())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('sort_order')
                            ->label('Sort Order')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PackagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}

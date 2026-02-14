<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\RelationManagers\PackagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ReleasesRelationManager;
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
        return __('admin.resources.products.navigation_badge_tooltip');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.navigation.shop_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.products.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.products.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.products.plural_model_label');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('admin.common.type') => $record->type->getLabel(),
            __('admin.resources.products.table.packages') => $record->packages_count.' '.__('admin.resources.products.global_search.packages_suffix'),
            __('admin.common.status') => $record->is_active ? __('admin.common.active') : __('admin.common.inactive'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'short_description', 'long_description'];
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
                Section::make(__('admin.resources.products.sections.product_details'))
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
                            ->label(__('admin.common.active'))
                            ->boolean(),
                        TextEntry::make('short_description')
                            ->label(__('admin.resources.products.fields.short_description'))
                            ->columnSpanFull()
                            ->placeholder(__('admin.resources.products.placeholders.no_short_description')),
                        TextEntry::make('long_description')
                            ->label(__('admin.resources.products.fields.long_description'))
                            ->columnSpanFull()
                            ->placeholder(__('admin.resources.products.placeholders.no_long_description')),
                        SpatieMediaLibraryImageEntry::make('screenshots')
                            ->collection('screenshots')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.resources.products.sections.statistics'))
                    ->icon(Heroicon::ChartBar)
                    ->columnSpan(1)
                    ->components([
                        TextEntry::make('packages_count')
                            ->label(__('admin.resources.products.fields.total_packages'))
                            ->state(fn (Product $record): int => $record->packages()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('active_packages_count')
                            ->label(__('admin.resources.products.fields.active_packages'))
                            ->state(fn (Product $record): int => $record->activePackages()->count())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('sort_order')
                            ->label(__('admin.resources.products.fields.sort_order'))
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('created_at')
                            ->label(__('admin.common.created'))
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label(__('admin.common.last_updated'))
                            ->since(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PackagesRelationManager::class,
            ReleasesRelationManager::class,
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

<?php

namespace App\Filament\Resources\Licenses;

use App\Enums\LicenseStatus;
use App\Filament\Resources\Licenses\Pages\CreateLicense;
use App\Filament\Resources\Licenses\Pages\EditLicense;
use App\Filament\Resources\Licenses\Pages\ListLicenses;
use App\Filament\Resources\Licenses\Pages\ViewLicense;
use App\Filament\Resources\Licenses\RelationManagers\ActivationsRelationManager;
use App\Filament\Resources\Licenses\Schemas\LicenseForm;
use App\Filament\Resources\Licenses\Tables\LicensesTable;
use App\Models\License;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'license_key';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', LicenseStatus::Active)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('admin.resources.licenses.navigation_badge_tooltip');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.navigation.license_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.licenses.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.licenses.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.licenses.plural_model_label');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->license_key;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('admin.common.customer') => $record->user?->name ?? __('admin.common.unknown'),
            __('admin.common.product') => $record->product?->name ?? __('admin.common.unknown'),
            __('admin.common.status') => $record->status->getLabel(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['license_key', 'user.name', 'user.email', 'product.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'product', 'package']);
    }

    public static function form(Schema $schema): Schema
    {
        return LicenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LicensesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.resources.licenses.sections.license_information'))
                    ->icon(Heroicon::Key)
                    ->columnSpanFull()
                    ->columns(3)
                    ->components([
                        TextEntry::make('license_key')
                            ->label(__('admin.resources.licenses.fields.license_key'))
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage(__('admin.resources.licenses.notifications.license_key_copied'))
                            ->icon(Heroicon::ClipboardDocument),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('expires_at')
                            ->label(__('admin.common.expires'))
                            ->dateTime()
                            ->placeholder(__('admin.common.never'))
                            ->icon(Heroicon::Calendar),
                    ]),
                Section::make(__('admin.resources.licenses.sections.customer_product'))
                    ->icon(Heroicon::User)
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        TextEntry::make('user.name')
                            ->label(__('admin.common.customer'))
                            ->icon(Heroicon::User),
                        TextEntry::make('user.email')
                            ->label(__('admin.common.email'))
                            ->copyable()
                            ->icon(Heroicon::Envelope),
                        TextEntry::make('product.name')
                            ->label(__('admin.common.product'))
                            ->badge()
                            ->color(fn ($record) => $record->product?->type?->getColor() ?? 'gray'),
                        TextEntry::make('package.name')
                            ->label(__('admin.common.package')),
                    ]),
                Section::make(__('admin.resources.licenses.sections.usage_limits'))
                    ->icon(Heroicon::ChartBar)
                    ->columnSpanFull()
                    ->columns(3)
                    ->components([
                        TextEntry::make('domain_limit')
                            ->label(__('admin.resources.licenses.fields.domain_limit'))
                            ->formatStateUsing(fn ($state) => $state === null ? __('admin.resources.licenses.placeholders.unlimited') : $state)
                            ->badge()
                            ->color(fn ($state) => $state === null ? 'success' : 'info'),
                        TextEntry::make('activeActivations')
                            ->label(__('admin.resources.licenses.fields.active_domains'))
                            ->state(fn ($record) => $record->activeActivations()->count())
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('last_validated_at')
                            ->label(__('admin.resources.licenses.fields.last_validated'))
                            ->since()
                            ->placeholder(__('admin.common.never')),
                    ]),
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make(__('admin.resources.licenses.sections.subscription'))
                            ->icon(Heroicon::CreditCard)
                            ->components([
                                TextEntry::make('subscription.type')
                                    ->label(__('admin.common.type'))
                                    ->placeholder(__('admin.resources.licenses.placeholders.no_subscription')),
                                TextEntry::make('subscription.stripe_status')
                                    ->label(__('admin.resources.licenses.fields.stripe_status'))
                                    ->badge()
                                    ->placeholder(__('admin.common.na')),
                            ]),
                        Section::make(__('admin.resources.licenses.sections.timestamps'))
                            ->icon(Heroicon::Clock)
                            ->components([
                                TextEntry::make('created_at')
                                    ->label(__('admin.common.created'))
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label(__('admin.common.last_updated'))
                                    ->since(),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLicenses::route('/'),
            'create' => CreateLicense::route('/create'),
            'view' => ViewLicense::route('/{record}'),
            'edit' => EditLicense::route('/{record}/edit'),
        ];
    }
}

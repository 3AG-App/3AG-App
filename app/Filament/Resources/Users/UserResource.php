<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\RelationManagers\LicensesRelationManager;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('admin.resources.users.navigation_badge_tooltip');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.navigation.user_management');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('admin.common.email') => $record->email,
            __('admin.common.status') => $record->stripe_id
                ? __('admin.resources.users.status.subscribed')
                : __('admin.resources.users.status.free'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['licenses']);
    }

    /**
     * @return Collection<int, GlobalSearchResult>
     */
    public static function getGlobalSearchResults(string $search): Collection
    {
        return parent::getGlobalSearchResults($search)->take(5);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(__('admin.resources.users.sections.account_information'))
                    ->icon(Heroicon::User)
                    ->columnSpan(1)
                    ->components([
                        TextEntry::make('name')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('email')
                            ->copyable()
                            ->icon(Heroicon::Envelope),
                        IconEntry::make('email_verified_at')
                            ->label(__('admin.resources.users.fields.verified'))
                            ->boolean()
                            ->trueIcon(Heroicon::CheckBadge)
                            ->falseIcon(Heroicon::XCircle),
                    ]),
                Section::make(__('admin.resources.users.sections.subscription_billing'))
                    ->icon(Heroicon::CreditCard)
                    ->columnSpan(1)
                    ->components([
                        TextEntry::make('stripe_id')
                            ->label(__('admin.resources.users.fields.stripe_customer_id'))
                            ->copyable()
                            ->placeholder(__('admin.resources.users.placeholders.no_stripe_account'))
                            ->icon(Heroicon::CreditCard),
                        TextEntry::make('pm_type')
                            ->label(__('admin.resources.users.fields.payment_method'))
                            ->formatStateUsing(fn ($state, $record) => $state && $record->pm_last_four
                                ? ucfirst($state).' •••• '.$record->pm_last_four
                                : null
                            )
                            ->placeholder(__('admin.resources.users.placeholders.no_payment_method')),
                        TextEntry::make('trial_ends_at')
                            ->label(__('admin.resources.users.fields.trial_ends'))
                            ->dateTime()
                            ->placeholder(__('admin.resources.users.placeholders.no_trial'))
                            ->badge()
                            ->color(fn ($state) => $state && $state->isFuture() ? 'warning' : 'gray'),
                    ]),
                Section::make(__('admin.resources.users.sections.statistics'))
                    ->icon(Heroicon::ChartBar)
                    ->columnSpan(1)
                    ->components([
                        TextEntry::make('licenses_count')
                            ->label(__('admin.resources.users.fields.total_licenses'))
                            ->state(fn ($record) => $record->licenses()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('active_licenses_count')
                            ->label(__('admin.resources.users.fields.active_licenses'))
                            ->state(fn ($record) => $record->licenses()->where('status', 'active')->count())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('created_at')
                            ->label(__('admin.resources.users.fields.member_since'))
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LicensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

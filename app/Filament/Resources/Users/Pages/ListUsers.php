<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus)
                ->label(__('admin.resources.users.list.actions.new_user')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.resources.users.list.tabs.all_users'))
                ->badge(User::count())
                ->badgeColor('gray'),
            'verified' => Tab::make(__('admin.resources.users.list.tabs.verified'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(User::whereNotNull('email_verified_at')->count())
                ->badgeColor('success')
                ->icon(Heroicon::CheckBadge),
            'unverified' => Tab::make(__('admin.resources.users.list.tabs.unverified'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(User::whereNull('email_verified_at')->count())
                ->badgeColor('warning')
                ->icon(Heroicon::ExclamationTriangle),
            'subscribed' => Tab::make(__('admin.resources.users.list.tabs.subscribed'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('stripe_id'))
                ->badge(User::whereNotNull('stripe_id')->count())
                ->badgeColor('info')
                ->icon(Heroicon::CreditCard),
        ];
    }
}

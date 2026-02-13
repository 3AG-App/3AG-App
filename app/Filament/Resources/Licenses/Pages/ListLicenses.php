<?php

namespace App\Filament\Resources\Licenses\Pages;

use App\Enums\LicenseStatus;
use App\Filament\Resources\Licenses\LicenseResource;
use App\Models\License;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::Plus)
                ->label(__('admin.resources.licenses.list.actions.new_license')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.resources.licenses.list.tabs.all_licenses'))
                ->badge(License::count())
                ->badgeColor('gray'),
            'active' => Tab::make(__('admin.resources.licenses.list.tabs.active'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LicenseStatus::Active))
                ->badge(License::where('status', LicenseStatus::Active)->count())
                ->badgeColor('success')
                ->icon(Heroicon::CheckCircle),
            'paused' => Tab::make(__('admin.resources.licenses.list.tabs.paused'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LicenseStatus::Paused))
                ->badge(License::where('status', LicenseStatus::Paused)->count())
                ->badgeColor('info')
                ->icon(Heroicon::PauseCircle),
            'suspended' => Tab::make(__('admin.resources.licenses.list.tabs.suspended'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LicenseStatus::Suspended))
                ->badge(License::where('status', LicenseStatus::Suspended)->count())
                ->badgeColor('warning')
                ->icon(Heroicon::ExclamationTriangle),
            'expired' => Tab::make(__('admin.resources.licenses.list.tabs.expired'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LicenseStatus::Expired))
                ->badge(License::where('status', LicenseStatus::Expired)->count())
                ->badgeColor('gray')
                ->icon(Heroicon::Clock),
            'cancelled' => Tab::make(__('admin.resources.licenses.list.tabs.cancelled'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LicenseStatus::Cancelled))
                ->badge(License::where('status', LicenseStatus::Cancelled)->count())
                ->badgeColor('danger')
                ->icon(Heroicon::XCircle),
        ];
    }
}

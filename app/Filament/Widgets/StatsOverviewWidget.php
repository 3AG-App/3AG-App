<?php

namespace App\Filament\Widgets;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $newUsersLastMonth = User::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ])->count();

        $activeLicenses = License::where('status', LicenseStatus::Active)->count();
        $totalLicenses = License::count();

        $activeProducts = Product::where('is_active', true)->count();

        $usersWithSubscription = User::whereNotNull('stripe_id')->count();

        return [
            Stat::make(__('admin.widgets.stats_overview.total_users'), Number::format($totalUsers))
                ->description(__('admin.widgets.stats_overview.new_this_month', ['count' => $newUsersThisMonth]))
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->chart($this->getUserTrendChart())
                ->color($newUsersThisMonth >= $newUsersLastMonth ? 'success' : 'danger'),

            Stat::make(__('admin.widgets.stats_overview.active_licenses'), Number::format($activeLicenses))
                ->description(__('admin.widgets.stats_overview.total_licenses', ['count' => Number::format($totalLicenses)]))
                ->descriptionIcon(Heroicon::Key)
                ->chart($this->getLicenseTrendChart())
                ->color('success'),

            Stat::make(__('admin.widgets.stats_overview.paying_customers'), Number::format($usersWithSubscription))
                ->description(__('admin.widgets.stats_overview.conversion', [
                    'percentage' => $totalUsers > 0 ? round(($usersWithSubscription / $totalUsers) * 100, 1) : 0,
                ]))
                ->descriptionIcon(Heroicon::CreditCard)
                ->color('info'),

            Stat::make(__('admin.widgets.stats_overview.active_products'), Number::format($activeProducts))
                ->description(__('admin.widgets.stats_overview.available_for_purchase'))
                ->descriptionIcon(Heroicon::CubeTransparent)
                ->color('warning'),
        ];
    }

    /**
     * @return array<int>
     */
    protected function getUserTrendChart(): array
    {
        return collect(range(6, 0))->map(function ($month) {
            $date = now()->subMonths($month);

            return User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();
    }

    /**
     * @return array<int>
     */
    protected function getLicenseTrendChart(): array
    {
        return collect(range(6, 0))->map(function ($month) {
            $date = now()->subMonths($month);

            return License::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();
    }
}

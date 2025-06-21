<?php

namespace App\Filament\Widgets;

use App\Traits\HasDashboardFilters;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Facades\DB;

class Kpi extends StatsOverviewWidget
{
    use HasDashboardFilters;

    protected function getStats(): array
    {
        $countries = DB::table('fact_sales')
            ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id')
            ->distinct('dim_country.country')
            ->count('dim_country.country');
        $total = DB::table('fact_sales')->sum('sales');
        $totalProfit = DB::table('fact_sales')->sum('profit');

        return [
            Stat::make('Active Countries', $countries)
                ->description('Negara yang bertransaksi')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
            Stat::make('Total Sales', '$ ' . number_format($total, 0, ',', '.'))
                ->description('Total penjualan')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            Stat::make('Total Profit', '$ ' . number_format($totalProfit, 0, ',', '.'))
                ->description('Total keuntungan')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
    protected function getFormSchema(): array
    {
        return (new \App\Traits\HasDashboardFilters)->getFormSchema();
    }
}

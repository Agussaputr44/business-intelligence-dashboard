<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Produk Berdasarkan Penjualan';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = DB::table('fact_sales')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->select('dim_product.product', DB::raw('SUM(fact_sales.sales) as total_sales'))
            ->groupBy('dim_product.product')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return [
            'labels' => $data->pluck('product')->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $data->pluck('total_sales')->toArray(),
                    'backgroundColor' => '#4BC0C0',
                    'borderColor' => '#2C3E50',
                    'borderWidth' => 1,
                ],
            ],
            'options' => [
                'indexAxis' => 'y',
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}

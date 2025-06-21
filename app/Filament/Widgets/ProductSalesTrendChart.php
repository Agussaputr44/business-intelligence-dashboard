<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProductSalesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penjualan Berdasarkan Produk';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = DB::table('fact_sales')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->select('dim_product.product', DB::raw('SUM(fact_sales.sales) as total_sales'))
            ->groupBy('dim_product.product')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data->pluck('total_sales')->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => '#4BC0C0',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('product')->toArray(),
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
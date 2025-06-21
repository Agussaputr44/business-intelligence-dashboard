<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductSalesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penjualan Berdasarkan Produk';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $rawData = DB::table('fact_sales')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->join('dim_time', 'fact_sales.time_id', '=', 'dim_time.time_id')
            ->select(
                'dim_product.product',
                'dim_time.month_name',
                'dim_time.month_number',
                DB::raw('SUM(fact_sales.sales) as total_sales')
            )
            ->groupBy('dim_product.product', 'dim_time.month_name', 'dim_time.month_number')
            ->orderBy('dim_time.month_number')
            ->get();

        // Buat labels berdasarkan month_number urut
        $labels = $rawData
            ->pluck('month_name', 'month_number')
            ->sortKeys()
            ->values()
            ->unique()
            ->toArray();

        if (empty($labels)) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Kelompokkan berdasarkan produk
        $groupedByProduct = $rawData->groupBy('product');

        $datasets = $groupedByProduct->map(function (Collection $records, string $product) use ($labels) {
            $salesPerMonth = $records->keyBy('month_name');

            $runningTotal = 0;
            $monthlySales = [];

            foreach ($labels as $month) {
                $monthly = optional($salesPerMonth->get($month))->total_sales ?? 0;
                $runningTotal += $monthly;
                $monthlySales[] = $runningTotal;
            }

            return [
                'label' => $product,
                'data' => $monthlySales,
                'borderWidth' => 2,
            ];
        })->values()->toArray();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlySalesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penjualan Bulanan';

    protected array $filters = [];

    public function filters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $query = DB::table('fact_sales')
            ->join('dim_time', 'fact_sales.time_id', '=', 'dim_time.time_id')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id');

        // Terapkan filter jika ada
        if (!empty($this->filters['country'])) {
            $query->whereIn('dim_country.country', $this->filters['country']);
        }
        if (!empty($this->filters['year'])) {
            $query->whereIn('dim_time.year', $this->filters['year']);
        }
        if (!empty($this->filters['product'])) {
            $query->whereIn('dim_product.product', $this->filters['product']);
        }
        if (!empty($this->filters['month_name'])) {
            $query->whereIn('dim_time.month_name', $this->filters['month_name']);
        }

        // Ambil data penjualan per bulan dan produk
        $salesData = $query
            ->select(
                'dim_time.month_name',
                'dim_time.month_number',
                'dim_product.product',
                DB::raw('SUM(fact_sales.sales) as total_sales')
            )
            ->groupBy('dim_time.month_name', 'dim_time.month_number', 'dim_product.product')
            ->orderBy('dim_time.month_number')
            ->get();

        // Susun urutan bulan yang muncul dalam data
        $labels = $salesData
            ->pluck('month_name', 'month_number')
            ->sortKeys()
            ->values()
            ->unique()
            ->toArray();

        // Kelompokkan data berdasarkan produk
        $grouped = $salesData->groupBy('product');

        // Buat dataset untuk masing-masing produk
        $datasets = $grouped->map(function ($items, $product) use ($labels) {
            $salesPerMonth = collect($items)->keyBy('month_name');

            return [
                'label' => $product,
                'data' => array_map(fn($month) => $salesPerMonth[$month]->total_sales ?? 0, $labels),
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

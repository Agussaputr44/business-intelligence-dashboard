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

        // Terapkan filter
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

        $data = $query
            ->select('dim_time.month_name', 'dim_time.month_number', DB::raw('SUM(fact_sales.sales) as total_sales'))
            ->groupBy('dim_time.month_name', 'dim_time.month_number')
            ->orderBy('dim_time.month_number')
            ->get()
            ->pluck('total_sales', 'month_name')
            ->toArray();

        $labels = DB::table('dim_time')
            ->when(!empty($this->filters['month_name']), function ($q) {
                $q->whereIn('month_name', $this->filters['month_name']);
            })
            ->pluck('month_name')
            ->unique()
            ->sortBy(function ($month) {
                return array_search(strtolower($month), ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']);
            })
            ->values()
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => array_map(fn($month) => $data[$month] ?? 0, $labels),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
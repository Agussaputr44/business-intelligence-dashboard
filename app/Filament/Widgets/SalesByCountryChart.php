<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Sale; // Pastikan model ini ada atau sesuaikan dengan struktur Anda
use Illuminate\Support\Facades\DB;

class SalesByCountryChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penjualan Berdasarkan Negara';

    protected function getType(): string
    {
        return 'bar'; 
    }

    protected function getData(): array
    {
        // Query untuk mengambil total sales per negara
        $data = Sale::select(
            'dim_country.country',
            DB::raw('SUM(fact_sales.sales) as total_sales')
        )
        ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id')
        ->groupBy('dim_country.country')
        ->orderBy('total_sales', 'desc')
        ->get()
        ->toArray();

        // Ekstrak label dan data untuk chart
        $labels = array_column($data, 'country');
        $sales = array_column($data, 'total_sales');

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $sales,
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'borderColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    // Opsional: Nonaktifkan autentikasi jika ingin publik
    public static function canView(): bool
    {
        return true;
    }
}
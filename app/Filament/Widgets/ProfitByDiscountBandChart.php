<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProfitByDiscountBandChart extends ChartWidget
{
    protected static ?string $heading = 'Proporsi Profit berdasarkan Kelompok Diskon';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = DB::table('fact_sales')
            ->join('dim_discount_band', 'fact_sales.discount_band_id', '=', 'dim_discount_band.discount_band_id')
            ->select('dim_discount_band.discount_band', DB::raw('SUM(fact_sales.profit) as total_profit'))
            ->groupBy('dim_discount_band.discount_band')
            ->orderByRaw("
                FIELD(dim_discount_band.discount_band, 'None', 'Low', 'Medium', 'High')
            ")
            ->get();

        $colorMap = [
            'Low' => '#36A2EB',     // Biru muda
            'Medium' => '#2C3E50',  // Biru gelap
            'High' => '#000000',    // Hitam
            'None' => '#7F8C8D',    // Abu-abu
        ];

        return [
            'labels' => $data->pluck('discount_band')->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Profit',
                    'data' => $data->pluck('total_profit')->toArray(),
                    'backgroundColor' => $data->pluck('discount_band')->map(function ($label) use ($colorMap) {
                        $normalized = ucfirst(strtolower(trim($label)));
                        return $colorMap[$normalized] ?? '#CCCCCC';
                    })->toArray(),
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}

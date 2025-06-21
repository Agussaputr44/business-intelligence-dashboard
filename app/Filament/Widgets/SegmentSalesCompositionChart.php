<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentSalesCompositionChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Penjualan per Segmen Pasar';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = DB::table('fact_sales')
            ->join('dim_segment', 'fact_sales.segment_id', '=', 'dim_segment.segment_id')
            ->select('dim_segment.segment', DB::raw('SUM(fact_sales.sales) as total_sales'))
            ->groupBy('dim_segment.segment')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('total_sales')->toArray(),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                ],
            ],
            'labels' => $data->pluck('segment')->toArray(),
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
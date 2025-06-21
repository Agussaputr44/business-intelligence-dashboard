<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getFilters(): array
    {
        return [
            Select::make('country')
                ->options(DB::table('dim_country')->pluck('country', 'country')->toArray())
                ->multiple()
                ->default(['Canada', 'Germany', 'Mexico', 'United States of America']),
            Select::make('year')
                ->options(DB::table('dim_time')->pluck('year', 'year')->unique()->toArray())
                ->multiple()
                ->default([2013, 2014]),
            Select::make('product')
                ->options(DB::table('dim_product')->pluck('product', 'product')->toArray())
                ->multiple(),
            Select::make('month_name')
                ->options(DB::table('dim_time')->pluck('month_name', 'month_name')->unique()->toArray())
                ->multiple(),
        ];
    }

    // Opsional: Terapkan filter ke widget atau data
    protected function getPages(array $data = []): array
    {
        $filters = $this->getFiltersForm()->getState(); // Ambil state filter dari form
        return [
            // Daftarkan widget Anda di sini, misalnya
            \App\Filament\Widgets\MonthlySalesTrendChart::class,
            \App\Filament\Widgets\SalesByCountryChart::class,
        ];
    }
}
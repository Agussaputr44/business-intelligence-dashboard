<?php

namespace App\Traits;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;

trait HasDashboardFilters
{
    protected function getFormSchema(): array
    {
        return [
            Select::make('year')
                ->label('Tahun')
                ->options(
                    DB::table('dim_time')->select('year')->distinct()->pluck('year', 'year')->toArray()
                )
                ->searchable()
                ->preload(),

            Select::make('month')
                ->label('Bulan')
                ->options(
                    DB::table('dim_time')->select('month_name')->distinct()->pluck('month_name', 'month_name')->toArray()
                )
                ->searchable()
                ->preload(),

            Select::make('product')
                ->label('Produk / Merk')
                ->options(
                    DB::table('dim_product')->select('product')->distinct()->pluck('product', 'product')->toArray()
                )
                ->searchable()
                ->preload(),
        ];
    }

    protected function getFilters(): array
    {
        return $this->form->getState();
    }
}

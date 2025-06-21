<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.dashboard';

    public ?array $filters = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('filters.year')
                ->label('Tahun')
                ->options(DB::table('dim_time')->distinct()->pluck('year', 'year')->toArray())
                ->searchable()
                ->multiple(),

            Select::make('filters.month')
                ->label('Bulan')
                ->options(DB::table('dim_time')->distinct()->pluck('month_name', 'month_name')->toArray())
                ->searchable()
                ->multiple(),

            Select::make('filters.product')
                ->label('Produk')
                ->options(DB::table('dim_product')->distinct()->pluck('product', 'product')->toArray())
                ->searchable()
                ->multiple(),
        ];
    }

    // public function getForm(): Form
    // {
    //     return parent::getForm()
    //         ->schema($this->getFormSchema());
    // }

    public function getHeaderActions(): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return $this->filters ?? [];
    }
}

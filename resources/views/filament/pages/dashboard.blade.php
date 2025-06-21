<x-filament::page>
    <div class="grid grid-cols-2 gap-4">
        @livewire('widgets.total-sales-kpi', ['filters' => $filters])
        @livewire('widgets.total-profit-kpi', ['filters' => $filters])
    </div>

    <div class="grid grid-cols-2 gap-4 mt-6">
        @livewire('widgets.monthly-sales-trend-chart', ['filters' => $filters])
        @livewire('widgets.product-sales-trend-chart', ['filters' => $filters])
    </div>
</x-filament::page>

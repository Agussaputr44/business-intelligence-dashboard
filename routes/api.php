<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/sales-by-month', [DashboardController::class, 'salesByMonth']);
    Route::get('/sales-by-country', [DashboardController::class, 'salesByCountry']);
    Route::get('/sales-by-segment', [DashboardController::class, 'SalesBySegment']);
    Route::get('/profit-by-discount-band', [DashboardController::class, 'profitByDiscountBand']);
    Route::get('/kpi', [DashboardController::class, 'kpiData']);
});

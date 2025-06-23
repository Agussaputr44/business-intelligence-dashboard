<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function salesByMonth()
    {
        $sales = DB::table('fact_sales')
            ->join('dim_time', 'fact_sales.time_id', '=', 'dim_time.time_id')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id')
            ->select(
                'dim_time.year',
                'dim_time.month_name',
                DB::raw('SUM(fact_sales.sales) as total_sales')
            )
            ->groupBy('dim_time.year', 'dim_time.month_name') // Group by both year and month
            ->orderBy('dim_time.year', 'asc') // Sort by year ascending
            ->orderBy('dim_time.month_name', 'asc') // Then sort by month name ascending
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $sales
        ], 200);
    }
    public function salesByProductAndMonth()
    {
        $rawData = DB::table('fact_sales')
            ->join('dim_product', 'fact_sales.product_id', '=', 'dim_product.product_id')
            ->join('dim_time', 'fact_sales.time_id', '=', 'dim_time.time_id')
            ->select(
                'dim_product.product',
                'dim_time.month_name',
                'dim_time.month_number',
                DB::raw('SUM(fact_sales.sales) as total_sales')
            )
            ->groupBy('dim_product.product', 'dim_time.month_name', 'dim_time.month_number')
            ->orderBy('dim_time.month_number')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rawData
        ], 200);
    }
    public function salesByCountry()
    {
        try {
            $data = DB::table('fact_sales')
                ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id')
                ->select(
                    'dim_country.country',
                    DB::raw('SUM(fact_sales.sales) as total_sales')
                )
                ->groupBy('dim_country.country')
                ->orderBy('total_sales', 'desc')
                ->get()
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function profitByDiscountBand()
    {
        $data = DB::table('fact_sales')
            ->join('dim_discount_band', 'fact_sales.discount_band_id', '=', 'dim_discount_band.discount_band_id')
            ->select('dim_discount_band.discount_band', DB::raw('SUM(fact_sales.profit) as total_profit'))
            ->groupBy('dim_discount_band.discount_band')
            ->orderByRaw("FIELD(dim_discount_band.discount_band, 'None', 'Low', 'Medium', 'High')")
            ->get();

        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']; // Colors for pie chart
        $response = $data->map(function ($item, $index) use ($colors) {
            return [
                'discount_band' => $item->discount_band,
                'total_profit' => (float) $item->total_profit,
                'color' => $colors[$index % count($colors)], // Cycle through colors
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }
    public function salesBySegment()
    {
        $data = DB::table('fact_sales')
            ->join('dim_segment', 'fact_sales.segment_id', '=', 'dim_segment.segment_id')
            ->select('dim_segment.segment', DB::raw('SUM(fact_sales.sales) as total_sales'))
            ->groupBy('dim_segment.segment')
            ->get();

        $colors = ['#FF6384', '#36A2EB', '#FFCE56'];
        $response = $data->map(function ($item, $index) use ($colors) {
            return [
                'segment' => $item->segment,
                'total_sales' => (float) $item->total_sales,
                'color' => $colors[$index % count($colors)],
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }
    public function kpiData(Request $request)
    {
        // Fetch Active Countries
        $countries = DB::table('fact_sales')
            ->join('dim_country', 'fact_sales.country_id', '=', 'dim_country.country_id')
            ->distinct('dim_country.country')
            ->count('dim_country.country');

        // Fetch Total Sales
        $totalSales = DB::table('fact_sales')->sum('sales');

        // Fetch Total Profit
        $totalProfit = DB::table('fact_sales')->sum('profit');

        // Fetch Total Products (assuming a products table or dimension)
        $totalProducts = DB::table('dim_product')->count(); // Adjust table name as per your schema

        // Fetch Products Sold (sum of quantities sold)
        $productsSold = DB::table('fact_sales')->sum('sales'); // Adjust column name as per your schema

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_countries' => $countries,
                'total_sales' => number_format($totalSales, 0, ',', '.'),
                'total_profit' => number_format($totalProfit, 0, ',', '.'),
                'total_products' => $totalProducts,
                'products_sold' => $productsSold,
            ],
        ], 200);
    }
}

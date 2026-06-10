<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $totalSales = Sale::count();
    $totalIncome = Sale::sum('total_price');

    $latestSales = Sale::with('customer')
        ->latest()
        ->take(5)
        ->get();

    $lowStockProducts = Product::orderBy('stock', 'asc')
        ->take(5)
        ->get();

   $salesChart = Sale::select(
        DB::raw('DATE(sales_date) as date'),
        DB::raw('SUM(total_price) as total')
    )
    ->groupBy('date')
    ->orderBy('date')
    ->get();


    $criticalStock = Product::where('stock', '<=', 10)->count();

        return view('dashboard', compact(
        'totalCustomers',
        'totalProducts',
        'totalSales',
        'totalIncome',
        'latestSales',
        'lowStockProducts',
        'salesChart',
        'criticalStock'
    ));

}
}
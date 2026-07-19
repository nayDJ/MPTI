<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $currentStart = now()->startOfMonth();
    $currentEnd = now()->endOfMonth();

    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $totalSales = Sale::count();
    
    $lunasIncome = Sale::where('payment_status', 'lunas')
        ->whereBetween('sales_date', [$currentStart, $currentEnd])
        ->sum('total_price');

    $cicilIncome = Sale::where('payment_status', 'cicil')
        ->whereBetween('sales_date', [$currentStart, $currentEnd])
        ->sum('paid_amount');

    $totalIncome = $lunasIncome + $cicilIncome;

    $totalExpense = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
        ->sum('amount');

    $latestSales = Sale::with('customer')
        ->latest()
        ->take(5)
        ->get();

    $lowStockProducts = Product::where('is_active', true)->where('track_stock', true)
        ->orderBy('stock', 'asc')
        ->take(5)
        ->get();

   $periodRange = [];
        for ($date = $currentStart->copy(); $date->lte($currentEnd); $date->addDay()) {
            $periodRange[] = $date->format('Y-m-d');
        }

        $salesRaw = Sale::select(
                DB::raw('DATE(sales_date) as date'),
                DB::raw("SUM(CASE WHEN payment_status = 'lunas' THEN total_price WHEN payment_status = 'cicil' THEN paid_amount ELSE 0 END) as total")
            )
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $expenseRaw = Expense::select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->whereBetween('expense_date', [$currentStart, $currentEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $salesChart = collect($periodRange)->map(fn($date) => [
            'label' => Carbon::parse($date)->format('d M'),
            'total' => $salesRaw->has($date) ? (int) $salesRaw[$date]->total : 0,
        ]);

        $expenseChart = collect($periodRange)->map(fn($date) => [
            'label' => Carbon::parse($date)->format('d M'),
            'total' => $expenseRaw->has($date) ? (int) $expenseRaw[$date]->total : 0,
        ]);

    $criticalStock = Product::where('is_active', true)->where('track_stock', true)->where('stock', '>', 0)->where('stock', '<', 10)->count();

    $totalActiveTracked = Product::where('is_active', true)->where('track_stock', true)->count();
    $stockHabis = Product::where('is_active', true)->where('track_stock', true)->where('stock', '<=', 0)->count();
    $stockKritis = Product::where('is_active', true)->where('track_stock', true)->where('stock', '>', 0)->where('stock', '<', 10)->count();
    $stockMenipis = Product::where('is_active', true)->where('track_stock', true)
        ->where('low_stock_alert_enabled', true)
        ->where('stock', '>=', 10)
        ->whereColumn('stock', '<=', 'low_stock_threshold')
        ->count();
    $stockAman = $totalActiveTracked - $stockHabis - $stockKritis - $stockMenipis;

    $topDebtors = Sale::select('customer_id',
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('SUM(total_price - COALESCE(paid_amount, 0)) as sisa_utang')
        )
        ->whereIn('payment_status', ['belum', 'cicil'])
        ->with('customer')
        ->groupBy('customer_id')
        ->orderByDesc('sisa_utang')
        ->take(5)
        ->get();

    $latestExpenses = Expense::latest('expense_date')->take(5)->get();

        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('dashboard', compact(
        'totalCustomers',
        'totalProducts',
        'totalSales',
        'totalIncome',
        'totalExpense',
        'latestSales',
        'lowStockProducts',
        'salesChart',
        'expenseChart',
        'criticalStock',
        'stockHabis',
        'stockKritis',
        'stockMenipis',
        'stockAman',
        'topDebtors',
        'latestExpenses',
        'customers',
        'products'
    ));

}
}
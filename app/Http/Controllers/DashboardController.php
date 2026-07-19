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
    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $totalSales = Sale::count();
    $lunasIncome = Sale::where('payment_status', 'lunas')
        ->whereMonth('sales_date', now()->month)
        ->whereYear('sales_date', now()->year)
        ->sum('total_price');

    $cicilIncome = Sale::where('payment_status', 'cicil')
        ->whereMonth('sales_date', now()->month)
        ->whereYear('sales_date', now()->year)
        ->sum('paid_amount');

    $totalIncome = $lunasIncome + $cicilIncome;

    $totalExpense = Expense::whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');

    $latestSales = Sale::with('customer')
        ->latest()
        ->take(5)
        ->get();

    $lowStockProducts = Product::orderBy('stock', 'asc')
        ->take(5)
        ->get();

   $salesChart = Sale::select(
        DB::raw('DATE(sales_date) as date'),
        DB::raw("SUM(CASE WHEN payment_status = 'lunas' THEN total_price WHEN payment_status = 'cicil' THEN paid_amount ELSE 0 END) as total")
    )
    ->groupBy('date')
    ->orderBy('date')
    ->get()
    ->transform(fn($d) => [
        'label' => Carbon::parse($d->date)->format('d M'),
        'total' => $d->total,
    ]);

   $expenseChart = Expense::select(
        DB::raw('DATE(expense_date) as date'),
        DB::raw('SUM(amount) as total')
    )
    ->groupBy('date')
    ->orderBy('date')
    ->get()
    ->transform(fn($d) => [
        'label' => Carbon::parse($d->date)->format('d M'),
        'total' => $d->total,
    ]);

    $criticalStock = Product::where('stock', '<=', 10)->count();

    $stockHabis = Product::where('stock', '<=', 0)->count();
    $stockKritis = Product::where('stock', '>', 0)->where('stock', '<', 10)->count();
    $stockMenipis = Product::where('low_stock_alert_enabled', true)
        ->where('stock', '>=', 10)
        ->whereColumn('stock', '<=', 'low_stock_threshold')
        ->count();
    $stockAman = Product::count() - $stockHabis - $stockKritis - $stockMenipis;

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
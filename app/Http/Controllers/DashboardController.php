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
    $totalIncome = Sale::where('payment_status', 'lunas')->sum('total_price');

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
        DB::raw("SUM(CASE WHEN payment_status = 'lunas' THEN total_price ELSE 0 END) as total")
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

    $customers = Customer::orderBy('name')->get(['id', 'name']);
    $products = Product::orderBy('name')->get();

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
        'topDebtors',
        'latestExpenses',
        'customers',
        'products'
    ));

}
}
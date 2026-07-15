<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $totalRevenue = Sale::where('payment_status', 'lunas')->sum('total_price');

        $totalTransactions = Sale::count();

        $totalCustomers = Customer::count();

        $totalProducts = Product::count();

        $totalExpense = Expense::sum('amount');

        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $topCustomers = Sale::select(
                'customer_id',
                DB::raw('COUNT(*) as total_orders')
            )
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_orders')
            ->take(5)
            ->get();

        $monthlySales = Sale::selectRaw(
                "DATE(sales_date) as date,
                SUM(CASE WHEN payment_status = 'lunas' THEN total_price ELSE 0 END) as total"
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->transform(fn($d) => [
                'label' => Carbon::parse($d->date)->format('d M'),
                'total' => $d->total,
            ]);

        $expenseByCategory = Expense::select(
                'category',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $expenses = Expense::latest('expense_date')->paginate(20);

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

        return view('reports.index', compact(
            'totalRevenue',
            'totalTransactions',
            'totalCustomers',
            'totalProducts',
            'totalExpense',
            'topProducts',
            'topCustomers',
            'monthlySales',
            'expenseByCategory',
            'expenses',
            'topDebtors'
        ));
    }
}
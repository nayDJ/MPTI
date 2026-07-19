<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'tahun';
        $view = $request->view ?? 'income';

        if ($period === 'bulan') {
            $currentStart = now()->startOfMonth();
            $currentEnd = now()->endOfMonth();
        } else {
            $currentStart = now()->startOfYear();
            $currentEnd = now()->endOfYear();
        }

        $totalRevenue = Sale::whereBetween('sales_date', [$currentStart, $currentEnd])
            ->selectRaw("SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;

        $totalExpense = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
            ->sum('amount');

        $totalProfit = $totalRevenue - $totalExpense;

        $outstandingReceivables = Sale::whereIn('payment_status', ['belum', 'cicil'])
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->sum(DB::raw('total_price - COALESCE(paid_amount, 0)'));

        $incomeMonthly = Sale::selectRaw("MONTH(sales_date) as month, SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->whereYear('sales_date', now()->year)
            ->groupBy(DB::raw('MONTH(sales_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $expenseMonthly = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', now()->year)
            ->groupBy(DB::raw('MONTH(expense_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $maxIncome = 0;
        $maxExpense = 0;
        $chartIncome = [];
        $chartExpense = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $inc = $incomeMonthly->has($m) ? (int) $incomeMonthly[$m]->total : 0;
            $exp = $expenseMonthly->has($m) ? (int) $expenseMonthly[$m]->total : 0;
            $chartIncome[] = $inc;
            $chartExpense[] = $exp;
            if ($inc > $maxIncome) $maxIncome = $inc;
            if ($exp > $maxExpense) $maxExpense = $exp;
        }

        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $topCustomers = Sale::select(
                'customer_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

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

        $recentSales = Sale::with('customer')
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->latest('sales_date')
            ->take(3)
            ->get();

        $recentExpenses = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
            ->latest('expense_date')
            ->take(3)
            ->get();

        $totalUnitsSold = SaleItem::whereHas('sale', function ($q) use ($currentStart, $currentEnd) {
                $q->whereBetween('sales_date', [$currentStart, $currentEnd]);
            })
            ->sum('quantity');

        $currentMonth = (int) now()->format('n');

        $periodLabel = $period === 'bulan'
            ? now()->isoFormat('MMMM YYYY')
            : 'Tahun ' . now()->year;

        return view('reports.index', compact(
            'totalRevenue',
            'totalProfit',
            'outstandingReceivables',
            'totalExpense',
            'chartIncome', 'chartExpense',
            'maxIncome', 'maxExpense',
            'monthNames',
            'topProducts', 'topCustomers', 'topDebtors',
            'recentSales', 'recentExpenses',
            'totalUnitsSold',
            'period', 'view',
            'currentMonth',
            'periodLabel'
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->period ?? 'tahun';
        $view = $request->view ?? 'income';

        if ($period === 'bulan') {
            $currentStart = now()->startOfMonth();
            $currentEnd = now()->endOfMonth();
        } else {
            $currentStart = now()->startOfYear();
            $currentEnd = now()->endOfYear();
        }

        $totalRevenue = Sale::whereBetween('sales_date', [$currentStart, $currentEnd])
            ->selectRaw("SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;

        $totalExpense = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
            ->sum('amount');

        $totalProfit = $totalRevenue - $totalExpense;

        $outstandingReceivables = Sale::whereIn('payment_status', ['belum', 'cicil'])
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->sum(DB::raw('total_price - COALESCE(paid_amount, 0)'));

        $incomeMonthly = Sale::selectRaw("MONTH(sales_date) as month, SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->whereYear('sales_date', now()->year)
            ->groupBy(DB::raw('MONTH(sales_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $expenseMonthly = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', now()->year)
            ->groupBy(DB::raw('MONTH(expense_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $maxIncome = 0;
        $maxExpense = 0;
        $chartIncome = [];
        $chartExpense = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($m = 1; $m <= 12; $m++) {
            $inc = $incomeMonthly->has($m) ? (int) $incomeMonthly[$m]->total : 0;
            $exp = $expenseMonthly->has($m) ? (int) $expenseMonthly[$m]->total : 0;
            $chartIncome[] = $inc;
            $chartExpense[] = $exp;
            if ($inc > $maxIncome) $maxIncome = $inc;
            if ($exp > $maxExpense) $maxExpense = $exp;
        }

        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $topCustomers = Sale::select('customer_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_price) as total_revenue'))
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        $topDebtors = Sale::select('customer_id', DB::raw('COUNT(*) as total_transaksi'), DB::raw('SUM(total_price - COALESCE(paid_amount, 0)) as sisa_utang'))
            ->whereIn('payment_status', ['belum', 'cicil'])
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('sisa_utang')
            ->take(5)
            ->get();

        $totalUnitsSold = SaleItem::whereHas('sale', function ($q) use ($currentStart, $currentEnd) {
            $q->whereBetween('sales_date', [$currentStart, $currentEnd]);
        })->sum('quantity');

        $currentMonth = (int) now()->format('n');

        $periodLabel = $period === 'bulan'
            ? now()->isoFormat('MMMM YYYY')
            : 'Tahun ' . now()->year;

        $sales = Sale::with('customer')
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->orderBy('sales_date', 'desc')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
            ->orderBy('expense_date', 'desc')
            ->get();

        $data = compact(
            'period', 'view', 'periodLabel',
            'totalRevenue', 'totalExpense', 'totalProfit', 'outstandingReceivables',
            'chartIncome', 'chartExpense', 'maxIncome', 'maxExpense', 'monthNames', 'currentMonth',
            'topProducts', 'topCustomers', 'topDebtors', 'totalUnitsSold',
            'sales', 'expenses'
        );

        $pdf = Pdf::loadView('reports.pdf', $data);

        $filename = 'laporan-sistem-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}

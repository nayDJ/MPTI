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
        $d = $this->reportData($period);

        $recentSales = Sale::with('customer')
            ->whereBetween('sales_date', [$d->currentStart, $d->currentEnd])
            ->latest('sales_date')->take(3)->get();

        $recentExpenses = Expense::whereBetween('expense_date', [$d->currentStart, $d->currentEnd])
            ->latest('expense_date')->take(3)->get();

        return view('reports.index', [
            'period' => $period, 'view' => $view,
            'recentSales' => $recentSales, 'recentExpenses' => $recentExpenses,
        ] + (array) $d);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->period ?? 'tahun';
        $view = $request->view ?? 'income';
        $d = $this->reportData($period);

        $sales = Sale::with('customer')
            ->whereBetween('sales_date', [$d->currentStart, $d->currentEnd])
            ->orderBy('sales_date', 'desc')->get();

        $expenses = Expense::whereBetween('expense_date', [$d->currentStart, $d->currentEnd])
            ->orderBy('expense_date', 'desc')->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'period' => $period, 'view' => $view,
            'sales' => $sales, 'expenses' => $expenses,
        ] + (array) $d);

        return $pdf->download('laporan-sistem-' . now()->format('Y-m-d') . '.pdf');
    }

    private function reportData(string $period): object
    {
        if ($period === 'bulan') {
            $currentStart = now()->startOfMonth();
            $currentEnd = now()->endOfMonth();
        } else {
            $currentStart = now()->startOfYear();
            $currentEnd = now()->endOfYear();
        }

        $totalRevenue = Sale::whereBetween('sales_date', [$currentStart, $currentEnd])
            ->collectableRevenue()
            ->value('total') ?? 0;

        $totalExpense = Expense::whereBetween('expense_date', [$currentStart, $currentEnd])
            ->sum('amount');

        $totalProfit = $totalRevenue - $totalExpense;

        $outstandingReceivables = Sale::whereIn('payment_status', ['belum', 'cicil'])
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
            ->sum(DB::raw('total_price - COALESCE(paid_amount, 0)'));

        $incomeMonthly = Sale::selectRaw("MONTH(sales_date) as month")
            ->selectRaw("SUM(CASE WHEN payment_status = 'lunas' THEN total_price WHEN payment_status = 'cicil' THEN paid_amount ELSE 0 END) as total")
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

        // ponytail: O(n²) top products, ganti query cache kalau produk > 100
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

        // ponytail: grouped nullable customer_id jadi 1 grup — upgrade kalau customer required
        $topDebtors = Sale::select('customer_id', DB::raw('COUNT(*) as total_transaksi'), DB::raw('SUM(total_price - COALESCE(paid_amount, 0)) as sisa_utang'))
            ->whereIn('payment_status', ['belum', 'cicil'])
            ->whereBetween('sales_date', [$currentStart, $currentEnd])
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

        return (object) compact(
            'currentStart', 'currentEnd', 'periodLabel', 'currentMonth',
            'totalRevenue', 'totalExpense', 'totalProfit', 'outstandingReceivables',
            'chartIncome', 'chartExpense', 'maxIncome', 'maxExpense', 'monthNames',
            'topProducts', 'topCustomers', 'topDebtors', 'totalUnitsSold'
        );
    }
}

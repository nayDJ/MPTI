<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    private function filterExpenses(Request $request)
    {
        $period = $request->period;
        $from = $request->from;
        $to = $request->to;

        if ($period === 'hari_ini') {
            $from = $to = now()->toDateString();
        } elseif ($period === 'minggu_ini') {
            $from = now()->startOfWeek()->toDateString();
            $to = now()->endOfWeek()->toDateString();
        } elseif ($period === 'bulan_ini') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        }

        return Expense::query()
            ->when($request->search, fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($from, fn($q, $f) => $q->whereDate('expense_date', '>=', $f))
            ->when($to, fn($q, $t) => $q->whereDate('expense_date', '<=', $t));
    }

    public function index(Request $request)
    {
        $query = $this->filterExpenses($request);

        $expenses = (clone $query)->latest('expense_date')->paginate(10)->withQueryString();
        $totalExpense = (clone $query)->sum('amount');
        $totalItems = (clone $query)->count();

        $currentMonthExpense = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)->sum('amount');
        $lastMonthExpense = Expense::whereMonth('expense_date', now()->subMonth()->month)
            ->whereYear('expense_date', now()->subMonth()->year)->sum('amount');
        $expenseGrowth = $lastMonthExpense > 0
            ? round(($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense * 100)
            : 0;

        $categoryDistribution = (clone $query)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        $categories = Category::orderBy('name')->get();
        $highestExpense = (clone $query)->orderByDesc('amount')->first();
        $averageDaily = $totalItems > 0 ? round($totalExpense / $totalItems) : 0;

        return view('expenses.index', [
            'expenses' => $expenses,
            'totalExpense' => $totalExpense,
            'totalItems' => $totalItems,
            'expenseGrowth' => $expenseGrowth,
            'categoryDistribution' => $categoryDistribution,
            'categories' => $categories,
            'highestExpense' => $highestExpense,
            'averageDaily' => $averageDaily,
            'search' => $request->search,
            'category' => $request->category,
            'period' => $request->period,
            'from' => $request->from,
            'to' => $request->to,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::create($data);

        Category::firstOrCreate(['name' => $request->category]);

        Notification::create([
            'type' => Notification::TYPE_SUCCESS,
            'title' => 'Pengeluaran Baru',
            'message' => $expense->description . ' — Rp ' . number_format($expense->amount) . ' oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_EXPENSE_CREATE,
            'notifiable_id' => $expense->id,
            'notifiable_type' => Expense::class,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
        ]);

        $expense->update($data);

        Category::firstOrCreate(['name' => $request->category]);

        Notification::create([
            'type' => Notification::TYPE_INFO,
            'title' => 'Pengeluaran Diupdate',
            'message' => $expense->description . ' berhasil diperbarui oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_EXPENSE_UPDATE,
            'notifiable_id' => $expense->id,
            'notifiable_type' => Expense::class,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    public function destroy(Expense $expense)
    {
        $desc = $expense->description;
        $expense->delete();

        Notification::create([
            'type' => Notification::TYPE_ERROR,
            'title' => 'Pengeluaran Dihapus',
            'message' => $desc . ' berhasil dihapus oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_EXPENSE_DELETE,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $expenses = (clone $this->filterExpenses($request))->latest('expense_date')->get();
        $totalExpense = $expenses->sum('amount');

        $pdf = Pdf::loadView('expenses.pdf', [
            'expenses' => $expenses,
            'totalExpense' => $totalExpense,
            'search' => $request->search,
            'category' => $request->category,
            'from' => $request->from,
            'to' => $request->to,
        ]);
        return $pdf->download('laporan-pengeluaran.pdf');
    }
}

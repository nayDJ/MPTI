<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
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

        $query = Expense::query()
            ->when($search, fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->when($from, fn($q, $f) => $q->whereDate('expense_date', '>=', $f))
            ->when($to, fn($q, $t) => $q->whereDate('expense_date', '<=', $t));

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

        return view('expenses.index', compact(
            'expenses', 'totalExpense', 'totalItems', 'expenseGrowth',
            'categoryDistribution', 'categories', 'highestExpense', 'averageDaily',
            'search', 'category', 'period', 'from', 'to'
        ));
    }

    public function create()
    {
        return redirect()->route('expenses.index');
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
            'type' => 'success',
            'title' => 'Pengeluaran Baru',
            'message' => $expense->description . ' — Rp ' . number_format($expense->amount),
            'action_type' => 'expense.create',
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

    public function edit(Expense $expense)
    {
        return redirect()->route('expenses.index');
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
            'type' => 'info',
            'title' => 'Pengeluaran Diupdate',
            'message' => $expense->description . ' berhasil diperbarui',
            'action_type' => 'expense.update',
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
            'type' => 'error',
            'title' => 'Pengeluaran Dihapus',
            'message' => $desc . ' berhasil dihapus',
            'action_type' => 'expense.delete',
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
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

        $query = Expense::query()
            ->when($search, fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->when($from, fn($q, $f) => $q->whereDate('expense_date', '>=', $f))
            ->when($to, fn($q, $t) => $q->whereDate('expense_date', '<=', $t));

        $expenses = (clone $query)->latest('expense_date')->get();
        $totalExpense = (clone $query)->sum('amount');

        $pdf = Pdf::loadView('expenses.pdf', compact(
            'expenses', 'totalExpense', 'search', 'category', 'from', 'to'
        ));
        return $pdf->download('laporan-pengeluaran.pdf');
    }
}

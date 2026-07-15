<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
        $period = $request->period;
        $from = $request->from;
        $to = $request->to;

        if ($period === 'harian') {
            $from = $to = now()->toDateString();
        } elseif ($period === 'bulanan') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        } elseif ($period === 'tahunan') {
            $from = now()->startOfYear()->toDateString();
            $to = now()->endOfYear()->toDateString();
        }

        $expenses = Expense::when($search, function ($q, $search) {
            $q->where('description', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->when($from, function ($q, $from) {
            $q->whereDate('expense_date', '>=', $from);
        })->when($to, function ($q, $to) {
            $q->whereDate('expense_date', '<=', $to);
        })->latest('expense_date')->paginate(10)->withQueryString();

        $totalExpense = Expense::when($search, function ($q, $search) {
            $q->where('description', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->when($from, function ($q, $from) {
            $q->whereDate('expense_date', '>=', $from);
        })->when($to, function ($q, $to) {
            $q->whereDate('expense_date', '<=', $to);
        })->sum('amount');

        $totalItems = Expense::when($search, function ($q, $search) {
            $q->where('description', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->when($from, function ($q, $from) {
            $q->whereDate('expense_date', '>=', $from);
        })->when($to, function ($q, $to) {
            $q->whereDate('expense_date', '<=', $to);
        })->count();

        $categories = Category::orderBy('name')->get();

        return view('expenses.index', compact(
            'expenses',
            'totalExpense',
            'totalItems',
            'categories',
            'search',
            'category',
            'period',
            'from',
            'to'
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

        Expense::create($data);

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

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
}

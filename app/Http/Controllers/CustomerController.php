<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $customers = Customer::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        $totalCustomers = Customer::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        })->count();

        $newCustomers = Customer::whereDate('created_at', now()->toDateString())->count();

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'newCustomers',
            'search'
        ));
    }
    public function create()
    {
        return redirect()->route('customers.index');
    }

    public function show(Customer $customer)
    {
        $customer->load('sales.items.product');

        $totalTransactions = $customer->sales->count();
        $totalPurchase = $customer->sales->sum('total_price');
        $totalPaid = $customer->sales->sum('paid_amount');
        $totalDebt = $totalPurchase - $totalPaid;

        $sales = $customer->sales()->latest()->with('items.product')->paginate(15);

        return view('customers.show', compact(
            'customer',
            'totalTransactions',
            'totalPurchase',
            'totalPaid',
            'totalDebt',
            'sales'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan');
    }

    public function edit(Customer $customer)
    {
        return redirect()->route('customers.index');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $customer->update($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
        ]);
    }
}
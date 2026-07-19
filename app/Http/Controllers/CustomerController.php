<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Notification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $is_active = $request->is_active;
        $debt_status = $request->debt_status;

        $customers = Customer::withSum(['sales as total_purchase'], 'total_price')
            ->withSum(['sales as total_paid'], 'paid_amount')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            })->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
                $q->where('is_active', $is_active);
            })->when($debt_status === 'hutang', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) > (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
            ->when($debt_status === 'lunas', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) <= (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
            ->latest()->paginate(10)->withQueryString();

        $totalCustomers = Customer::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        })->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
            $q->where('is_active', $is_active);
        })->when($debt_status === 'hutang', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) > (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
        ->when($debt_status === 'lunas', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) <= (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
        ->count();

        $newCustomers = Customer::whereDate('created_at', now()->toDateString())->count();

        $totalTransactions = Sale::whereHas('customer', function ($q) use ($search, $is_active, $debt_status) {
            $q->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            })->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
                $q->where('is_active', $is_active);
            })->when($debt_status === 'hutang', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) > (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)');
            })->when($debt_status === 'lunas', function ($q) {
                $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) <= (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)');
            });
        })->count();

        $debtorCount = Customer::whereHas('sales', function ($q) {
            $q->whereColumn('total_price', '>', 'paid_amount');
        })->when($search, fn($q) => $q->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
        }))->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
            $q->where('is_active', $is_active);
        })->count();

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'newCustomers',
            'totalTransactions',
            'debtorCount',
            'search',
            'is_active',
            'debt_status'
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
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);

        Notification::create([
            'type' => 'success',
            'title' => 'Pelanggan Baru',
            'message' => $customer->name . ' — ' . ($customer->phone ?: '-') . ' oleh ' . auth()->user()->name,
            'action_type' => 'customer.create',
            'notifiable_id' => $customer->id,
            'notifiable_type' => Customer::class,
        ]);

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
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);

        Notification::create([
            'type' => 'info',
            'title' => 'Pelanggan Diupdate',
            'message' => $customer->name . ' berhasil diperbarui oleh ' . auth()->user()->name,
            'action_type' => 'customer.update',
            'notifiable_id' => $customer->id,
            'notifiable_type' => Customer::class,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil diupdate');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();

        Notification::create([
            'type' => 'error',
            'title' => 'Pelanggan Dihapus',
            'message' => $name . ' berhasil dihapus oleh ' . auth()->user()->name,
            'action_type' => 'customer.delete',
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }

    public function toggleStatus(Customer $customer)
    {
        if ($customer->is_active) {
            $totalDebt = $customer->sales()
                ->where(function ($q) {
                    $q->where('payment_status', 'belum')
                      ->orWhere(function ($q2) {
                          $q2->where('payment_status', 'cicil')
                             ->whereRaw('total_price > paid_amount');
                      });
                })
                ->selectRaw('COALESCE(SUM(total_price - paid_amount), 0) as debt')
                ->value('debt');

            if ($totalDebt > 0) {
                return back()->with('error',
                    'Tidak dapat menonaktifkan pelanggan karena masih memiliki piutang sebesar Rp ' . number_format($totalDebt));
            }
        }

        $customer->update(['is_active' => !$customer->is_active]);

        Notification::create([
            'type' => 'info',
            'title' => 'Status Pelanggan',
            'message' => $customer->name . ' ' . ($customer->is_active ? 'diaktifkan' : 'dinonaktifkan') . ' oleh ' . auth()->user()->name,
            'action_type' => 'customer.toggle',
            'notifiable_id' => $customer->id,
            'notifiable_type' => Customer::class,
        ]);

        return back()->with('success', 'Status customer berhasil diubah');
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;
        $is_active = $request->is_active;
        $debt_status = $request->debt_status;

        $customers = Customer::withCount('sales')
            ->withSum(['sales as total_purchase'], 'total_price')
            ->withSum(['sales as total_paid'], 'paid_amount')
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")
                ->orWhere('address', 'like', "%{$s}%"))
            ->when($is_active !== null && $is_active !== '', fn($q) => $q->where('is_active', $is_active))
            ->when($debt_status === 'hutang', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) > (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
            ->when($debt_status === 'lunas', fn($q) => $q->whereRaw('(SELECT COALESCE(SUM(s.total_price), 0) FROM sales s WHERE s.customer_id = customers.id) <= (SELECT COALESCE(SUM(s.paid_amount), 0) FROM sales s WHERE s.customer_id = customers.id)'))
            ->latest()->get();

        $pdf = Pdf::loadView('customers.pdf', compact('customers', 'search', 'is_active', 'debt_status'));
        return $pdf->download('laporan-pelanggan.pdf');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $from = $request->from;
        $to = $request->to;
        $period = $request->period;

        if ($period === 'harian') {
            $from = $to = now()->toDateString();
        } elseif ($period === 'bulanan') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        } elseif ($period === 'tahunan') {
            $from = now()->startOfYear()->toDateString();
            $to = now()->endOfYear()->toDateString();
        }

        $query = Sale::with('customer');

        if ($search) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('payment_status', $status);
        }

        if ($from) {
            $query->whereDate('sales_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('sales_date', '<=', $to);
        }

        $sales = $query->latest()->paginate(10)->withQueryString();

        $statQuery = Sale::query()
            ->when($from, fn($q) => $q->whereDate('sales_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sales_date', '<=', $to));

        $totalSales = (clone $statQuery)->count();
        $totalRevenue = (clone $statQuery)
            ->selectRaw("SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;
        $totalDebt = (clone $statQuery)
            ->selectRaw("SUM(CASE WHEN payment_status = 'belum' THEN total_price WHEN payment_status = 'cicil' THEN total_price - paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;

        $todaySales = Sale::whereDate('sales_date', now()->toDateString())->count();

        $debtorCount = (clone $statQuery)
            ->where(function ($q) {
                $q->where('payment_status', 'belum')
                  ->orWhere(function ($q2) {
                      $q2->where('payment_status', 'cicil')
                         ->whereRaw('paid_amount < total_price');
                  });
            })->count();

        $currentMonthRevenue = Sale::whereMonth('sales_date', now()->month)
            ->whereYear('sales_date', now()->year)
            ->selectRaw("SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;

        $lastMonthRevenue = Sale::whereMonth('sales_date', now()->subMonth()->month)
            ->whereYear('sales_date', now()->subMonth()->year)
            ->selectRaw("SUM(CASE payment_status WHEN 'lunas' THEN total_price WHEN 'cicil' THEN paid_amount ELSE 0 END) as total")
            ->value('total') ?? 0;

        $revenueGrowth = $lastMonthRevenue > 0
            ? round(($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100)
            : 0;

        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('sales.index', compact(
            'sales',
            'totalSales',
            'totalRevenue',
            'totalDebt',
            'todaySales',
            'debtorCount',
            'revenueGrowth',
            'customers',
            'products',
            'search',
            'status',
            'from',
            'to',
            'period'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('sales.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'sales_date'  => 'required|date',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_status' => 'required|in:belum,lunas,cicil',
            'paid_amount' => 'nullable|numeric|min:0',
        ];

        if ($request->customer_id === 'NEW') {
            $rules['new_customer_name'] = 'required|string|max:255';
            $rules['new_customer_phone'] = 'nullable|string|max:20';
            $rules['new_customer_address'] = 'nullable|string';
        } else {
            $rules['customer_id'] = 'required|exists:customers,id';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->customer_id === 'NEW') {
                $customer = Customer::create([
                    'name' => $request->new_customer_name,
                    'phone' => $request->new_customer_phone ?? '',
                    'address' => $request->new_customer_address ?? '',
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $request->customer_id;
            }

            $total = 0;
            $itemsData = [];

            foreach ($request->items as $i => $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($item['quantity'] > $product->stock) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->withErrors(["items.{$i}.quantity" => "Stok {$product->name} hanya {$product->stock}"]);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $product->decrement('stock', $item['quantity']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            $paidAmount = $request->payment_status === 'belum' ? 0
                : ($request->payment_status === 'lunas' ? $total
                : ($request->paid_amount ?? 0));

            $sale = Sale::create([
                'customer_id' => $customerId,
                'total_price' => $total,
                'sales_date' => $request->sales_date,
                'payment_status' => $request->payment_status,
                'paid_amount' => $paidAmount,
            ]);

            foreach ($itemsData as $data) {
                $sale->items()->create($data);
            }

            DB::commit();

            $customerName = $sale->customer->name ?? 'Pelanggan';

            Notification::create([
                'type' => 'success',
                'title' => 'Penjualan Baru',
                'message' => '#NQ-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT) . ' an. ' . $customerName . ' — Rp ' . number_format($sale->total_price),
                'action_type' => 'sale.create',
                'notifiable_id' => $sale->id,
                'notifiable_type' => Sale::class,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Transaksi gagal, silakan coba lagi');
        }

        $redirect = $request->redirect_to === 'dashboard' ? 'dashboard' : 'sales.index';

        return redirect()
            ->route($redirect)
            ->with('success', 'Transaksi berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load([
            'customer',
            'items.product'
        ]);

        return view(
            'sales.show',
            compact('sale')
        );
    }

    /**
     * Export sales data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $from = $request->from;
        $to = $request->to;

        $period = $request->period;

        if ($period === 'harian') {
            $from = $to = now()->toDateString();
        } elseif ($period === 'bulanan') {
            $from = now()->startOfMonth()->toDateString();
            $to = now()->endOfMonth()->toDateString();
        } elseif ($period === 'tahunan') {
            $from = now()->startOfYear()->toDateString();
            $to = now()->endOfYear()->toDateString();
        }

        $query = Sale::with('customer');

        if ($search) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('payment_status', $status);
        }

        if ($from) {
            $query->whereDate('sales_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('sales_date', '<=', $to);
        }

        $sales = $query->latest()->get();

        $totalRevenue = $sales->sum(function ($sale) {
            return $sale->payment_status === 'lunas' ? $sale->total_price
                : ($sale->payment_status === 'cicil' ? ($sale->paid_amount ?? 0) : 0);
        });

        $pdf = Pdf::loadView('sales.pdf', compact('sales', 'totalRevenue', 'search', 'status', 'from', 'to', 'period'));

        return $pdf->download('laporan-penjualan.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:belum,lunas,cicil',
            'additional_payment' => 'nullable|numeric|min:0',
        ]);

        if ($data['payment_status'] === 'lunas') {
            $data['paid_amount'] = $sale->total_price;
        } elseif ($data['payment_status'] === 'belum') {
            $data['paid_amount'] = 0;
        } else {
            $additional = $data['additional_payment'] ?? 0;
            $data['paid_amount'] = min(
                ($sale->paid_amount ?? 0) + $additional,
                $sale->total_price
            );
            if ((float) $data['paid_amount'] >= (float) $sale->total_price) {
                $data['payment_status'] = 'lunas';
            }
        }

        $sale->update([
            'payment_status' => $data['payment_status'],
            'paid_amount' => $data['paid_amount'],
        ]);

        Notification::create([
            'type' => 'info',
            'title' => 'Status Bayar Diubah',
            'message' => '#NQ-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT) . ' → ' . $data['payment_status'],
            'action_type' => 'sale.update',
            'notifiable_id' => $sale->id,
            'notifiable_type' => Sale::class,
        ]);

        return redirect()
            ->route('sales.index')
            ->with('success', 'Status pembayaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $id = $sale->id;
        $sale->items()->delete();
        $sale->delete();

        Notification::create([
            'type' => 'error',
            'title' => 'Penjualan Dihapus',
            'message' => '#NQ-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' berhasil dihapus',
            'action_type' => 'sale.delete',
        ]);

        return redirect()
            ->route('sales.index')
            ->with('success', 'Penjualan berhasil dihapus');
    }
}
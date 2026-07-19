<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
        $is_active = $request->is_active;

        $products = Product::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
            $q->where('is_active', $is_active);
        })->latest()->paginate(10)->withQueryString();

        $totalProducts = Product::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->when($is_active !== null && $is_active !== '', function ($q) use ($is_active) {
            $q->where('is_active', $is_active);
        })->count();

        $totalItems = Product::sum('stock');
        $lowStockCount = $is_active === '0' ? 0 : Product::where('is_active', true)->where('stock', '>', 0)
            ->where(function ($q) {
                $q->where('stock', '<', 10)
                  ->orWhere(function ($sub) {
                      $sub->where('low_stock_alert_enabled', true)
                          ->whereColumn('stock', '<=', 'low_stock_threshold');
                  });
            })
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->count();
        $criticalStockCount = $is_active === '0' ? 0 : Product::where('is_active', true)->where('stock', '>', 0)->where('stock', '<', 10)
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->count();
        $outOfStockCount = $is_active === '0' ? 0 : Product::where('is_active', true)->where('stock', '<=', 0)
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->count();
        $totalValuation = $is_active === '0' ? 0 : Product::where('is_active', true)
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->selectRaw('SUM(stock * price) as total')->value('total') ?? 0;
        $categories = Product::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('products.index', compact(
            'products',
            'totalProducts',
            'totalItems',
            'lowStockCount',
            'criticalStockCount',
            'outOfStockCount',
            'totalValuation',
            'categories',
            'search',
            'category',
            'is_active'
        ));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:100',
            'low_stock_alert_enabled' => 'nullable|boolean',
        ]);

        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 30;
        $data['low_stock_alert_enabled'] = $request->boolean('low_stock_alert_enabled');

        $product = Product::create($data);

        Notification::create([
            'type' => 'success',
            'title' => 'Produk Baru',
            'message' => $product->name . ' — Rp ' . number_format($product->price),
            'action_type' => 'product.create',
            'notifiable_id' => $product->id,
            'notifiable_type' => Product::class,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
        public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:100',
            'low_stock_alert_enabled' => 'nullable|boolean',
        ]);

        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 30;
        $data['low_stock_alert_enabled'] = $request->boolean('low_stock_alert_enabled');

        $product->update($data);

        Notification::create([
            'type' => 'info',
            'title' => 'Produk Diupdate',
            'message' => $product->name . ' berhasil diperbarui',
            'action_type' => 'product.update',
            'notifiable_id' => $product->id,
            'notifiable_type' => Product::class,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        Notification::create([
            'type' => 'error',
            'title' => 'Produk Dihapus',
            'message' => $name . ' berhasil dihapus',
            'action_type' => 'product.delete',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function toggleStatus(Product $product)
    {
        $status = !$product->is_active;
        $product->update(['is_active' => $status]);

        Notification::create([
            'type' => 'info',
            'title' => 'Status Produk',
            'message' => $product->name . ' ' . ($status ? 'diaktifkan' : 'dinonaktifkan'),
            'action_type' => 'product.toggle',
            'notifiable_id' => $product->id,
            'notifiable_type' => Product::class,
        ]);

        return redirect()->back()
            ->with('success', 'Status produk berhasil diperbarui.');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
        $is_active = $request->is_active;

        $products = Product::when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->when($is_active !== null && $is_active !== '', fn($q) => $q->where('is_active', $is_active))
            ->latest()->get();

        $totalValuation = $products->sum(fn($p) => $p->stock * $p->price);

        $pdf = Pdf::loadView('products.pdf', compact('products', 'totalValuation', 'search', 'category'));
        return $pdf->download('laporan-produk.pdf');
    }
}

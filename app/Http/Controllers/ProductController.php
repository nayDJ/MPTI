<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    private function filterProducts(Request $request)
    {
        $search = $request->search;
        $category = $request->category;
        $is_active = $request->is_active;

        return Product::when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($category, fn($q, $c) => $q->where('category', $c))
            ->when($is_active !== null && $is_active !== '', fn($q) => $q->where('is_active', $is_active));
    }

    public function index(Request $request)
    {
        $category = $request->category;

        $products = ($this->filterProducts($request))->latest()->paginate(10)->withQueryString();
        $totalProducts = ($this->filterProducts($request))->count();

        $totalItems = Product::where('track_stock', true)->sum('stock');

        // ponytail: consolidate 4 queries → 1 selectRaw with multiple aggregates
        if ($request->is_active === '0') {
            $lowStockCount = 0;
            $criticalStockCount = 0;
            $outOfStockCount = 0;
            $totalValuation = 0;
        } else {
            $stats = Product::where('is_active', true)
                ->where('track_stock', true)
                ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->when($category, fn($q, $c) => $q->where('category', $c))
                ->selectRaw('
                    SUM(CASE WHEN low_stock_alert_enabled = 1 AND stock >= 10 AND stock <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock_count,
                    SUM(CASE WHEN stock > 0 AND stock < 10 THEN 1 ELSE 0 END) as critical_stock_count,
                    SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                    SUM(stock * price) as total_valuation
                ')
                ->first();

            $lowStockCount = $stats->low_stock_count ?? 0;
            $criticalStockCount = $stats->critical_stock_count ?? 0;
            $outOfStockCount = $stats->out_of_stock_count ?? 0;
            $totalValuation = $stats->total_valuation ?? 0;
        }

        $categories = Product::select('category')->whereNotNull('category')->distinct()->pluck('category');
        $allProducts = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'stock', 'track_stock']);

        return view('products.index', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalItems' => $totalItems,
            'lowStockCount' => $lowStockCount,
            'criticalStockCount' => $criticalStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalValuation' => $totalValuation,
            'categories' => $categories,
            'allProducts' => $allProducts,
            'search' => $request->search,
            'category' => $category,
            'is_active' => $request->is_active,
        ]);
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
            'stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:100',
            'low_stock_alert_enabled' => 'nullable|boolean',
            'track_stock' => 'nullable|boolean',
        ]);

        $data['track_stock'] = $request->boolean('track_stock');
        $data['stock'] = $data['track_stock'] ? ($data['stock'] ?? 0) : 0;
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 30;
        $data['low_stock_alert_enabled'] = $data['track_stock'] && $request->boolean('low_stock_alert_enabled');

        $product = Product::create($data);

        if ($request->has('components')) {
            foreach ($request->components as $comp) {
                $product->components()->create([
                    'component_product_id' => $comp['product_id'],
                    'quantity' => $comp['quantity'] ?? 1,
                ]);
            }
        }

        Notification::create([
            'type' => Notification::TYPE_SUCCESS,
            'title' => 'Produk Baru',
            'message' => $product->name . ' — Rp ' . number_format($product->price) . ' oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_PRODUCT_CREATE,
            'notifiable_id' => $product->id,
            'notifiable_type' => Product::class,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:100',
            'low_stock_alert_enabled' => 'nullable|boolean',
            'track_stock' => 'nullable|boolean',
        ]);

        $data['track_stock'] = $request->boolean('track_stock');
        $data['stock'] = $data['track_stock'] ? ($data['stock'] ?? 0) : 0;
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 30;
        $data['low_stock_alert_enabled'] = $data['track_stock'] && $request->boolean('low_stock_alert_enabled');

        $product->update($data);

        $product->components()->delete();
        if ($request->has('components')) {
            foreach ($request->components as $comp) {
                $product->components()->create([
                    'component_product_id' => $comp['product_id'],
                    'quantity' => $comp['quantity'] ?? 1,
                ]);
            }
        }

        Notification::create([
            'type' => 'info',
            'title' => 'Produk Diupdate',
            'message' => $product->name . ' berhasil diperbarui oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_PRODUCT_UPDATE,
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
            'type' => Notification::TYPE_ERROR,
            'title' => 'Produk Dihapus',
            'message' => $name . ' berhasil dihapus oleh ' . auth()->user()->name,
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
            'message' => $product->name . ' ' . ($status ? 'diaktifkan' : 'dinonaktifkan') . ' oleh ' . auth()->user()->name,
            'action_type' => Notification::ACTION_PRODUCT_TOGGLE,
            'notifiable_id' => $product->id,
            'notifiable_type' => Product::class,
        ]);

        return redirect()->back()
            ->with('success', 'Status produk berhasil diperbarui.');
    }

    public function exportPdf(Request $request)
    {
        $products = ($this->filterProducts($request))->latest()->get();
        $totalValuation = $products->sum(fn($p) => $p->stock * $p->price);

        $pdf = Pdf::loadView('products.pdf', [
            'products' => $products,
            'totalValuation' => $totalValuation,
            'search' => $request->search,
            'category' => $request->category,
        ]);
        return $pdf->download('laporan-produk.pdf');
    }
}

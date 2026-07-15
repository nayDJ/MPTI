<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;

        $products = Product::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->latest()->paginate(10)->withQueryString();

        $totalProducts = Product::when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%");
        })->when($category, function ($q, $category) {
            $q->where('category', $category);
        })->count();

        $totalItems = Product::sum('stock');
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<=', 30)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $categories = Product::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('products.index', compact(
            'products',
            'totalProducts',
            'totalItems',
            'lowStockCount',
            'outOfStockCount',
            'categories',
            'search',
            'category'
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
        ]);

        Product::create($data);

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
        ]);

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}

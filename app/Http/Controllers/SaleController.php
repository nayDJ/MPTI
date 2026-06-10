<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer')
            ->latest()
            ->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();

        return view('sales.create', compact(
            'customers',
            'products'
        ));
    }

    public function store(Request $request)
    {
        $product = Product::findOrFail(
            $request->product_id
        );

        $subtotal =
            $product->price *
            $request->quantity;

        $sale = Sale::create([
            'customer_id' => $request->customer_id,
            'total_price' => $subtotal,
            'sales_date' => now()
        ]);

        SaleItem::create([
            'sales_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'subtotal' => $subtotal
        ]);

        $product->decrement('stock', $request->quantity);

        if ($request->quantity > $product->stock) {
    return back()->with(
        'error',
        'Stok tidak mencukupi'
    );
}

        return redirect()
            ->route('sales.index');
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'customer',
            'items.product'
        ]);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        //
    }

    public function update(Request $request, Sale $sale)
    {
        //
    }

    public function destroy(Sale $sale)
    {
        //
    }
}
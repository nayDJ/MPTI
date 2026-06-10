<x-app-layout>

    <div class="p-6">

        <h1 class="text-2xl font-bold mb-6">
            Detail Transaksi #{{ $sale->id }}
        </h1>

        <div class="bg-white p-4 rounded shadow mb-6">

            <p>
                <strong>Customer:</strong>
                {{ $sale->customer->name }}
            </p>

            <p>
                <strong>Tanggal:</strong>
                {{ $sale->sales_date }}
            </p>

            <p>
                <strong>Total:</strong>
                Rp {{ number_format($sale->total_price) }}
            </p>

        </div>

        <table class="table-auto w-full border">

            <thead>

                <tr>

                    <th class="border p-2">Produk</th>
                    <th class="border p-2">Qty</th>
                    <th class="border p-2">Subtotal</th>

                </tr>

            </thead>

            <tbody>

                @foreach($sale->items as $item)

                    <tr>

                        <td class="border p-2">
                            {{ $item->product->name }}
                        </td>

                        <td class="border p-2">
                            {{ $item->quantity }}
                        </td>

                        <td class="border p-2">
                            Rp {{ number_format($item->subtotal) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</x-app-layout>
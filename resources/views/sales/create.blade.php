<x-app-layout>

    <div class="p-6">

        <h1 class="text-2xl font-bold mb-6">
            Tambah Transaksi Penjualan
        </h1>

        <form action="{{ route('sales.store') }}" method="POST">

            @csrf

            <div class="mb-4">
                <label>Customer</label>

                <select
                    name="customer_id"
                    class="border rounded p-2 w-full"
                    required
                >
                    <option value="">
                        Pilih Customer
                    </option>

                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="mb-4">
                <label>Produk</label>

                <select
                    name="product_id"
                    class="border rounded p-2 w-full"
                    required
                >
                    <option value="">
                        Pilih Produk
                    </option>

                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                            (Stok: {{ $product->stock }})
                            - Rp {{ number_format($product->price) }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="mb-4">
                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    min="1"
                    class="border rounded p-2 w-full"
                    required
                >
            </div>

            <button
                type="submit"
                class="bg-blue-600 text-black px-4 py-2 rounded"
            >
                Simpan Transaksi
            </button>

        </form>

    </div>

</x-app-layout>
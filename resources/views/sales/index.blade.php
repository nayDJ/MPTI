<x-app-layout>

    <div class="p-6">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold">
                Data Penjualan
            </h1>

            <a
                href="{{ route('sales.create') }}"
                class="bg-blue-600 text-black px-4 py-2 rounded hover:bg-blue-700"
            >
                Tambah Penjualan
            </a>

        </div>

        <table class="table-auto w-full border border-collapse">

            <thead>

                <tr class="bg-gray-100">

                    <th class="border p-2">ID</th>
                    <th class="border p-2">Customer</th>
                    <th class="border p-2">Tanggal</th>
                    <th class="border p-2">Total</th>
                    <th class="border p-2">Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse($sales as $sale)

                    <tr>

                        <td class="border p-2">
                            {{ $sale->id }}
                        </td>

                        <td class="border p-2">
                            {{ $sale->customer->name }}
                        </td>

                        <td class="border p-2">
                            {{ $sale->sales_date }}
                        </td>

                        <td class="border p-2">
                            Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                        </td>

                        <td class="border p-2">

                            <a
                                href="{{ route('sales.show', $sale->id) }}"
                                class="bg-blue-500 text-black px-3 py-1 rounded hover:bg-blue-600"
                            >
                                Detail
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="border p-4 text-center">
                            Belum ada data penjualan
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</x-app-layout>
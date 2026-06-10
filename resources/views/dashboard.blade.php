<x-app-layout>

<div class="p-6 bg-gray-100 min-h-screen">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Dashboard Admin NNQUA
        </h1>

        <p class="text-gray-500">
            Analisis distribusi air dan penjualan secara real-time
        </p>
    </div>

    {{-- Quick Action --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <a href="{{ route('sales.create') }}"
            class="bg-blue-600 text-white p-5 rounded-xl shadow hover:bg-blue-700 transition">
            <div class="text-sm opacity-80">Menu Cepat</div>
            <div class="text-xl font-bold">+ Penjualan</div>
        </a>

        <a href="{{ route('products.index') }}"
            class="bg-green-600 text-white p-5 rounded-xl shadow hover:bg-green-700 transition">
            <div class="text-sm opacity-80">Menu Cepat</div>
            <div class="text-xl font-bold">Produk</div>
        </a>

        <a href="{{ route('customers.index') }}"
            class="bg-purple-600 text-white p-5 rounded-xl shadow hover:bg-purple-700 transition">
            <div class="text-sm opacity-80">Menu Cepat</div>
            <div class="text-xl font-bold">Customer</div>
        </a>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-500 text-sm">Total Penjualan</p>
            <h2 class="text-4xl font-bold text-blue-600 mt-2">
                {{ $totalSales }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-500 text-sm">Pendapatan</p>
            <h2 class="text-4xl font-bold text-green-600 mt-2">
                Rp {{ number_format($totalIncome) }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-500 text-sm">Total Produk</p>
            <h2 class="text-4xl font-bold text-orange-500 mt-2">
                {{ $totalProducts }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border border-red-200">
            <p class="text-gray-500 text-sm">Produk Kritis</p>
            <h2 class="text-4xl font-bold text-red-600 mt-2">
                {{ $criticalStock }}
            </h2>
            <p class="text-sm text-red-500 mt-2">
                Stok ≤ 10
            </p>
        </div>

    </div>

    {{-- Konten Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Grafik + Aktivitas --}}
        <div class="lg:col-span-2">

            {{-- Grafik --}}
            <div class="bg-white rounded-xl shadow p-6 mb-6">

                <h2 class="text-xl font-bold mb-4">
                    Grafik Penjualan
                </h2>

                <div style="height:350px;">
                    <canvas id="salesChart"></canvas>
                </div>

            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="bg-white rounded-xl shadow">

                <div class="p-5 border-b">
                    <h2 class="text-xl font-bold">
                        Aktivitas Terbaru
                    </h2>
                </div>

                <div class="overflow-x-auto">

                    <table class="table-auto w-full">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">ID</th>
                                <th class="p-3 text-left">Customer</th>
                                <th class="p-3 text-left">Tanggal</th>
                                <th class="p-3 text-left">Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($latestSales as $sale)

                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">#{{ $sale->id }}</td>
                                    <td class="p-3">{{ $sale->customer->name }}</td>
                                    <td class="p-3">{{ $sale->sales_date }}</td>
                                    <td class="p-3 font-semibold text-green-600">
                                        Rp {{ number_format($sale->total_price) }}
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">
                                        Belum ada transaksi
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- Monitor Stok --}}
        <div class="bg-white rounded-xl shadow p-5">

            <h2 class="text-xl font-bold mb-5">
                Monitor Stok
            </h2>

            @forelse($lowStockProducts as $product)

                <div class="mb-5">

                    <div class="flex justify-between mb-2">
                        <span class="font-medium">
                            {{ $product->name }}
                        </span>

                        <span class="text-sm text-gray-500">
                            {{ $product->stock }} Unit
                        </span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-3">

                        <div
                            class="h-3 rounded-full
                            @if($product->stock <= 10)
                                bg-red-500
                            @elseif($product->stock <= 30)
                                bg-yellow-500
                            @else
                                bg-green-500
                            @endif"
                            style="width: {{ min($product->stock, 100) }}%">
                        </div>

                    </div>

                </div>

            @empty

                <p class="text-gray-500">
                    Tidak ada data stok.
                </p>

            @endforelse

        </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($salesChart->pluck('date')),
            datasets: [{
                label: 'Total Penjualan',
                data: @json($salesChart->pluck('total')),
                backgroundColor: '#2563eb',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

});
</script>

</x-app-layout>
<x-app-layout>

<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Dashboard NNQUA
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Monitoring distribusi air dan transaksi penjualan
                </p>
            </div>

            <a href="{{ route('sales.create') }}"
                class="bg-[#0F6E8C] hover:bg-[#0b5c75] text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Penjualan Baru
            </a>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <p class="text-sm text-slate-500">
                    Total Penjualan
                </p>

                <h2 class="text-3xl font-bold text-slate-800 mt-2">
                    {{ $totalSales }}
                </h2>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <p class="text-sm text-slate-500">
                    Pendapatan
                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    Rp {{ number_format($totalIncome) }}
                </h2>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <p class="text-sm text-slate-500">
                    Total Produk
                </p>

                <h2 class="text-3xl font-bold text-slate-800 mt-2">
                    {{ $totalProducts }}
                </h2>
            </div>

            <div class="bg-white rounded-xl border border-red-100 p-5">
                <p class="text-sm text-slate-500">
                    Produk Kritis
                </p>

                <h2 class="text-3xl font-bold text-red-500 mt-2">
                    {{ $criticalStock }}
                </h2>

                <p class="text-xs text-red-400 mt-1">
                    Stok ≤ 10 unit
                </p>
            </div>

        </div>

        {{-- Content --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Grafik Penjualan --}}
    <div class="xl:col-span-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">

            <div class="flex justify-between items-center mb-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">
                        Grafik Penjualan
                    </h2>

                    <p class="text-sm text-gray-500">
                        Total transaksi berdasarkan tanggal
                    </p>
                </div>
            </div>

            <div class="h-[400px]">
                <canvas id="salesChart"></canvas>
            </div>

        </div>
    </div>

    {{-- Monitor Stok --}}
    <div class="xl:col-span-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-slate-800">
                    Monitor Stok
                </h2>

                <span class="bg-sky-100 text-sky-700 text-xs px-3 py-1 rounded-full">
                    Logistik
                </span>
            </div>

            @forelse($lowStockProducts as $product)

                <div class="mb-6">

                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-slate-700">
                            {{ $product->name }}
                        </span>

                        <span class="text-sm text-gray-500">
                            {{ $product->stock }} Unit
                        </span>
                    </div>

                    <div class="w-full bg-slate-200 rounded-full h-2">

                        <div
                            class="
                                h-2 rounded-full
                                @if($product->stock <= 10)
                                    bg-red-500
                                @elseif($product->stock <= 30)
                                    bg-yellow-500
                                @else
                                    bg-[#0F6E8C]
                                @endif
                            "
                            style="width: {{ min($product->stock,100) }}%">
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

    {{-- Aktivitas Terbaru Full Width --}}
    <div class="xl:col-span-12">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-slate-800">
                    Aktivitas Terbaru
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="border-b bg-slate-50">
                            <th class="text-left p-4">ID</th>
                            <th class="text-left p-4">Customer</th>
                            <th class="text-left p-4">Tanggal</th>
                            <th class="text-right p-4">Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($latestSales as $sale)

                            <tr class="border-b hover:bg-slate-50">

                                <td class="p-4">
                                    #{{ $sale->id }}
                                </td>

                                <td class="p-4">
                                    {{ $sale->customer->name }}
                                </td>

                                <td class="p-4">
                                    {{ $sale->sales_date }}
                                </td>

                                <td class="p-4 text-right font-semibold text-green-600">
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

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const ctx = document.getElementById('salesChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($salesChart->pluck('date')),
            datasets: [{
                data: @json($salesChart->pluck('total')),
                backgroundColor: '#0F6E8C',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },

                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E2E8F0'
                    }
                }
            }
        }
    });

});
</script>

</x-app-layout>
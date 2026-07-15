@section('title', 'Reports & Analytics')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8"
     x-data="{ tab: 'pemasukan' }"
     x-init="$watch('tab', () => $nextTick(() => initCharts()))">

        {{-- Header --}}
    <div class="mb-8">

        <nav class="text-sm text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Reports</span>
        </nav>

        <h1 class="text-3xl font-bold text-[#0F6E8C]">
            Reports & Analytics
        </h1>

        <p class="text-gray-500 mt-1">
            Ringkasan performa bisnis NNQUA
        </p>

    </div>

    {{-- Tab Pemasukan / Pengeluaran --}}
    <div class="flex gap-2 mb-6">
        <button @click="tab = 'pemasukan'"
            class="px-5 py-2 rounded-lg text-sm font-medium transition"
            :class="tab === 'pemasukan' ? 'bg-[#0F6E8C] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            Pemasukan
        </button>
        <button @click="tab = 'pengeluaran'"
            class="px-5 py-2 rounded-lg text-sm font-medium transition"
            :class="tab === 'pengeluaran' ? 'bg-[#0F6E8C] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            Pengeluaran
        </button>
    </div>

    {{-- Tab Pemasukan --}}
    <template x-if="tab === 'pemasukan'">
        <div>
            {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <p class="text-gray-500 text-sm">
                Total Pendapatan
            </p>

            <h2 class="text-3xl font-bold text-green-600 mt-2">
                Rp {{ number_format($totalRevenue) }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <p class="text-gray-500 text-sm">
                Total Transaksi
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $totalTransactions }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <p class="text-gray-500 text-sm">
                Total Customer
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $totalCustomers }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <p class="text-gray-500 text-sm">
                Total Produk
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $totalProducts }}
            </h2>

        </div>

    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">

        {{-- Produk Terlaris --}}
        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <h2 class="text-xl font-bold mb-5">
                Produk Terlaris
            </h2>

            @foreach($topProducts as $item)

                <div class="flex justify-between py-3 border-b">

                    <span>
                        {{ $item->product->name }}
                    </span>

                    <span class="font-bold text-[#0F6E8C]">

                        {{ $item->total_sold }} Unit

                    </span>

                </div>

            @endforeach

        </div>

        {{-- Customer Teraktif --}}
        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <h2 class="text-xl font-bold mb-5">
                Customer Teraktif
            </h2>

            @foreach($topCustomers as $customer)

                <div class="flex justify-between py-3 border-b">

                    <span>
                        {{ $customer->customer->name }}
                    </span>

                    <span class="font-bold text-green-600">

                        {{ $customer->total_orders }} Transaksi

                    </span>

                </div>

            @endforeach

        </div>

        {{-- Customer Berutang --}}
        <div class="bg-white rounded-2xl shadow-sm border p-6">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold">Customer Berutang</h2>
                <span class="bg-red-100 text-red-600 text-xs px-3 py-1 rounded-full">≥ Rp100rb</span>
            </div>

            @forelse($topDebtors as $debtor)

                <div class="flex justify-between items-center py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium text-slate-700">{{ $debtor->customer->name }}</p>
                        <p class="text-xs text-gray-500">{{ $debtor->total_transaksi }} transaksi</p>
                    </div>
                    <span class="font-bold {{ $debtor->sisa_utang >= 100000 ? 'text-red-600' : 'text-yellow-600' }}">
                        Rp {{ number_format($debtor->sisa_utang) }}
                    </span>
                </div>

            @empty

                <p class="text-gray-500 text-sm">Tidak ada data utang.</p>

            @endforelse

        </div>

    </div>

    {{-- Grafik --}}
    <div class="bg-white rounded-2xl shadow-sm border p-6">

        <h2 class="text-xl font-bold mb-5">
            Grafik Pendapatan
        </h2>

        <div style="height:350px;">
            <canvas id="reportChart"></canvas>
        </div>

    </div>

</div>
    </template>

    {{-- Tab Pengeluaran --}}
    <template x-if="tab === 'pengeluaran'">
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <h2 class="text-3xl font-bold text-red-600 mt-2">
                        Rp {{ number_format($totalExpense) }}
                    </h2>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <p class="text-gray-500 text-sm">Total Transaksi</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalTransactions }}</h2>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <p class="text-gray-500 text-sm">Total Customer</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalCustomers }}</h2>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <p class="text-gray-500 text-sm">Total Produk</p>
                    <h2 class="text-3xl font-bold mt-2">{{ $totalProducts }}</h2>
                </div>

            </div>

            {{-- Grafik Kategori --}}
            <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
                <h2 class="text-xl font-bold mb-5">Pengeluaran per Kategori</h2>
                <div style="height:350px;">
                    <canvas id="expenseCategoryChart"></canvas>
                </div>
            </div>

            {{-- Tabel Semua Pengeluaran --}}
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-slate-800">Riwayat Pengeluaran</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b">
                                <th class="text-left p-4">Tanggal</th>
                                <th class="text-left p-4">Deskripsi</th>
                                <th class="text-left p-4">Kategori</th>
                                <th class="text-right p-4">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="border-b hover:bg-slate-50">
                                    <td class="p-4">{{ $expense->expense_date }}</td>
                                    <td class="p-4 font-medium">{{ $expense->description }}</td>
                                    <td class="p-4">
                                        <span class="bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-semibold text-red-600">
                                        Rp {{ number_format($expense->amount) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data pengeluaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $expenses->links() }}
                </div>
            </div>

        </div>
    </template>

</div>

<script>
let reportChartInstance = null;
let expenseCatChartInstance = null;

function initCharts() {
    if (reportChartInstance) { reportChartInstance.destroy(); reportChartInstance = null; }
    if (expenseCatChartInstance) { expenseCatChartInstance.destroy(); expenseCatChartInstance = null; }

    const rc = document.getElementById('reportChart');
    if (rc) {
        reportChartInstance = new Chart(rc, {
            type: 'line',
            data: {
                labels: @json($monthlySales->pluck('label')),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($monthlySales->pluck('total')),
                    borderColor: '#0F6E8C',
                    backgroundColor: 'rgba(15, 110, 140, 0.08)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(items) {
                                if (!items.length) return '';
                                return items[0].label;
                            },
                            label: function(ctx) {
                                let label = ctx.dataset.label || '';
                                if (label) label += ': ';
                                label += 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0' }
                    }
                }
            }
        });
    }

    const ec = document.getElementById('expenseCategoryChart');
    if (ec) {
        expenseCatChartInstance = new Chart(ec, {
            type: 'bar',
            data: {
                labels: @json($expenseByCategory->pluck('category')),
                datasets: [{
                    label: 'Total',
                    data: @json($expenseByCategory->pluck('total')),
                    backgroundColor: '#EF4444',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0' }
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initCharts);
</script>

</x-app-layout>
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data>

        {{-- Header --}}
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-3xl text-[#0F6E8C] mt-0.5">dashboard</span>
                    <div>
                        <h1 class="text-3xl font-bold text-on-surface">
                            Dashboard NNQUA
                        </h1>

                        <p class="text-sm text-on-surface-variant mt-1">
                            Monitoring distribusi air dan transaksi penjualan
                        </p>
                    </div>
                </div>

                <button type="button" @click="$dispatch('open-modal', 'add-sale')"
                    class="bg-[#0F6E8C] hover:bg-[#0b5c75] text-white px-6 py-3 rounded-xl text-base font-medium transition">
                    + Penjualan Baru
                </button>
            </div>

        </div>

        {{-- Alert Stok Kritis --}}
        @if($stockKritis > 0)
        <div class="bg-error-container border border-error/20 rounded-xl p-4 mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 text-error flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-on-error-container">{{ $stockKritis }} produk dengan stok kritis (≤ 10 unit)</p>
                <p class="text-xs text-on-error-container mt-0.5 opacity-80">Segera lakukan restock untuk menghindari kehabisan stok</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-on-error-container hover:underline underline whitespace-nowrap">Lihat Produk</a>
        </div>
        @endif

        {{-- Alert Hutang --}}
        @if($topDebtors->isNotEmpty())
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-orange-800">{{ $topDebtors->count() }} customer memiliki tagihan menunggak</p>
                <p class="text-xs text-orange-600 mt-0.5">Total tagihan menunggu pembayaran dari customer</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-sm font-medium text-orange-700 hover:text-orange-900 underline whitespace-nowrap">Lihat Laporan</a>
        </div>
        @endif

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">

            <a href="{{ route('sales.index') }}" class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 border-t-2 border-t-primary transition-transform hover:-translate-y-1 no-underline">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-on-surface-variant">Total Penjualan</p>
                    <span class="material-symbols-outlined text-outline-variant">payments</span>
                </div>
                <h2 class="text-3xl font-bold text-primary">
                    {{ $totalSales }}
                </h2>
            </a>

            <a href="{{ route('reports.index') }}" class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 border-t-2 border-t-primary transition-transform hover:-translate-y-1 no-underline">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-on-surface-variant">Pendapatan Bulan Ini</p>
                    <span class="material-symbols-outlined text-green-500">trending_up</span>
                </div>
                <h2 class="text-3xl font-bold text-primary">
                    Rp {{ number_format($totalIncome) }}
                </h2>
            </a>

            <a href="{{ route('expenses.index') }}" class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 border-t-2 border-t-primary transition-transform hover:-translate-y-1 no-underline">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-on-surface-variant">Pengeluaran Bulan Ini</p>
                    <span class="material-symbols-outlined text-red-500">trending_down</span>
                </div>
                <h2 class="text-3xl font-bold text-error">
                    Rp {{ number_format($totalExpense) }}
                </h2>
            </a>

            <a href="{{ route('products.index') }}" class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 border-t-2 border-t-primary transition-transform hover:-translate-y-1 no-underline">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-on-surface-variant">Total Produk</p>
                    <span class="material-symbols-outlined text-outline-variant">inventory</span>
                </div>
                <h2 class="text-3xl font-bold text-primary">
                    {{ $totalProducts }}
                </h2>
            </a>

            <a href="{{ route('products.index') }}" class="block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-5 border-t-2 border-t-primary transition-transform hover:-translate-y-1 no-underline">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-on-surface-variant">Produk Kritis</p>
                    <span class="material-symbols-outlined text-error">warning</span>
                </div>
                <h2 class="text-3xl font-bold text-error">
                    {{ $stockKritis }}
                </h2>
                <p class="text-xs text-error mt-1">Stok ≤ 10 unit</p>
            </a>

        </div>

        {{-- Content --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Grafik Penjualan --}}
    <div class="xl:col-span-8">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 h-full">

            <div class="flex justify-between items-center mb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">bar_chart</span>
                        <h2 class="text-xl font-bold text-on-surface">
                            Grafik Penjualan
                        </h2>
                    </div>

                    <p class="text-sm text-on-surface-variant">
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
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6 h-full">

            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    <h2 class="text-xl font-bold text-on-surface">
                        Monitor Stok
                    </h2>
                </div>

                <span class="bg-surface-container text-on-surface-variant text-xs px-3 py-1 rounded-full">
                    Logistik
                </span>
            </div>

            <div class="flex flex-col items-center">
                <div class="h-[200px] w-full flex items-center justify-center">
                    <canvas id="stockDonutChart"></canvas>
                </div>

                <div class="w-full space-y-2 mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="text-on-surface-variant">Habis</span>
                        </div>
                        <span class="font-semibold text-on-surface">{{ $stockHabis }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                            <span class="text-on-surface-variant">Stok Kritis</span>
                        </div>
                        <span class="font-semibold text-on-surface">{{ $stockKritis }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            <span class="text-on-surface-variant">Stok Menipis</span>
                        </div>
                        <span class="font-semibold text-on-surface">{{ $stockMenipis }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-on-surface-variant">Tersedia</span>
                        </div>
                        <span class="font-semibold text-on-surface">{{ $stockAman }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Customer Berutang --}}
    <div class="xl:col-span-12">

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">credit_score</span>
                    <h2 class="text-xl font-bold text-on-surface">
                        Customer Berutang
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-error-container text-on-error-container text-xs px-3 py-1 rounded-full">
                        ≥ Rp100rb
                    </span>
                    <a href="{{ route('reports.index') }}" class="text-sm text-primary hover:underline font-medium">Lihat Semua</a>
                </div>
            </div>

            <div class="divide-y divide-outline-variant/30">
                @forelse($topDebtors as $debtor)

                    <div class="flex justify-between items-center py-3 hover:bg-surface-container-low/50 px-3 -mx-3 rounded-lg transition-colors">
                        <div>
                            <p class="font-medium text-on-surface">{{ $debtor->customer->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $debtor->total_transaksi }} transaksi</p>
                        </div>
                        <span class="font-bold {{ $debtor->sisa_utang >= 100000 ? 'text-error' : 'text-yellow-600' }}">
                            Rp {{ number_format($debtor->sisa_utang) }}
                        </span>
                    </div>

                @empty

                    <p class="text-on-surface-variant text-sm py-3">Tidak ada data utang.</p>

                @endforelse
            </div>

        </div>

    </div>

    {{-- Aktivitas Transaksi Terbaru --}}
    <div class="xl:col-span-12">

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt_long</span>
                    <h2 class="text-xl font-bold text-on-surface">
                        Transaksi Terbaru
                    </h2>
                </div>
                <a href="{{ route('sales.index') }}" class="text-sm text-primary hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="bg-surface-container text-on-surface-variant font-bold text-xs uppercase tracking-wider">
                            <th class="text-left p-4">ID</th>
                            <th class="text-left p-4">Customer</th>
                            <th class="text-left p-4">Tanggal</th>
                            <th class="text-left p-4">Status</th>
                            <th class="text-right p-4">Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-outline-variant/30">

                        @forelse($latestSales as $sale)

                            <tr class="hover:bg-surface-container-low/50 transition-colors">

                                <td class="p-4 font-medium text-on-surface">
                                    #{{ $sale->id }}
                                </td>

                                <td class="p-4 text-on-surface">
                                    {{ $sale->customer?->name ?? 'Pelanggan dihapus' }}
                                </td>

                                <td class="p-4 text-on-surface-variant">
                                    {{ $sale->sales_date }}
                                </td>

                                <td class="p-4">
                                    @php
                                        $statusLabel = match($sale->payment_status) {
                                            'lunas' => 'Lunas',
                                            'cicil' => 'Cicil',
                                            default => 'Belum Lunas',
                                        };
                                        $statusClass = match($sale->payment_status) {
                                            'lunas' => 'bg-green-100 text-green-700',
                                            'cicil' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                @php
                                    $totalClass = match($sale->payment_status) {
                                        'lunas' => 'text-green-600',
                                        'cicil' => 'text-yellow-600',
                                        default => 'text-red-600',
                                    };
                                @endphp

                                <td class="p-4 text-right font-semibold {{ $totalClass }}">
                                    Rp {{ number_format($sale->total_price) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="p-4 text-center text-on-surface-variant">
                                    Belum ada transaksi
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Pengeluaran Terbaru --}}
    <div class="xl:col-span-12">

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm p-6">

            <div class="flex justify-between items-center mb-5">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt</span>
                    <h2 class="text-xl font-bold text-on-surface">
                        Pengeluaran Terbaru
                    </h2>
                </div>
                <a href="{{ route('expenses.index') }}" class="text-sm text-primary hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="bg-surface-container text-on-surface-variant font-bold text-xs uppercase tracking-wider">
                            <th class="text-left p-4">Tanggal</th>
                            <th class="text-left p-4">Deskripsi</th>
                            <th class="text-left p-4">Kategori</th>
                            <th class="text-right p-4">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-outline-variant/30">

                        @forelse($latestExpenses as $expense)

                            <tr class="hover:bg-surface-container-low/50 transition-colors">

                                <td class="p-4 text-on-surface-variant">
                                    {{ $expense->expense_date }}
                                </td>

                                <td class="p-4 font-medium text-on-surface">
                                    {{ $expense->description }}
                                </td>

                                <td class="p-4">
                                    <span class="bg-surface-container text-on-surface-variant px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ $expense->category }}
                                    </span>
                                </td>

                                <td class="p-4 text-right font-semibold text-error">
                                    Rp {{ number_format($expense->amount) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="p-4 text-center text-on-surface-variant">
                                    Belum ada pengeluaran
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const ctx = document.getElementById('salesChart');

    if (ctx) { new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($salesChart->pluck('label')),
            datasets: [{
                label: 'Pemasukan',
                data: @json($salesChart->pluck('total')),
                backgroundColor: '#0F6E8C',
                borderRadius: 4
            }, {
                label: 'Pengeluaran',
                data: @json($expenseChart->pluck('total')),
                backgroundColor: '#EF4444',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
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
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
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
    }); }

    // ---- Stock Donut Chart ----
    const donutCtx = document.getElementById('stockDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Habis', 'Stok Kritis', 'Stok Menipis', 'Tersedia'],
                datasets: [{
                    data: [
                        {{ $stockHabis }},
                        {{ $stockKritis }},
                        {{ $stockMenipis }},
                        {{ $stockAman }}
                    ],
                    backgroundColor: ['#DC2626', '#F97316', '#EAB308', '#22C55E'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                let pct = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

<x-modal name="add-sale" :show="$errors->any()" maxWidth="4xl" focusable>
    <form action="{{ route('sales.store') }}" method="POST"
          x-data="saleForm(@js($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()))"
          x-ref="form" @submit.prevent="goConfirm()">
        @csrf

        <div class="glass-panel max-h-[921px] overflow-hidden rounded-xl shadow-xl flex flex-col">

            {{-- HEADER --}}
            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">add_shopping_cart</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Tambah Penjualan</h2>
                        <p class="text-sm text-on-surface-variant">Input pesanan air baru ke sistem</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'add-sale')" class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- STEPPER --}}
            <div class="px-6 pt-4 pb-2 flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-12 rounded-full" :class="step === 'form' ? 'bg-primary' : 'bg-outline-variant'"></div>
                    <span class="text-xs font-semibold tracking-wider" :class="step === 'form' ? 'text-primary' : 'text-on-surface-variant'">LANGKAH 1: DETAIL</span>
                </div>
                <div class="h-px flex-1 bg-outline-variant/40"></div>
                <div class="flex items-center gap-2" :class="step === 'confirm' ? '' : 'opacity-40'">
                    <div class="h-2 w-12 rounded-full" :class="step === 'confirm' ? 'bg-primary' : 'bg-outline-variant'"></div>
                    <span class="text-xs font-semibold tracking-wider text-on-surface-variant">LANGKAH 2: KONFIRMASI</span>
                </div>
            </div>

            @if($errors->any())
                <div class="mx-6 mt-2 bg-error-container text-on-error-container p-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- BODY --}}
            <p x-show="validationMsg" x-text="validationMsg"
               class="text-sm text-error mb-3 mx-6"></p>
            <div x-show="step === 'form'"
                 class="flex-1 overflow-y-auto custom-scrollbar p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- LEFT: 7/12 --}}
                    <div class="lg:col-span-7 space-y-6">

                        {{-- Informasi Pelanggan --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined text-lg">person</span>
                                <h3 class="font-semibold text-on-surface">Informasi Pelanggan</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative"
                                     @click.away="showCustomerDropdown = false"
                                     @keydown.escape="showCustomerDropdown = false">
                                    <label class="block text-sm text-on-surface-variant mb-1">Cari Pelanggan</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                                        <input type="text" name="customer_search" x-model="customerSearch"
                                            @input="showCustomerDropdown = customerSearch.length > 0"
                                            @keydown.enter.prevent="if(filteredCustomers.length) selectCustomer(filteredCustomers[0])"
                                            placeholder="Nama atau ID Pelanggan..."
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                            autocomplete="off" required>
                                    </div>
                                    <input type="hidden" name="customer_id" x-model="customerId">

                                    <div x-show="showCustomerDropdown && filteredCustomers.length > 0"
                                         x-cloak class="absolute z-50 mt-1 w-full bg-white border border-outline-variant rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <template x-for="customer in filteredCustomers" :key="customer.id">
                                            <div @click="selectCustomer(customer)"
                                                 class="px-3 py-2 cursor-pointer hover:bg-primary/10 text-sm"
                                                 :class="{ 'bg-primary/10 text-primary font-semibold': customer.id === customerId }">
                                                <span x-text="customer.name"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="showCustomerDropdown && filteredCustomers.length === 0 && customerSearch.length > 0"
                                         x-cloak class="relative z-10 mt-2 w-full bg-white border border-outline-variant rounded-lg shadow-lg p-3 text-sm">
                                        <p class="text-center text-on-surface-variant mb-3">Customer tidak ditemukan</p>
                                        <button @click="showQuickAdd = !showQuickAdd" :disabled="quickAddSubmitting"
                                            class="w-full bg-primary hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50">
                                            <span x-text="showQuickAdd ? '— Batal' : '+ Tambah Customer Baru'"></span>
                                        </button>
                                        <div x-show="showQuickAdd" x-cloak class="mt-3 space-y-2 border-t border-outline-variant/30 pt-3">
                                            <div x-text="quickAddError" class="text-error text-xs" x-show="quickAddError"></div>
                                            <input type="text" x-model="newCustomerName" placeholder="Nama Customer *"
                                                class="w-full border border-outline rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            <input type="tel" x-model="newCustomerPhone" placeholder="No HP (opsional)"
                                                class="w-full border border-outline rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            <input type="text" x-model="newCustomerAddress" placeholder="Alamat (opsional)"
                                                class="w-full border border-outline rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            <button @click="submitQuickAdd()" :disabled="quickAddSubmitting || !newCustomerName.trim()"
                                                class="w-full bg-primary hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50">
                                                <span x-text="quickAddSubmitting ? 'Menyimpan...' : 'Simpan Customer'"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                                </div>

                                <div>
                                    <label class="block text-sm text-on-surface-variant mb-1">Tanggal Transaksi</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">calendar_today</span>
                                        <input type="date" name="sales_date" x-model="salesDate"
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm" required>
                                    </div>
                                    <x-input-error :messages="$errors->get('sales_date')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- Pilihan Produk --}}
                        <div class="space-y-4 pt-4 border-t border-outline-variant/20">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined text-lg">inventory_2</span>
                                <h3 class="font-semibold text-on-surface">Pilihan Produk</h3>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(row, i) in items" :key="row.id">
                                    <div class="p-4 bg-surface-container-low rounded-xl border border-outline-variant/30">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                            <div class="md:col-span-7">
                                                <label class="block text-sm text-on-surface-variant mb-1">Pilih Produk</label>
                                                <select x-model="row.product_id"
                                                    :name="'items['+i+'][product_id]'"
                                                    @change="setProduct(row, row.product_id)"
                                                    class="w-full border border-outline rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none py-2.5" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}" id="product-opt-{{ $p->id }}"
                                                            data-price="{{ $p->price }}" data-stock="{{ $p->stock }}"
                                                            data-name="{{ $p->name }}">
                                                            {{ $p->name }} (stok: {{ $p->stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="mt-1 flex items-center gap-3">
                                                    <span class="text-xs text-on-surface-variant bg-surface-container px-2 py-0.5 rounded" x-show="row.product_id" x-text="'Stok: ' + row.product_stock + ' unit'"></span>
                                                    <span class="text-xs text-on-surface-variant bg-surface-container px-2 py-0.5 rounded" x-show="row.product_id" x-text="'Harga: Rp ' + row.product_price.toLocaleString('id-ID')"></span>
                                                </div>
                                                <span class="text-xs" x-text="stockStatus(row)"
                                                      :class="stockStatus(row).includes('habis') ? 'text-error' : stockStatus(row).includes('hanya') ? 'text-orange-600' : 'text-green-600'">
                                                </span>
                                            </div>
                                            <div class="md:col-span-5">
                                                <label class="block text-sm text-on-surface-variant mb-1">Jumlah</label>
                                                <div class="flex items-center bg-white border border-outline rounded-lg overflow-hidden">
                                                    <button type="button" @click="decrementQty(row.id)"
                                                        class="px-3 py-2.5 hover:bg-surface-container transition-colors text-on-surface-variant">
                                                        <span class="material-symbols-outlined text-base">remove</span>
                                                    </button>
                                                    <input type="number" x-model="row.quantity"
                                                        :name="'items['+i+'][quantity]'" min="1"
                                                        class="w-full text-center border-none focus:ring-0 text-sm font-medium" required>
                                                    <button type="button" @click="incrementQty(row.id)"
                                                        class="px-3 py-2.5 hover:bg-surface-container transition-colors text-on-surface-variant">
                                                        <span class="material-symbols-outlined text-base">add</span>
                                                    </button>
                                                </div>
                                                <div class="text-right text-sm font-medium text-on-surface mt-1">
                                                    <span x-text="'Rp ' + subtotal(row).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeRow(row.id)" x-show="items.length > 1"
                                            class="mt-2 text-sm text-error hover:text-red-700 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base">delete</span> Hapus
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <button type="button" @click="addRow()"
                                class="flex items-center gap-1.5 text-sm text-primary hover:underline font-medium">
                                <span class="material-symbols-outlined text-lg">add_circle</span>
                                Tambah Produk Lain
                            </button>
                        </div>

                        {{-- Status Pembayaran --}}
                        <div class="space-y-4 pt-4 border-t border-outline-variant/20">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined text-lg">payments</span>
                                <h3 class="font-semibold text-on-surface">Status Pembayaran</h3>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <label class="flex-1 cursor-pointer group min-w-[100px]">
                                    <input type="radio" name="payment_status" value="lunas" x-model="paymentStatus" class="hidden peer">
                                    <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="paymentStatus === 'lunas' ? 'text-primary' : 'text-outline'">check_circle</span>
                                        <span class="text-sm font-semibold" :class="paymentStatus === 'lunas' ? 'text-primary' : 'text-on-surface'">Lunas</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer group min-w-[100px]">
                                    <input type="radio" name="payment_status" value="cicil" x-model="paymentStatus" class="hidden peer">
                                    <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="paymentStatus === 'cicil' ? 'text-primary' : 'text-outline'">schedule</span>
                                        <span class="text-sm font-semibold" :class="paymentStatus === 'cicil' ? 'text-primary' : 'text-on-surface'">Cicilan</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer group min-w-[100px]">
                                    <input type="radio" name="payment_status" value="belum" x-model="paymentStatus" class="hidden peer">
                                    <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="paymentStatus === 'belum' ? 'text-primary' : 'text-outline'">error_outline</span>
                                        <span class="text-sm font-semibold" :class="paymentStatus === 'belum' ? 'text-primary' : 'text-on-surface'">Belum Bayar</span>
                                    </div>
                                </label>
                            </div>

                            <div x-show="paymentStatus === 'cicil'" x-cloak>
                                <label for="paid_amount" class="block text-sm text-on-surface-variant mb-1">Jumlah Dibayar</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                                    <input type="number" id="paid_amount" name="paid_amount" step="0.01" min="0"
                                        value="{{ old('paid_amount') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm">
                                </div>
                                <p class="text-xs text-on-surface-variant mt-1">Nominal yang dibayarkan sekarang</p>
                                <x-input-error :messages="$errors->get('paid_amount')" class="mt-1" />
                            </div>

                            <x-input-error :messages="$errors->get('payment_status')" />
                        </div>

                        <input type="hidden" name="redirect_to" value="dashboard">

                    </div>

                    {{-- RIGHT: 5/12 SUMMARY --}}
                    <div class="lg:col-span-5">
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 space-y-5 sticky top-0 shadow-sm overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>

                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-on-surface">Ringkasan Pesanan</h3>
                                <span class="px-2 py-0.5 bg-secondary-container text-on-secondary-container rounded-full text-[10px] font-bold tracking-widest uppercase">Draft</span>
                            </div>

                            <template x-if="items.length === 0 || !items.some(i => i.product_id)">
                                <div class="text-center py-8 text-on-surface-variant text-sm">
                                    <span class="material-symbols-outlined text-3xl mb-2">shopping_cart</span>
                                    <p>Belum ada produk</p>
                                </div>
                            </template>

                            <div class="space-y-3 border-b border-outline-variant/30 pb-4 max-h-48 overflow-y-auto custom-scrollbar" x-show="items.some(i => i.product_id)">
                                <template x-for="row in items" :key="row.id">
                                    <div class="flex justify-between items-center" x-show="row.product_id">
                                        <div>
                                            <p class="text-sm font-medium text-on-surface" x-text="row.product_name || '-'"></p>
                                            <p class="text-xs text-on-surface-variant" x-text="'Qty: ' + row.quantity + ' x Rp ' + row.product_price.toLocaleString('id-ID')"></p>
                                        </div>
                                        <span class="text-sm font-medium text-on-surface" x-text="'Rp ' + subtotal(row).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm text-on-surface-variant">
                                    <span>Subtotal</span>
                                    <span class="font-medium" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between text-sm text-on-surface-variant">
                                    <span>Pajak (0%)</span>
                                    <span class="font-medium">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 mt-3 border-t border-outline text-primary">
                                    <span class="text-lg font-bold">Total</span>
                                    <span class="text-lg font-bold" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div class="space-y-2 pt-2">
                                <button type="button" @click="goConfirm()" class="btn-primary-animate w-full py-2.5 bg-primary text-white rounded-xl font-semibold flex items-center justify-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all text-sm">
                                    <span>Lanjut ke Konfirmasi</span>
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            {{-- CONFIRM STEP --}}
            <div x-show="step === 'confirm'"
                 class="flex-1 overflow-y-auto custom-scrollbar p-6">

                    <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 mb-4 text-sm">
                        <p class="font-semibold text-on-surface mb-4 text-base flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">order_approve</span>
                            Konfirmasi Transaksi
                        </p>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Customer</span>
                                <span class="font-medium text-on-surface" x-text="customerName || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Tanggal</span>
                                <span class="font-medium text-on-surface" x-text="salesDate"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Status</span>
                                <span x-text="paymentStatus === 'lunas' ? 'Lunas' : paymentStatus === 'cicil' ? 'Cicil' : 'Belum Dibayar'"
                                      :class="paymentStatus === 'lunas' ? 'text-green-600' : paymentStatus === 'cicil' ? 'text-yellow-600' : 'text-error'"
                                      class="font-medium"></span>
                            </div>
                        </div>

                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-on-surface-variant border-b border-outline-variant/30">
                                    <th class="text-left py-2">Produk</th>
                                    <th class="text-center py-2 w-16">Qty</th>
                                    <th class="text-right py-2 w-28">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="row in items" :key="row.id">
                                    <tr class="border-b border-outline-variant/10 last:border-0">
                                        <td class="py-2 text-on-surface" x-text="row.product_name || '-'"></td>
                                        <td class="py-2 text-center" x-text="row.quantity"></td>
                                        <td class="py-2 text-right font-medium" x-text="'Rp ' + subtotal(row).toLocaleString('id-ID')"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="text-right font-bold text-on-surface mt-3 pt-3 border-t border-outline-variant/30">
                            Total: <span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="confirmBeforeClose()"
                            class="px-5 py-2.5 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                            Kembali
                        </button>
                        <button type="button" @click="submitForm()"
                            class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2">
                            <span>Konfirmasi & Simpan</span>
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                        </button>
                    </div>
                </div>

            {{-- CANCEL CONFIRMATION --}}
            <div x-show="cancelConfirm" x-cloak class="px-6 py-4 border-t border-outline-variant/20">
                <div class="bg-error-container border border-error/20 rounded-xl p-4 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-error">warning</span>
                        <div>
                            <p class="font-semibold text-on-error-container">Data yang diisi akan hilang</p>
                            <p class="text-xs text-on-surface-variant">Yakin ingin membatalkan?</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="dismissCancel()"
                            class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                            Tidak
                        </button>
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 bg-error text-on-error rounded-lg text-sm font-medium hover:bg-red-700 transition">
                            Ya, Batalkan
                        </button>
                    </div>
                </div>
            </div>



            <input type="hidden" name="new_customer_name" x-model="quickNewCustomer?.name || ''">
            <input type="hidden" name="new_customer_phone" x-model="quickNewCustomer?.phone || ''">
            <input type="hidden" name="new_customer_address" x-model="quickNewCustomer?.address || ''">
        </div>
    </form>
</x-modal>

</x-app-layout>
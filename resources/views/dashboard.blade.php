@section('title', 'Dashboard')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data>

        {{-- Header --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Dashboard NNQUA
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Monitoring distribusi air dan transaksi penjualan
                </p>
            </div>

            <button type="button" @click="$dispatch('open-modal', 'add-sale')"
                class="bg-[#0F6E8C] hover:bg-[#0b5c75] text-white px-6 py-3 rounded-xl text-base font-medium transition">
                + Penjualan Baru
            </button>
        </div>

        {{-- Alert Stok Kritis --}}
        @if($criticalStock > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800">{{ $criticalStock }} produk dengan stok kritis (≤ 10 unit)</p>
                <p class="text-xs text-red-600 mt-0.5">Segera lakukan restock untuk menghindari kehabisan stok</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-medium text-red-700 hover:text-red-900 underline whitespace-nowrap">Lihat Produk</a>
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

            <div class="bg-white rounded-xl border border-red-100 p-5">
                <p class="text-sm text-slate-500">
                    Pengeluaran Bulan Ini
                </p>

                <h2 class="text-3xl font-bold text-red-500 mt-2">
                    Rp {{ number_format($totalExpense) }}
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
                            style="width: {{ min(($product->stock / 50) * 100, 100) }}%">
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

    {{-- Customer Berutang --}}
    <div class="xl:col-span-12">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-slate-800">
                    Customer Berutang
                </h2>
                <span class="bg-red-100 text-red-600 text-xs px-3 py-1 rounded-full">
                    ≥ Rp100rb
                </span>
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

    {{-- Pengeluaran Terbaru --}}
    <div class="xl:col-span-12">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-slate-800">
                    Pengeluaran Terbaru
                </h2>
                <a href="{{ route('expenses.index') }}" class="text-sm text-[#0F6E8C] hover:underline font-medium">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="border-b bg-slate-50">
                            <th class="text-left p-4">Tanggal</th>
                            <th class="text-left p-4">Deskripsi</th>
                            <th class="text-left p-4">Kategori</th>
                            <th class="text-right p-4">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($latestExpenses as $expense)

                            <tr class="border-b hover:bg-slate-50">

                                <td class="p-4">
                                    {{ $expense->expense_date }}
                                </td>

                                <td class="p-4 font-medium">
                                    {{ $expense->description }}
                                </td>

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
                                <td colspan="4" class="p-4 text-center text-gray-500">
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

    new Chart(ctx, {
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
    });

});
</script>

<x-modal name="add-sale" :show="$errors->any()" focusable>
    <form action="{{ route('sales.store') }}" method="POST" class="p-6"
          x-data="saleForm(@js($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()))"
          x-ref="form" @submit.prevent="goConfirm()">
        @csrf
        <h2 class="text-lg font-bold text-slate-800 mb-4">Tambah Penjualan</h2>

        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4">
            <div class="relative"
                    @click.away="showCustomerDropdown = false"
                    @keydown.escape="showCustomerDropdown = false">
                <x-input-label for="customer_search" value="Customer" />
                <input type="text" id="customer_search" name="customer_search"
                    x-model="customerSearch"
                    @input="showCustomerDropdown = customerSearch.length > 0"
                    @keydown.enter.prevent="if(filteredCustomers.length) selectCustomer(filteredCustomers[0])"
                    placeholder="Cari customer..."
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    autocomplete="off"
                    required>
                <input type="hidden" name="customer_id" x-model="customerId">

                <div x-show="showCustomerDropdown && filteredCustomers.length > 0"
                    x-cloak
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto">
                    <template x-for="customer in filteredCustomers" :key="customer.id">
                        <div @click="selectCustomer(customer)"
                            class="px-3 py-2 cursor-pointer hover:bg-[#0F6E8C] hover:text-white text-sm"
                            :class="{ 'bg-[#0F6E8C] text-white': customer.id === customerId }">
                            <span x-text="customer.name"></span>
                        </div>
                    </template>
                </div>

                <div x-show="showCustomerDropdown && filteredCustomers.length === 0 && customerSearch.length > 0"
                    x-cloak
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg p-3 text-sm text-gray-500">
                    <div class="text-center mb-3">Customer tidak ditemukan</div>
                    <button @click="showQuickAdd = !showQuickAdd"
                        :disabled="quickAddSubmitting"
                        class="w-full bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50">
                        <span x-text="showQuickAdd ? '— Batal' : '+ Tambah Customer Baru'"></span>
                    </button>

                    <div x-show="showQuickAdd" x-cloak class="mt-3 space-y-2 border-t pt-3">
                        <div x-text="quickAddError" class="text-red-600 text-xs" x-show="quickAddError"></div>
                        <input type="text" x-model="newCustomerName" placeholder="Nama Customer *"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="tel" x-model="newCustomerPhone" placeholder="No HP (opsional)"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="text" x-model="newCustomerAddress" placeholder="Alamat (opsional)"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button @click="submitQuickAdd()"
                            :disabled="quickAddSubmitting || !newCustomerName.trim()"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50">
                            <span x-text="quickAddSubmitting ? 'Menyimpan...' : 'Simpan Customer'"></span>
                        </button>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('customer_id')" />
            </div>

            <div>
                <x-input-label for="sales_date" value="Tanggal" />
                <x-text-input id="sales_date" name="sales_date" type="date"
                    x-model="salesDate"
                    class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('sales_date')" />
            </div>

            <div>
                <x-input-label value="Produk" />
                <div class="overflow-x-auto max-h-56 overflow-y-auto mt-1">
                    <table class="w-full table-fixed">
                        <thead>
                            <tr class="text-sm text-gray-500 border-b">
                                <th class="text-left p-2">Produk</th>
                                <th class="text-center p-2 w-20">Qty</th>
                                <th class="text-right p-2 w-28">Subtotal</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in items" :key="row.id">
                                <tr>
                                    <td class="p-2">
                                        <select x-model="row.product_id"
                                            :name="'items['+i+'][product_id]'"
                                            @change="setProduct(row, row.product_id)"
                                            class="w-full border-gray-300 rounded-md text-sm" required>
                                            <option value="">Pilih</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" id="product-opt-{{ $p->id }}"
                                                    data-price="{{ $p->price }}" data-stock="{{ $p->stock }}"
                                                    data-name="{{ $p->name }}">
                                                    {{ $p->name }} (stok: {{ $p->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-xs" x-text="stockStatus(row)"
                                              :class="stockStatus(row).startsWith('Stok') ? 'text-red-500' : 'text-green-600'">
                                        </span>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" x-model="row.quantity"
                                            :name="'items['+i+'][quantity]'" min="1"
                                            class="w-20 border-gray-300 rounded-md text-sm text-center" required>
                                    </td>
                                    <td class="p-2 text-right font-medium text-green-600"
                                        x-text="'Rp ' + subtotal(row).toLocaleString('id-ID')">
                                    </td>
                                    <td class="p-2 w-10">
                                        <button type="button" @click="removeRow(row.id)"
                                                x-show="items.length > 1"
                                                class="text-red-500 hover:text-red-700 text-sm p-1">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="addRow()"
                    class="mt-2 px-4 py-1.5 text-sm font-medium border border-[#0F6E8C] text-[#0F6E8C] rounded-lg hover:bg-[#0F6E8C] hover:text-white transition">
                    + Tambah Baris
                </button>
                <div class="text-right font-bold text-lg mt-2 text-slate-800">
                    Total: <span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div>
                <x-input-label value="Status Pembayaran" />
                <div class="mt-1 flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="lunas" x-model="paymentStatus" class="text-green-600">
                        <span class="text-sm">Lunas</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="cicil" x-model="paymentStatus" class="text-yellow-600">
                        <span class="text-sm">Cicil</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="belum" x-model="paymentStatus" checked class="text-red-600">
                        <span class="text-sm">Belum</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('payment_status')" />
            </div>

            <input type="hidden" name="redirect_to" value="dashboard">

            <div x-show="paymentStatus === 'cicil'" x-cloak>
                <x-input-label for="paid_amount" value="Jumlah Dibayar" />
                <x-text-input id="paid_amount" name="paid_amount" type="number" step="0.01" min="0"
                    value="{{ old('paid_amount') }}" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('paid_amount')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <template x-if="step === 'form'">
                <div>
                    <div class="flex gap-3" x-show="!cancelConfirm">
                        <button type="button" @click="confirmBeforeClose()"
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                            Batal
                        </button>
                        <button type="button" @click="goConfirm()"
                            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                            Simpan Transaksi
                        </button>
                    </div>
                    <div x-show="cancelConfirm" class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm">
                        <p class="text-red-800 font-medium mb-2">Data yang diisi akan hilang. Yakin batalkan?</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="dismissCancel()"
                                class="px-3 py-1.5 text-sm text-slate-600 hover:text-slate-800">
                                Tidak
                            </button>
                            <button type="button" @click="closeModal()"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                                Ya, Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="step === 'confirm'">
                <div class="w-full">
                    <p x-show="validationMsg" x-text="validationMsg"
                       class="text-sm text-red-600 mb-3"></p>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-4 text-sm text-slate-600">
                        <p class="font-semibold text-slate-800 mb-3">Konfirmasi Transaksi</p>
                        <div class="space-y-1 mb-3">
                            <p><span class="font-medium">Customer:</span> <span x-text="customerName || '-'"></span></p>
                            <p><span class="font-medium">Tanggal:</span> <span x-text="salesDate"></span></p>
                            <p><span class="font-medium">Status:</span>
                                <span x-text="paymentStatus === 'lunas' ? 'Lunas' : paymentStatus === 'cicil' ? 'Cicil' : 'Belum Dibayar'"
                                      :class="paymentStatus === 'lunas' ? 'text-green-600' : paymentStatus === 'cicil' ? 'text-yellow-600' : 'text-red-600'"
                                      class="font-medium"></span>
                            </p>
                        </div>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-500 border-b">
                                    <th class="text-left p-1.5">Produk</th>
                                    <th class="text-center p-1.5 w-16">Qty</th>
                                    <th class="text-right p-1.5 w-28">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="row in items" :key="row.id">
                                    <tr class="border-b border-slate-200 last:border-0">
                                        <td class="p-1.5" x-text="row.product_name || '-'"></td>
                                        <td class="p-1.5 text-center" x-text="row.quantity"></td>
                                        <td class="p-1.5 text-right" x-text="'Rp ' + subtotal(row).toLocaleString('id-ID')"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="text-right font-bold text-slate-800 mt-2 pt-2 border-t border-slate-200">
                            Total: <span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="confirmBeforeClose()"
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                            Kembali
                        </button>
                        <button type="button" @click="submitForm()"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                            Konfirmasi & Simpan
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </form>
</x-modal>

</x-app-layout>
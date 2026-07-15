@section('title', 'Data Penjualan')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data="{
    submitting: false,
    paymentStatus: 'belum',
    editStatus: '',
    editPaidAmount: 0,
    editSaleId: null,
    editSaleTotal: 0,
    openEditStatus(id, status, paid, total) {
        this.editSaleId = id;
        this.editStatus = status;
        this.editPaidAmount = paid;
        this.editSaleTotal = total;
        document.getElementById('edit-payment-form').action = '/sales/' + id;
        this.$dispatch('open-modal', 'edit-payment-status');
    },
    confirmDelete(url) {
        document.getElementById('delete-sale-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-sale');
    }
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 text-green-700 p-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 text-red-600 p-4 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">

        <div>

            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600">Data Penjualan</span>
            </nav>

            <h1 class="text-3xl font-bold text-[#0F6E8C]">
                Data Penjualan
            </h1>

            <p class="text-gray-500 mt-1">
                Kelola seluruh transaksi penjualan NNQUA
            </p>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 inline-block mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['period' => null]) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition
                              {{ !request('period') ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'harian']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition
                              {{ request('period') === 'harian' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Harian
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'bulanan']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition
                              {{ request('period') === 'bulanan' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Bulanan
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'tahunan']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition
                              {{ request('period') === 'tahunan' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Tahunan
                    </a>
                </div>
            </div>

        </div>

        <button
            @click="$dispatch('open-modal', 'add-sale')"
            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-3 rounded-xl shadow-sm transition">

            + Tambah Penjualan

        </button>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Total Transaksi
            </p>

            <h2 class="text-3xl font-bold text-slate-800 mt-3">
                {{ $totalSales }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Total Pendapatan
            </p>

            <h2 class="text-3xl font-bold text-green-600 mt-3">
                Rp {{ number_format($totalRevenue) }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Total Hutang
            </p>

            <h2 class="text-3xl font-bold text-red-600 mt-3">
                Rp {{ number_format($totalDebt) }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Transaksi Hari Ini
            </p>

            <h2 class="text-3xl font-bold text-[#0F6E8C] mt-3">
                {{ $todaySales }}
            </h2>

        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-6 border-b">

            <div class="flex items-center gap-4">
                <h2 class="text-xl font-bold text-slate-800 whitespace-nowrap">
                    Riwayat Transaksi
                </h2>

                <div class="flex items-center gap-2 ml-auto">
                    <form method="GET" action="{{ route('sales.index') }}" class="flex gap-2 items-center flex-wrap">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Cari customer..."
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36">

                        <select name="status"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Semua Status</option>
                            <option value="lunas" {{ ($status ?? '') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="cicil" {{ ($status ?? '') === 'cicil' ? 'selected' : '' }}>Cicil</option>
                            <option value="belum" {{ ($status ?? '') === 'belum' ? 'selected' : '' }}>Belum</option>
                        </select>

                        <input type="date" name="from" value="{{ $from ?? '' }}"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36">
                        <span class="text-sm text-slate-500">—</span>
                        <input type="date" name="to" value="{{ $to ?? '' }}"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-36">

                        <button type="submit"
                            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm transition">
                            Filter
                        </button>

                        @if($search || $status || $from || $to)
                            <a href="{{ route('sales.index') }}"
                                class="px-3 py-2 text-sm text-slate-600 hover:text-slate-800">
                                Reset
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('sales.export.pdf', request()->query()) }}"
                       class="btn-pdf">
                        <span class="text">PDF</span>
                        <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 35" class="w-4 h-4 fill-current"><path d="M17.5,22.131a1.249,1.249,0,0,1-1.25-1.25V2.187a1.25,1.25,0,0,1,2.5,0V20.881A1.25,1.25,0,0,1,17.5,22.131Z"></path><path d="M17.5,22.693a3.189,3.189,0,0,1-2.262-.936L8.487,15.006a1.249,1.249,0,0,1,1.767-1.767l6.751,6.751a.7.7,0,0,0,.99,0l6.751-6.751a1.25,1.25,0,0,1,1.768,1.767l-6.752,6.751A3.191,3.191,0,0,1,17.5,22.693Z"></path><path d="M31.436,34.063H3.564A3.318,3.318,0,0,1,.25,30.749V22.011a1.25,1.25,0,0,1,2.5,0v8.738a.815.815,0,0,0,.814.814H31.436a.815.815,0,0,0,.814-.814V22.011a1.25,1.25,0,1,1,2.5,0v8.738A3.318,3.318,0,0,1,31.436,34.063Z"></path></svg></span>
                    </a>
                </div>
            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="bg-slate-50 border-b">

                        <th class="p-4 text-left">ID</th>
                        <th class="p-4 text-left">Customer</th>
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Total</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-center">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($sales as $sale)

                    <tr class="border-b hover:bg-slate-50">

                        <td class="p-4 font-medium">
                            #{{ $sale->id }}
                        </td>

                        <td class="p-4">
                            {{ $sale->customer->name }}
                        </td>

                        <td class="p-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($sale->sales_date)->format('d M Y') }}
                        </td>

                        <td class="p-4">

                            <span class="font-bold text-green-600">

                                Rp {{ number_format($sale->total_price) }}

                            </span>

                        </td>

                        <td class="p-4">
                            @if($sale->payment_status === 'lunas')
                                <span @click="openEditStatus({{ $sale->id }}, 'lunas', {{ $sale->paid_amount ?? 0 }}, {{ $sale->total_price }})"
                                    class="cursor-pointer bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold hover:ring-2 hover:ring-green-300">Lunas</span>
                            @elseif($sale->payment_status === 'cicil')
                                <span @click="openEditStatus({{ $sale->id }}, 'cicil', {{ $sale->paid_amount ?? 0 }}, {{ $sale->total_price }})"
                                    class="cursor-pointer bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold hover:ring-2 hover:ring-yellow-300">Cicil (Rp {{ number_format($sale->paid_amount) }})</span>
                            @else
                                <span @click="openEditStatus({{ $sale->id }}, 'belum', {{ $sale->paid_amount ?? 0 }}, {{ $sale->total_price }})"
                                    class="cursor-pointer bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold hover:ring-2 hover:ring-red-300">Belum</span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('sales.show', $sale->id) }}"
                                   title="Lihat"
                                   class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition relative group">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Lihat</span>
                                </a>

                                <button @click="openEditStatus({{ $sale->id }}, '{{ $sale->payment_status }}', {{ $sale->paid_amount ?? 0 }}, {{ $sale->total_price }})"
                                        title="Edit"
                                        class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg transition relative group">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Edit</span>
                                </button>

                                <button @click="confirmDelete('/sales/' + {{ $sale->id }})"
                                        title="Hapus"
                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition relative group">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Hapus</span>
                                </button>
                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6"
                            class="text-center p-8 text-gray-500">

                            Belum ada data transaksi

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

                </div>
            </div>

        </div>

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

<x-modal name="edit-payment-status" focusable>
    <form id="edit-payment-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('PUT')
        <h2 class="text-lg font-bold text-slate-800 mb-1">Ubah Status Pembayaran</h2>
        <p class="text-sm text-gray-500 mb-4">
            Total: <span class="font-semibold" x-text="'Rp ' + Number(editSaleTotal).toLocaleString('id-ID')"></span>
            — Dibayar: <span class="font-semibold" x-text="'Rp ' + Number(editPaidAmount).toLocaleString('id-ID')"></span>
        </p>

        <div class="space-y-4">
            <div>
                <x-input-label value="Status" />
                <div class="mt-1 flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="lunas" x-model="editStatus" class="text-green-600">
                        <span class="text-sm">Lunas</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="cicil" x-model="editStatus" class="text-yellow-600">
                        <span class="text-sm">Cicil</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_status" value="belum" x-model="editStatus" class="text-red-600">
                        <span class="text-sm">Belum</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('payment_status')" />
            </div>

            <div>
                <x-input-label for="additional_payment" value="Tambahan Bayar" />
                <p class="text-xs text-gray-400 mb-1">Jumlah yang dibayarkan sekarang</p>
                <x-text-input id="additional_payment" name="additional_payment" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('additional_payment')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                @click="$dispatch('close-modal', 'edit-payment-status')"
                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                Batal
            </button>
            <button
                type="submit"
                :disabled="submitting"
                class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

{{-- Modal Hapus --}}
<x-modal name="confirm-delete-sale" focusable>
    <form id="delete-sale-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('DELETE')
        <div class="text-center">
            <svg class="mx-auto h-14 w-14 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Hapus Penjualan</h2>
            <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus penjualan ini? Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'confirm-delete-sale')"
                class="px-5 py-2.5 text-sm text-slate-600 hover:text-slate-800 font-medium">
                Batal
            </button>
            <button type="submit" :disabled="submitting"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Ya, Hapus
            </button>
        </div>
    </form>
</x-modal>

</x-app-layout>
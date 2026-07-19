@section('title', 'Data Penjualan')
@section('topbar-title', 'Riwayat Penjualan')
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
}">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
                <span class="material-symbols-outlined text-[16px] align-middle mx-1">chevron_right</span>
                <span class="text-slate-600">Riwayat Penjualan</span>
            </nav>
            <h1 class="text-3xl font-bold text-primary">
                Riwayat Penjualan
            </h1>
            <p class="text-gray-500 mt-1">
                Kelola seluruh transaksi penjualan NNQUA
            </p>

            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm p-3 inline-block mt-4">
                <div class="flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['period' => null]) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition border-0
                              {{ !request('period') ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        Semua
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'harian']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition border-0
                              {{ request('period') === 'harian' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        Harian
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'bulanan']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition border-0
                              {{ request('period') === 'bulanan' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        Bulanan
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'tahunan']) }}"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium transition border-0
                              {{ request('period') === 'tahunan' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                        Tahunan
                    </a>
                </div>
            </div>
        </div>

        <button
            @click="$dispatch('open-modal', 'add-sale')"
            class="bg-primary hover:bg-primary-container text-white px-5 py-3 rounded-xl shadow-sm transition whitespace-nowrap inline-flex items-center gap-2">
            <span class="material-symbols-outlined">add_shopping_cart</span>
            Tambah Penjualan
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 border-t-2 border-t-primary shadow-sm p-5 flex flex-col gap-2">
            <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg self-start">payments</span>
            <p class="text-sm text-on-surface-variant">Total Omzet <span class="font-light">(Uang Masuk)</span></p>
            <p class="text-3xl font-bold text-on-surface">Rp {{ number_format($totalRevenue) }}</p>
            @if($revenueGrowth != 0 && $period === 'bulanan')
                <span class="text-xs inline-flex items-center gap-1 {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <span class="material-symbols-outlined text-sm">{{ $revenueGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% dari bulan lalu
                </span>
            @endif
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 border-t-2 border-t-error shadow-sm p-5 flex flex-col gap-2">
            <span class="material-symbols-outlined text-error bg-error/10 p-2 rounded-lg self-start">credit_score</span>
            <p class="text-sm text-on-surface-variant">Total Piutang <span class="font-light">(Uang Belum Dibayar)</span></p>
            <p class="text-3xl font-bold text-on-surface">Rp {{ number_format($totalDebt) }}</p>
            <span class="text-xs text-on-surface-variant">{{ $debtorCount }} Outstanding</span>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 border-t-2 border-t-tertiary shadow-sm p-5 flex flex-col gap-2">
            <span class="material-symbols-outlined text-tertiary bg-tertiary/10 p-2 rounded-lg self-start">receipt_long</span>
            <p class="text-sm text-on-surface-variant">Total Transaksi</p>
            <p class="text-3xl font-bold text-on-surface">{{ $totalSales }}</p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 border-t-2 border-t-[#586377] shadow-sm p-5 flex flex-col gap-2">
            <span class="material-symbols-outlined text-on-surface-variant bg-surface-container-high p-2 rounded-lg self-start">today</span>
            <p class="text-sm text-on-surface-variant">Transaksi Hari Ini</p>
            <p class="text-3xl font-bold text-on-surface">{{ $todaySales }}</p>
        </div>

    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-outline-variant/20">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-on-surface">Riwayat Transaksi</h2>
                <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-2">

                    <div class="relative" x-data="{ showFilter: false, statusFilter: '{{ request('status', '') }}' }">
                        <button type="button" @click="showFilter = !showFilter"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span>
                            Status Bayar
                            <span x-show="statusFilter !== ''" x-cloak class="w-2 h-2 rounded-full bg-primary"></span>
                        </button>
                        <div x-show="showFilter" @click.outside="showFilter = false" x-cloak
                            class="absolute right-0 mt-2 w-44 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-lg z-20 overflow-hidden py-1">
                            <button type="button"
                                @click="let f = $el.closest('form'); let p = new URLSearchParams(new FormData(f)); p.set('status', ''); window.location.href = '{{ route('sales.index') }}?' + p.toString();"
                                class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                    {{ !request('status') ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                                <span x-show="statusFilter === ''" class="material-symbols-outlined text-primary text-base">check</span>
                                <span>Semua</span>
                            </button>
                            <button type="button"
                                @click="let f = $el.closest('form'); let p = new URLSearchParams(new FormData(f)); p.set('status', 'lunas'); window.location.href = '{{ route('sales.index') }}?' + p.toString();"
                                class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                    {{ request('status') === 'lunas' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                                <span x-show="statusFilter === 'lunas'" class="material-symbols-outlined text-primary text-base">check</span>
                                <span class="w-2 h-2 rounded-full bg-green-600 inline-block mr-1"></span> Lunas
                            </button>
                            <button type="button"
                                @click="let f = $el.closest('form'); let p = new URLSearchParams(new FormData(f)); p.set('status', 'cicil'); window.location.href = '{{ route('sales.index') }}?' + p.toString();"
                                class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                    {{ request('status') === 'cicil' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                                <span x-show="statusFilter === 'cicil'" class="material-symbols-outlined text-primary text-base">check</span>
                                <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block mr-1"></span> Cicil
                            </button>
                            <button type="button"
                                @click="let f = $el.closest('form'); let p = new URLSearchParams(new FormData(f)); p.set('status', 'belum'); window.location.href = '{{ route('sales.index') }}?' + p.toString();"
                                class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                    {{ request('status') === 'belum' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                                <span x-show="statusFilter === 'belum'" class="material-symbols-outlined text-primary text-base">check</span>
                                <span class="w-2 h-2 rounded-full bg-red-600 inline-block mr-1"></span> Belum Bayar
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Cari..."
                            class="w-32 pl-10 pr-4 py-2 border border-outline rounded-lg text-sm outline-none bg-surface-container-low">
                    </div>
                    <div class="relative">
                        <input type="date" name="from" value="{{ $from ?? '' }}"
                            class="w-32 pl-3 pr-2 py-2 border border-outline rounded-lg text-xs outline-none bg-surface-container-low">
                    </div>
                    <span class="text-on-surface-variant">—</span>
                    <div class="relative">
                        <input type="date" name="to" value="{{ $to ?? '' }}"
                            class="w-32 pl-3 pr-2 py-2 border border-outline rounded-lg text-xs outline-none bg-surface-container-low">
                    </div>

                    @if($search || $status || $from || $to)
                        <a href="{{ route('sales.index') }}"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            Reset
                        </a>
                    @endif
                    <a href="{{ route('sales.export.pdf', request()->query()) }}"
                       class="btn-pdf inline-flex items-center gap-2">
                        <span class="text">PDF</span>
                        <span class="icon material-symbols-outlined">picture_as_pdf</span>
                    </a>

                    <input type="hidden" name="period" value="{{ $period ?? '' }}">
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">ID Pesanan</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Total</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase trackinTotalg-wider text-left">Status Bayar</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $initial = strtoupper(substr($sale->customer->name, 0, 1));
                            $colors = ['bg-primary/10 text-primary', 'bg-secondary-container/50 text-secondary', 'bg-tertiary/10 text-tertiary', 'bg-error/10 text-error'];
                            $color = $colors[crc32($sale->customer->id) % 4];
                        @endphp
                        <tr class="border-b border-outline-variant/20 hover:bg-primary/5 transition-colors group">
                            <td class="px-4 py-3">
                                <span class="font-label-numeric text-primary font-bold">#NQ-{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-on-surface group-hover:text-primary transition-colors">{{ $sale->customer->name }}</p>
                                        <p class="text-xs text-on-surface-variant">#CUST-{{ str_pad($sale->customer->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-on-surface-variant">
                                {{ \Carbon\Carbon::parse($sale->sales_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-label-numeric text-on-surface font-bold">Rp {{ number_format($sale->total_price) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($sale->payment_status === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-green-600"></span>
                                        Lunas
                                    </span>
                                @elseif($sale->payment_status === 'cicil')
                                    <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                        Cicil (Rp {{ number_format($sale->paid_amount) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('sales.show', $sale->id) }}"
                                       title="Lihat Detail"
                                       class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <button @click="openEditStatus({{ $sale->id }}, '{{ $sale->payment_status }}', {{ $sale->paid_amount ?? 0 }}, {{ $sale->total_price }})"
                                            title="Ubah Status Pembayaran"
                                            class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">payments</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-on-surface-variant">
                                <span class="material-symbols-outlined text-6xl text-outline-variant mb-4 inline-block">receipt_long</span>
                                <p class="text-lg font-medium text-on-surface-variant mb-2">Belum ada data penjualan</p>
                                <p class="text-sm text-on-surface-variant mb-6">Buat transaksi penjualan pertama Anda</p>
                                <button
                                    @click="$dispatch('open-modal', 'add-sale')"
                                    class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined">add_shopping_cart</span>
                                    Tambah Penjualan
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-outline-variant/20 bg-surface-container-low/30">
            {{ $sales->links() }}
        </div>

    </div>

{{-- Modal Tambah Penjualan --}}
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
                        <p class="text-sm text-on-surface-variant">Input pesanan air ke sistem</p>
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

{{-- Modal Edit Status Pembayaran --}}
<x-modal name="edit-payment-status" focusable>
    <form id="edit-payment-form" method="POST" @submit="submitting = true">
        @csrf
        @method('PUT')

        <div class="glass-panel rounded-xl shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">payments</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Ubah Status Pembayaran</h2>
                        <p class="text-sm text-on-surface-variant">Total: <span class="font-semibold" x-text="'Rp ' + Number(editSaleTotal).toLocaleString('id-ID')"></span> — Dibayar: <span class="font-semibold" x-text="'Rp ' + Number(editPaidAmount).toLocaleString('id-ID')"></span></p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'edit-payment-status')"
                    class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            @if($errors->any())
                <div class="mx-6 mt-4 bg-error-container text-on-error-container p-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Status Pembayaran</label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex-1 cursor-pointer group min-w-[100px]">
                            <input type="radio" name="payment_status" value="lunas" x-model="editStatus" class="hidden peer">
                            <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="editStatus === 'lunas' ? 'text-primary' : 'text-outline'">check_circle</span>
                                <span class="text-sm font-semibold" :class="editStatus === 'lunas' ? 'text-primary' : 'text-on-surface'">Lunas</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer group min-w-[100px]">
                            <input type="radio" name="payment_status" value="cicil" x-model="editStatus" class="hidden peer">
                            <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="editStatus === 'cicil' ? 'text-primary' : 'text-outline'">schedule</span>
                                <span class="text-sm font-semibold" :class="editStatus === 'cicil' ? 'text-primary' : 'text-on-surface'">Cicilan</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer group min-w-[100px]">
                            <input type="radio" name="payment_status" value="belum" x-model="editStatus" class="hidden peer">
                            <div class="p-4 border border-outline-variant rounded-xl peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-outline" style="font-variation-settings:'FILL'1;" :class="editStatus === 'belum' ? 'text-primary' : 'text-outline'">error_outline</span>
                                <span class="text-sm font-semibold" :class="editStatus === 'belum' ? 'text-primary' : 'text-on-surface'">Belum Bayar</span>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('payment_status')" />
                </div>

                <div x-show="editStatus === 'cicil'" x-cloak>
                    <label for="additional_payment" class="block text-sm font-medium text-on-surface-variant mb-1">Tambahan Bayar</label>
                    <p class="text-xs text-on-surface-variant mb-2">Jumlah yang dibayarkan sekarang</p>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                        <input type="number" id="additional_payment" name="additional_payment" step="0.01" min="0"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm">
                    </div>
                    <x-input-error :messages="$errors->get('additional_payment')" />
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'edit-payment-status')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan
                </button>
            </div>

        </div>
    </form>
</x-modal>

</div>

</x-app-layout>

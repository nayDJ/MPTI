@section('title', 'Laporan & Analitik')
@section('topbar-title', 'Dashboard Laporan')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8"
     x-data="{ view: '{{ $view }}', ready: true }"
     x-init="$watch('view', () => { ready = false; $nextTick(() => { ready = true; }) })">

    <div class="mb-8">
        <nav class="text-sm text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
            <span class="mx-1">›</span>
            <span class="text-on-surface-variant">Laporan</span>
        </nav>
        <h1 class="text-3xl font-bold text-primary">Laporan Sistem</h1>
        <p class="text-on-surface-variant mt-1">Analitik performa real-time &amp; pengawasan keuangan</p>
    </div>

    <div class="flex gap-2 mb-8">
        <a href="{{ route('reports.index', ['period' => 'bulan', 'view' => $view]) }}"
           class="px-5 py-2 rounded-lg text-sm font-medium transition"
           :class="'{{ $period }}' === 'bulan' ? 'bg-primary text-white' : 'bg-white text-on-surface-variant border border-outline-variant/30 hover:bg-surface-container-low'">
           Bulan
        </a>
        <a href="{{ route('reports.index', ['period' => 'tahun', 'view' => $view]) }}"
           class="px-5 py-2 rounded-lg text-sm font-medium transition"
           :class="'{{ $period }}' === 'tahun' ? 'bg-primary text-white' : 'bg-white text-on-surface-variant border border-outline-variant/30 hover:bg-surface-container-low'">
           Tahun
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-outline-variant/30 rounded-xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-primary"></div>
            <div class="flex items-start mb-4">
                <span class="material-symbols-outlined text-primary bg-[#bde9ff] p-2 rounded-lg" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
            </div>
            <p class="text-sm text-on-surface-variant">Total Pendapatan <span class="font-light">(Uang Masuk)</span></p>
            <h3 class="text-3xl font-bold text-on-surface mt-1">Rp {{ number_format($totalRevenue) }}</h3>
            <p class="text-[11px] text-on-surface-variant/60 mt-4 font-medium tracking-wider">{{ $periodLabel }}</p>
        </div>

        <div class="bg-white border border-outline-variant/30 rounded-xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-[#8f5919]"></div>
            <div class="flex items-start mb-4">
                <span class="material-symbols-outlined text-[#724200] bg-[#ffb870] p-2 rounded-lg" style="font-variation-settings: 'FILL' 1;">payments</span>
            </div>
            <p class="text-sm text-on-surface-variant">Laba Bersih</p>
            <h3 class="text-3xl font-bold text-on-surface mt-1">Rp {{ number_format($totalProfit) }}</h3>
            <p class="text-[11px] text-on-surface-variant/60 mt-4 font-medium tracking-wider">{{ $periodLabel }}</p>
        </div>

        <div class="bg-white border border-outline-variant/30 rounded-xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-[#ba1a1a]"></div>
            <div class="flex items-start mb-4">
                <span class="material-symbols-outlined text-[#ba1a1a] bg-[#ffdad6] p-2 rounded-lg" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
            </div>
            <p class="text-sm text-on-surface-variant">Total Piutang</p>
            <h3 class="text-3xl font-bold text-on-surface mt-1">Rp {{ number_format($outstandingReceivables) }}</h3>
            <p class="text-[11px] text-on-surface-variant/60 mt-4 font-medium tracking-wider">{{ $periodLabel }}</p>
        </div>

        <div class="bg-white border border-outline-variant/30 rounded-xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-[#545f73]"></div>
            <div class="flex items-start mb-4">
                <span class="material-symbols-outlined text-[#545f73] bg-[#d5e0f8] p-2 rounded-lg" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
            </div>
            <p class="text-sm text-on-surface-variant">Total Pengeluaran</p>
            <h3 class="text-3xl font-bold text-on-surface mt-1">Rp {{ number_format($totalExpense) }}</h3>
            <p class="text-[11px] text-on-surface-variant/60 mt-4 font-medium tracking-wider">{{ $periodLabel }}</p>
        </div>
    </div>

    <div class="bg-white border border-outline-variant/30 rounded-xl p-6 shadow-sm mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-on-surface" x-text="view === 'income' ? 'Pemasukan (Tren Pendapatan Bulanan)' : 'Pengeluaran (Tren Pengeluaran Bulanan)'"></h3>
                <p class="text-sm text-on-surface-variant">Ikhtisar mendetail arus keuangan perusahaan</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex bg-[#f1f4f6] p-1 rounded-lg">
                    <button @click="view = 'income'"
                        class="px-4 py-1.5 rounded-md text-sm font-bold transition"
                        :class="view === 'income' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:text-primary'">
                        Pemasukan
                    </button>
                    <button @click="view = 'expense'"
                        class="px-4 py-1.5 rounded-md text-sm font-bold transition"
                        :class="view === 'expense' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:text-primary'">
                        Pengeluaran
                    </button>
                </div>
                <button class="p-1.5 text-on-surface-variant hover:bg-[#f1f4f6] rounded-lg">
                </button>
            </div>
        </div>

        @php
            $incomeSteps = [];
            $incomeMax = $maxIncome > 0 ? $maxIncome : 1;
            for ($i = 0; $i <= 4; $i++) $incomeSteps[] = ($incomeMax / 4) * $i;
            $expenseSteps = [];
            $expenseMax = $maxExpense > 0 ? $maxExpense : 1;
            for ($i = 0; $i <= 4; $i++) $expenseSteps[] = ($expenseMax / 4) * $i;
        @endphp

        <div x-show="view === 'income'" class="h-96 w-full flex items-end gap-3 px-4 pb-4 border-l border-b border-outline-variant/30 relative">
            <div class="absolute -left-14 bottom-0 h-full flex flex-col justify-between text-right w-12 pb-4">
                @foreach(array_reverse($incomeSteps) as $step)
                    <span class="text-[10px] font-medium text-on-surface-variant/60">Rp {{ number_format($step) }}</span>
                @endforeach
            </div>
            @for($m = 0; $m < 12; $m++)
                @php
                    $heightPct = $incomeMax > 0 ? max(1, ($chartIncome[$m] / $incomeMax) * 100) : 0;
                    $isCurrent = ($m + 1) === $currentMonth;
                @endphp
                <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full group">
                    <div class="relative w-full flex justify-center">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[#181c1e] text-white px-3 py-1.5 rounded text-[10px] whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none shadow-lg">Rp {{ number_format($chartIncome[$m]) }}</div>
                    </div>
                    <div class="w-full rounded-t-lg hover:brightness-110 cursor-pointer bar-item"
                         style="height: {{ $heightPct }}%; transition-delay: {{ $m * 0.04 }}s; {{ $isCurrent ? 'background: #0F6E8C;' : 'background: rgba(15, 110, 140, 0.25);' }}"
                         :style="{ transform: ready ? 'scaleY(1)' : 'scaleY(0)' }"></div>
                    <span class="text-[11px] font-medium {{ $isCurrent ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $monthNames[$m] }}</span>
                </div>
            @endfor
        </div>

        <div x-show="view === 'expense'" class="h-96 w-full flex items-end gap-3 px-4 pb-4 border-l border-b border-outline-variant/30 relative">
            <div class="absolute -left-14 bottom-0 h-full flex flex-col justify-between text-right w-12 pb-4">
                @foreach(array_reverse($expenseSteps) as $step)
                    <span class="text-[10px] font-medium text-on-surface-variant/60">Rp {{ number_format($step) }}</span>
                @endforeach
            </div>
            @for($m = 0; $m < 12; $m++)
                @php
                    $heightPct = $expenseMax > 0 ? max(1, ($chartExpense[$m] / $expenseMax) * 100) : 0;
                    $isCurrent = ($m + 1) === $currentMonth;
                @endphp
                <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full group">
                    <div class="relative w-full flex justify-center">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[#181c1e] text-white px-3 py-1.5 rounded text-[10px] whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none shadow-lg">Rp {{ number_format($chartExpense[$m]) }}</div>
                    </div>
                    <div class="w-full rounded-t-lg hover:brightness-110 cursor-pointer bar-item"
                         style="height: {{ $heightPct }}%; transition-delay: {{ $m * 0.04 }}s; {{ $isCurrent ? 'background: #EF4444;' : 'background: rgba(239, 68, 68, 0.25);' }}"
                         :style="{ transform: ready ? 'scaleY(1)' : 'scaleY(0)' }"></div>
                    <span class="text-[11px] font-medium {{ $isCurrent ? 'text-red-500 font-bold' : 'text-on-surface-variant' }}">{{ $monthNames[$m] }}</span>
                </div>
            @endfor
        </div>
    </div>

    <div class="bg-white border border-outline-variant/30 rounded-xl shadow-sm mb-8">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="text-xl font-bold text-on-surface">Produk Terlaris <span class="text-sm font-normal text-on-surface-variant ml-2">{{ number_format($totalUnitsSold) }} total terjual</span></h3>
            <a href="{{ route('products.index') }}" class="text-primary text-sm font-bold hover:underline">Lihat Semua</a>
        </div>
        <div class="divide-y divide-outline-variant/20 max-h-[220px] overflow-y-auto custom-scrollbar">
            @forelse($topProducts as $item)
                <div class="p-4 flex items-center gap-4 hover:bg-surface-container-low transition-colors">
                    <div class="w-11 h-11 rounded-lg bg-[#f1f4f6] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[#6f787e]">inventory_2</span>
                    </div>
                    <div class="flex-grow min-w-0">
                        <h4 class="font-semibold text-on-surface truncate">{{ $item->product->name }}</h4>
                        <p class="text-sm text-on-surface-variant">{{ number_format($item->total_sold) }} terjual</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-primary">Rp {{ number_format($item->total_revenue) }}</p>
                        @if($loop->first && $item->total_sold > 0)
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">Terlaris</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-on-surface-variant">Belum ada data produk</div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-outline-variant/30 rounded-xl shadow-sm">
            <div class="p-6 border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="text-xl font-bold text-on-surface">Pelanggan Teratas</h3>
                <a href="{{ route('customers.index') }}" class="text-primary text-sm font-bold hover:underline">Kelola Akun</a>
            </div>
            <div class="divide-y divide-outline-variant/20 max-h-[400px] overflow-y-auto custom-scrollbar">
                @forelse($topCustomers as $customer)
                    <div class="p-4 flex items-center gap-4 hover:bg-surface-container-low transition-colors">
                        <div class="w-9 h-9 rounded-full bg-[#d5e0f8] flex items-center justify-center flex-shrink-0 font-bold text-[#545f73] text-sm">
                            {{ strtoupper(substr($customer->customer->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <h4 class="font-semibold text-on-surface truncate">{{ $customer->customer->name }}</h4>
                            <p class="text-sm text-on-surface-variant">{{ $customer->total_orders }} Pesanan</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-primary">Rp {{ number_format($customer->total_revenue) }}</p>
                            <p class="text-[10px] font-medium text-on-surface-variant/60">{{ $loop->first ? 'Mitra Utama' : ($loop->index < 2 ? 'VIP' : 'Aktif') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-on-surface-variant">Belum ada data pelanggan</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-outline-variant/30 rounded-xl shadow-sm">
            <div class="p-6 border-b border-outline-variant/30">
                <h3 class="text-xl font-bold text-on-surface">Debitur Teratas</h3>
            </div>
            <div class="divide-y divide-outline-variant/20 max-h-[400px] overflow-y-auto custom-scrollbar">
                @forelse($topDebtors as $debtor)
                    <div class="p-4 flex items-center gap-4 hover:bg-surface-container-low transition-colors">
                        <div class="w-9 h-9 rounded-full bg-[#ffdad6] flex items-center justify-center flex-shrink-0 font-bold text-[#ba1a1a] text-sm">
                            {{ strtoupper(substr($debtor->customer->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <h4 class="font-semibold text-on-surface truncate">{{ $debtor->customer->name }}</h4>
                            <p class="text-sm text-on-surface-variant">{{ $debtor->total_transaksi }} transaksi</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold {{ $debtor->sisa_utang >= 100000 ? 'text-[#ba1a1a]' : 'text-[#8f5919]' }}">
                                Rp {{ number_format($debtor->sisa_utang) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-on-surface-variant">Tidak ada data utang.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white border border-outline-variant/30 rounded-xl shadow-sm mb-8">
        <template x-if="view === 'income'">
            <div>
                <div class="p-6 border-b border-outline-variant/30">
                    <h3 class="text-xl font-bold text-on-surface">Penjualan Terbaru</h3>
                </div>
                <div class="overflow-x-auto max-h-[220px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#f1f4f6] border-b border-outline-variant/20">
                                <th class="text-left p-4 text-sm font-semibold text-on-surface-variant">Tanggal</th>
                                <th class="text-left p-4 text-sm font-semibold text-on-surface-variant">Pelanggan</th>
                                <th class="text-right p-4 text-sm font-semibold text-on-surface-variant">Total</th>
                                <th class="text-center p-4 text-sm font-semibold text-on-surface-variant">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($sale->sales_date)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="p-4 text-sm font-medium">{{ $sale->customer->name ?? '-' }}</td>
                                    <td class="p-4 text-sm text-right font-semibold">Rp {{ number_format($sale->total_price) }}</td>
                                    <td class="p-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $sale->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : ($sale->payment_status === 'cicil' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600') }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-on-surface-variant">Belum ada penjualan di periode ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="view === 'expense'">
            <div>
                <div class="p-6 border-b border-outline-variant/30">
                    <h3 class="text-xl font-bold text-on-surface">Pengeluaran Terbaru</h3>
                </div>
                <div class="overflow-x-auto max-h-[220px] overflow-y-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#f1f4f6] border-b border-outline-variant/20">
                                <th class="text-left p-4 text-sm font-semibold text-on-surface-variant">Tanggal</th>
                                <th class="text-left p-4 text-sm font-semibold text-on-surface-variant">Deskripsi</th>
                                <th class="text-left p-4 text-sm font-semibold text-on-surface-variant">Kategori</th>
                                <th class="text-right p-4 text-sm font-semibold text-on-surface-variant">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExpenses as $expense)
                                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                                    <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($expense->expense_date)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="p-4 text-sm font-medium">{{ $expense->description }}</td>
                                    <td class="p-4">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-right font-semibold text-[#ba1a1a]">Rp {{ number_format($expense->amount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-on-surface-variant">Belum ada pengeluaran di periode ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>

    <div class="bg-white/80 backdrop-blur-lg border border-white/30 rounded-2xl p-6 shadow-lg">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex flex-col md:flex-row items-center gap-6 w-full md:w-auto">
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1 ml-2">Rentang Tanggal</label>
                    <div class="flex items-center bg-white border border-outline-variant/30 rounded-lg px-4 py-2">
                        <span class="material-symbols-outlined text-on-surface-variant mr-2 text-lg">calendar_today</span>
                        <span class="text-sm text-on-surface">{{ $periodLabel }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="{{ route('reports.pdf', ['period' => $period, 'view' => $view]) }}"
                   class="btn-pdf flex-1 md:flex-none">
                    <span class="material-symbols-outlined icon">picture_as_pdf</span>
                    <span class="text">Ekspor PDF</span>
                </a>
            </div>
        </div>
    </div>

</div>

</x-app-layout>

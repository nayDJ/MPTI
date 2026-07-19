@section('title', 'Detail Pelanggan - ' . $customer->name)
@section('topbar-title', 'Detail Pelanggan')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8">

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-8 mb-6">

        <div class="flex items-start">

            <a href="{{ route('customers.index') }}"
                class="text-on-surface-variant hover:text-primary mr-4 mt-1 transition">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>

            <div class="flex-1">
                <nav class="text-sm text-on-surface-variant/60 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
                    <span class="material-symbols-outlined text-[16px] align-middle mx-1">chevron_right</span>
                    <a href="{{ route('customers.index') }}" class="hover:text-primary transition">Data Pelanggan</a>
                    <span class="material-symbols-outlined text-[16px] align-middle mx-1">chevron_right</span>
                    <span class="text-on-surface-variant">Detail Pelanggan</span>
                </nav>

                <h1 class="text-3xl font-bold text-primary">Detail Pelanggan</h1>
                <p class="text-on-surface-variant mt-1">Informasi lengkap pelanggan NNQUA</p>
            </div>

        </div>

    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-8 mb-6">
        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <p class="text-on-surface-variant text-sm">Nama</p>
                <h3 class="font-bold text-lg text-on-surface">{{ $customer->name }}</h3>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm">No HP</p>
                <h3 class="font-bold text-lg text-on-surface">{{ $customer->phone }}</h3>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm">Alamat</p>
                <h3 class="font-bold text-lg text-on-surface">{{ $customer->address }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6">
            <p class="text-sm text-on-surface-variant">Total Transaksi</p>
            <h2 class="text-3xl font-bold text-on-surface mt-3">{{ $totalTransactions }}</h2>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6">
            <p class="text-sm text-on-surface-variant">Total Pembelian</p>
            <h2 class="text-3xl font-bold text-green-600 mt-3">Rp {{ number_format($totalPurchase) }}</h2>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6">
            <p class="text-sm text-on-surface-variant">Total Dibayar</p>
            <h2 class="text-3xl font-bold text-yellow-600 mt-3">Rp {{ number_format($totalPaid) }}</h2>
        </div>
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-6">
            <p class="text-sm text-on-surface-variant">Sisa Hutang</p>
            <h2 class="text-3xl font-bold text-red-600 mt-3">Rp {{ number_format($totalDebt) }}</h2>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant/20">
            <h2 class="text-xl font-bold text-on-surface">Riwayat Transaksi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">ID</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Produk</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Total</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Dibayar</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="border-b border-outline-variant/20 hover:bg-primary/5 transition-colors">
                            <td class="px-4 py-3 font-label-numeric text-primary font-bold">#NQ-{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-sm text-on-surface-variant">{{ \Carbon\Carbon::parse($sale->sales_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @foreach($sale->items as $item)
                                    <div class="text-on-surface">{{ $item->product->name }} <span class="text-on-surface-variant">({{ $item->quantity }}x)</span></div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 font-label-numeric text-on-surface font-bold">Rp {{ number_format($sale->total_price) }}</td>
                            <td class="px-4 py-3">
                                @if($sale->payment_status === 'lunas')
                                    <span class="text-green-600 font-semibold">Rp {{ number_format($sale->total_price) }}</span>
                                @elseif($sale->payment_status === 'cicil')
                                    <span class="text-on-surface-variant">Rp {{ number_format($sale->paid_amount) }}</span>
                                @else
                                    <span class="text-on-surface-variant/50">Rp 0</span>
                                @endif
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
                                        Cicil
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('sales.show', $sale->id) }}"
                                   title="Lihat"
                                   class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-lg transition-colors inline-flex">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-on-surface-variant">
                                <span class="material-symbols-outlined text-6xl text-outline mb-4 inline-block">receipt_long</span>
                                <p class="text-lg font-medium text-on-surface-variant mb-2">Belum ada transaksi</p>
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

</div>

</x-app-layout>

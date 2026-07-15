@section('title', 'Detail Customer - ' . $customer->name)
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8">

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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">

        <div class="flex items-start">

            <a href="{{ route('customers.index') }}"
                class="text-slate-500 hover:text-[#0F6E8C] mr-4 mt-1 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div class="flex-1">
                <nav class="text-sm text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                    <span class="mx-1">›</span>
                    <a href="{{ route('customers.index') }}" class="hover:text-[#0F6E8C] transition">Data Customer</a>
                    <span class="mx-1">›</span>
                    <span class="text-slate-600">Detail Customer</span>
                </nav>

                <h1 class="text-3xl font-bold text-[#0F6E8C]">Detail Customer</h1>
                <p class="text-gray-500 mt-1">Informasi lengkap pelanggan NNQUA</p>
            </div>

        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <p class="text-gray-500 text-sm">Nama</p>
                <h3 class="font-bold text-lg">{{ $customer->name }}</h3>
            </div>
            <div>
                <p class="text-gray-500 text-sm">No HP</p>
                <h3 class="font-bold text-lg">{{ $customer->phone }}</h3>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Alamat</p>
                <h3 class="font-bold text-lg">{{ $customer->address }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <h2 class="text-3xl font-bold text-slate-800 mt-3">{{ $totalTransactions }}</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Pembelian</p>
            <h2 class="text-3xl font-bold text-green-600 mt-3">Rp {{ number_format($totalPurchase) }}</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Dibayar</p>
            <h2 class="text-3xl font-bold text-yellow-600 mt-3">Rp {{ number_format($totalPaid) }}</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Sisa Hutang</p>
            <h2 class="text-3xl font-bold text-red-600 mt-3">Rp {{ number_format($totalDebt) }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-slate-800">Riwayat Transaksi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="p-4 text-left">ID</th>
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Produk</th>
                        <th class="p-4 text-left">Total</th>
                        <th class="p-4 text-left">Dibayar</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-4 font-medium">#{{ $sale->id }}</td>
                            <td class="p-4 text-gray-600">{{ \Carbon\Carbon::parse($sale->sales_date)->format('d M Y') }}</td>
                            <td class="p-4">
                                @foreach($sale->items as $item)
                                    <div>{{ $item->product->name }} ({{ $item->quantity }}x)</div>
                                @endforeach
                            </td>
                            <td class="p-4 font-bold text-green-600">Rp {{ number_format($sale->total_price) }}</td>
                            <td class="p-4">
                                @if($sale->payment_status === 'lunas')
                                    <span class="text-green-600">Rp {{ number_format($sale->total_price) }}</span>
                                @elseif($sale->payment_status === 'cicil')
                                    Rp {{ number_format($sale->paid_amount) }}
                                @else
                                    <span class="text-gray-400">Rp 0</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($sale->payment_status === 'lunas')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Lunas</span>
                                @elseif($sale->payment_status === 'cicil')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Cicil</span>
                                @else
                                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">Belum</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('sales.show', $sale->id) }}"
                                   title="Lihat"
                                   class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition relative group inline-block">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Lihat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8 text-gray-500">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $sales->links() }}
        </div>
    </div>

</div>

</x-app-layout>

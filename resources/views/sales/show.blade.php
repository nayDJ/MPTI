@section('title', 'Detail Transaksi #' . $sale->id)
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

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">

            <div class="flex items-start">

                <a href="{{ route('sales.index') }}"
                    class="text-slate-500 hover:text-[#0F6E8C] mr-4 mt-1 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex-1 flex justify-between items-start">

                    <div>

                        <nav class="text-sm text-slate-400 mb-1">
                            <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                            <span class="mx-1">›</span>
                            <a href="{{ route('sales.index') }}" class="hover:text-[#0F6E8C] transition">Data Penjualan</a>
                            <span class="mx-1">›</span>
                            <span class="text-slate-600">Detail #{{ $sale->id }}</span>
                        </nav>

                        <h1 class="text-3xl font-bold text-[#0F6E8C]">

                            Detail Transaksi #{{ $sale->id }}

                        </h1>

                        <p class="text-gray-500 mt-1">

                            Informasi transaksi penjualan

                        </p>

                    </div>

                @if($sale->payment_status === 'lunas')
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">Lunas</span>
                @elseif($sale->payment_status === 'cicil')
                    <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-semibold">Cicil</span>
                @else
                    <span class="bg-red-100 text-red-600 px-4 py-2 rounded-full text-sm font-semibold">Belum Dibayar</span>
                @endif

            </div>

            </div>

        </div>

        {{-- Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">

            <div class="grid md:grid-cols-3 gap-6">

                <div>

                    <p class="text-gray-500 text-sm">
                        Customer
                    </p>

                    <h3 class="font-bold text-lg">
                        {{ $sale->customer->name }}
                    </h3>

                </div>

                <div>

                    <p class="text-gray-500 text-sm">
                        Tanggal
                    </p>

                    <h3 class="font-bold text-lg">
                        {{ \Carbon\Carbon::parse($sale->sales_date)->format('d M Y') }}
                    </h3>

                </div>

                <div>

                    <p class="text-gray-500 text-sm">
                        Total
                    </p>

                    <h3 class="font-bold text-lg text-green-600">
                        Rp {{ number_format($sale->total_price) }}
                    </h3>

                </div>

                @if($sale->payment_status === 'cicil')
                <div>
                    <p class="text-gray-500 text-sm">Dibayar</p>
                    <h3 class="font-bold text-lg text-yellow-600">Rp {{ number_format($sale->paid_amount) }}</h3>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Sisa</p>
                    <h3 class="font-bold text-lg text-red-600">Rp {{ number_format($sale->total_price - $sale->paid_amount) }}</h3>
                </div>
                @endif

            </div>

        </div>

        {{-- Produk --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="p-6 border-b">

                <h2 class="text-xl font-bold">

                    Detail Produk

                </h2>

            </div>

            <table class="w-full">

                <thead>

                    <tr class="bg-slate-50 border-b">

                        <th class="p-4 text-left">
                            Produk
                        </th>

                        <th class="p-4 text-left">
                            Quantity
                        </th>

                        <th class="p-4 text-left">
                            Subtotal
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($sale->items as $item)

                    <tr class="border-b">

                        <td class="p-4">

                            {{ $item->product->name }}

                        </td>

                        <td class="p-4">

                            {{ $item->quantity }}

                        </td>

                        <td class="p-4 font-bold text-green-600">

                            Rp {{ number_format($item->subtotal) }}

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-app-layout>
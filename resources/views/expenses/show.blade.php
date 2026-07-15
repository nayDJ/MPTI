@section('title', 'Detail Pengeluaran')
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
            <a href="{{ route('expenses.index') }}"
                class="text-slate-500 hover:text-[#0F6E8C] mr-4 mt-1 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <nav class="text-sm text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                    <span class="mx-1">›</span>
                    <a href="{{ route('expenses.index') }}" class="hover:text-[#0F6E8C] transition">Data Pengeluaran</a>
                    <span class="mx-1">›</span>
                    <span class="text-slate-600">Detail Pengeluaran</span>
                </nav>
                <h1 class="text-3xl font-bold text-[#0F6E8C]">Detail Pengeluaran</h1>
                <p class="text-gray-500 mt-1">Informasi lengkap pengeluaran NNQUA</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <p class="text-gray-500 text-sm mb-1">Deskripsi</p>
                <p class="font-semibold text-lg">{{ $expense->description }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Kategori</p>
                <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $expense->category }}
                </span>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Jumlah</p>
                <p class="font-bold text-lg text-red-600">Rp {{ number_format($expense->amount) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Tanggal</p>
                <p class="font-semibold text-lg">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Dibuat</p>
                <p class="text-slate-600">{{ $expense->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Terakhir diupdate</p>
                <p class="text-slate-600">{{ $expense->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t flex gap-3">
            <a href="{{ route('expenses.index') }}"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">
                Kembali
            </a>
        </div>
    </div>

</div>

</x-app-layout>

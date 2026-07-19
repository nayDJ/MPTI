@section('title', 'Detail Pengeluaran')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8">

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8 mb-6">
        <div class="flex items-start">
            <a href="{{ route('expenses.index') }}"
                class="text-on-surface-variant hover:text-primary mr-4 mt-1 transition">
                <span class="material-symbols-outlined">chevron_left</span>
            </a>
            <div class="flex-1">
                <nav class="text-sm text-on-surface-variant mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
                    <span class="mx-1">›</span>
                    <a href="{{ route('expenses.index') }}" class="hover:text-primary transition">Data Pengeluaran</a>
                    <span class="mx-1">›</span>
                    <span class="text-on-surface">Detail Pengeluaran</span>
                </nav>
                <h1 class="text-3xl font-bold text-primary">Detail Pengeluaran</h1>
                <p class="text-on-surface-variant mt-1">Informasi lengkap pengeluaran NNQUA</p>
            </div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Deskripsi</p>
                <p class="font-semibold text-lg text-on-surface">{{ $expense->description }}</p>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Kategori</p>
                @php
                    $badgeColors = [
                        ['bg' => '#dbeafe', 'text' => '#1e40af'],
                        ['bg' => '#fef3c7', 'text' => '#92400e'],
                        ['bg' => '#fee2e2', 'text' => '#991b1b'],
                        ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                        ['bg' => '#ccfbf1', 'text' => '#0f766e'],
                        ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                        ['bg' => '#ffedd5', 'text' => '#9a3412'],
                        ['bg' => '#fce7f3', 'text' => '#9d174d'],
                        ['bg' => '#cffafe', 'text' => '#0e7490'],
                        ['bg' => '#f0fdf4', 'text' => '#166534'],
                        ['bg' => '#f5f5f4', 'text' => '#44403c'],
                    ];
                    $bc = $badgeColors[abs(crc32($expense->category)) % count($badgeColors)];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block" style="background-color: {{ $bc['bg'] }}; color: {{ $bc['text'] }};">
                    {{ $expense->category }}
                </span>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Jumlah</p>
                <p class="font-bold text-lg text-on-surface">Rp {{ number_format($expense->amount) }}</p>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Tanggal</p>
                <p class="font-semibold text-lg text-on-surface">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Dibuat</p>
                <p class="text-on-surface-variant">{{ $expense->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-on-surface-variant text-sm mb-1">Terakhir diupdate</p>
                <p class="text-on-surface-variant">{{ $expense->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-outline-variant/30 flex gap-3">
            <a href="{{ route('expenses.index') }}"
                class="px-5 py-2.5 bg-surface-container-high hover:bg-surface-container-highest text-on-surface rounded-lg text-sm font-medium transition">
                Kembali
            </a>
        </div>
    </div>

</div>

</x-app-layout>

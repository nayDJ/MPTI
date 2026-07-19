@section('title', 'Notifikasi')
@section('topbar-title', 'Notifikasi')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8">

    <div class="flex justify-between items-start mb-8">
        <div>
            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
                <span class="material-symbols-outlined text-[16px] align-middle mx-1">chevron_right</span>
                <span class="text-on-surface-variant">Notifikasi</span>
            </nav>
            <h1 class="text-3xl font-bold text-primary">Notifikasi</h1>
            <p class="text-on-surface-variant mt-1">Riwayat aktivitas sistem</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-outline-variant/20">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select name="action_type" onchange="this.form.submit()"
                        class="pl-3 pr-8 py-2 border border-outline rounded-lg text-sm bg-surface-container-low outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Semua Notifikasi</option>
                        <option value="auth" {{ $actionType === 'auth' ? 'selected' : '' }}>Login / Logout</option>
                        <option value="sale" {{ $actionType === 'sale' ? 'selected' : '' }}>Penjualan</option>
                        <option value="customer" {{ $actionType === 'customer' ? 'selected' : '' }}>Pelanggan</option>
                        <option value="product" {{ $actionType === 'product' ? 'selected' : '' }}>Produk</option>
                        <option value="expense" {{ $actionType === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                @if($actionType)
                    <a href="{{ route('notifications.index') }}" class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="divide-y divide-outline-variant/20">
            @forelse($notifications as $n)
                <div class="px-6 py-4 flex items-start gap-4 hover:bg-primary/5 transition-colors {{ !$n->is_read ? 'bg-primary/5' : '' }}">
                    <div class="mt-0.5">
                        @if($n->type === 'success')
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                        @elseif($n->type === 'error')
                            <span class="material-symbols-outlined text-red-500">error</span>
                        @else
                            <span class="material-symbols-outlined text-primary">info</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-on-surface truncate">{{ $n->title }}</p>
                            <span class="text-xs text-on-surface-variant whitespace-nowrap">{{ $n->created_at->timezone('Asia/Jakarta')->format('g:i A') }}</span>
                        </div>
                        <p class="text-sm text-on-surface-variant mt-0.5">{{ $n->message }}</p>
                    </div>
                    @if(!$n->is_read)
                        <span class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></span>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 text-on-surface-variant">
                    <span class="material-symbols-outlined text-6xl text-outline mb-4 inline-block">notifications_off</span>
                    <p class="text-lg font-medium">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-outline-variant/20 bg-surface-container-low/30">
            {{ $notifications->links() }}
        </div>
    </div>

</div>

</x-app-layout>

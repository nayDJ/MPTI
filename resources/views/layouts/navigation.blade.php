<aside
    :class="{
        'w-64': sidebarOpen && !isMobile,
        'w-16': !sidebarOpen && !isMobile,
        'translate-x-0': sidebarOpen && isMobile,
        '-translate-x-full': !sidebarOpen && isMobile,
    }"
    class="fixed left-0 top-0 h-screen bg-white shadow-md z-50 flex flex-col overflow-hidden"
    :class="initialized ? 'transition-all duration-300' : ''">

    {{-- Header: Logo + Hamburger --}}
    <div class="flex items-center px-4 h-[72px] border-b border-gray-100"
         :class="sidebarOpen || isMobile ? 'justify-between' : 'justify-center'">
        {{-- Logo + Brand --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0"
           x-show="sidebarOpen || isMobile">
            <svg class="w-11 h-11 flex-shrink-0" viewBox="272 35 130 180" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="smoothGrad2" x1="15%" y1="0%" x2="85%" y2="100%">
                        <stop offset="0%" stop-color="#63A1B4"/>
                        <stop offset="50%" stop-color="#0F6E8C"/>
                        <stop offset="100%" stop-color="#0B4D62"/>
                    </linearGradient>
                </defs>
                <path d="M340,40 C340,40 396,107.2 396,149.2 C396,181.4 370.8,205.2 340,205.2 C309.2,205.2 284,181.4 284,149.2 C284,107.2 340,40 340,40 Z" fill="url(#smoothGrad2)"/>
                <ellipse cx="318" cy="95" rx="24" ry="34" fill="#ffffff" opacity="0.25"/>
                <ellipse cx="313" cy="88" rx="9" ry="13" fill="#ffffff" opacity="0.35"/>
            </svg>
            <div x-show="sidebarOpen || isMobile" class="whitespace-nowrap">
                <h1 class="text-lg font-bold text-[#0F6E8C] leading-tight">NNQUA</h1>
                <p class="text-xs text-on-surface-variant leading-tight">Water Management</p>
            </div>
        </a>

        {{-- Hamburger --}}
        <button @click="toggleSidebar()"
                class="flex flex-col items-center justify-center w-10 h-10 gap-[5px] rounded-lg hover:bg-gray-100 transition-colors flex-shrink-0">
            <div class="w-5 h-[2.5px] bg-outline rounded transition-all duration-300"
                 :class="sidebarOpen ? 'rotate-45 translate-y-[7.5px]' : ''"></div>
            <div class="w-5 h-[2.5px] bg-outline rounded transition-all duration-300"
                 :class="sidebarOpen ? 'opacity-0' : ''"></div>
            <div class="w-5 h-[2.5px] bg-outline rounded transition-all duration-300"
                 :class="sidebarOpen ? '-rotate-45 -translate-y-[7.5px]' : ''"></div>
        </button>
    </div>

    {{-- Nav Items --}}
    <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-1">
        {{-- Dashboard --}}
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            icon="dashboard" label="Dashboard" />

        {{-- Produk --}}
        <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')"
            icon="inventory_2" label="Produk">
            <x-slot name="badge">
                @if($criticalStockCount > 0)
                    <span x-show="sidebarOpen || isMobile"
                          class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $criticalStockCount }}</span>
                    <span x-show="!sidebarOpen && !isMobile"
                          class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-red-500"></span>
                @endif
            </x-slot>
        </x-sidebar-link>

        {{-- Pelanggan --}}
        <x-sidebar-link :href="route('customers.index')" :active="request()->routeIs('customers.*')"
            icon="group" label="Pelanggan" />

        {{-- Penjualan --}}
        <x-sidebar-link :href="route('sales.index')" :active="request()->routeIs('sales.*')"
            icon="payments" label="Penjualan">
            <x-slot name="badge">
                @if($pendingPaymentCount > 0)
                    <span x-show="sidebarOpen || isMobile"
                          class="bg-orange-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $pendingPaymentCount }}</span>
                    <span x-show="!sidebarOpen && !isMobile"
                          class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-orange-400"></span>
                @endif
            </x-slot>
        </x-sidebar-link>

        {{-- Pengeluaran --}}
        <x-sidebar-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')"
            icon="receipt_long" label="Pengeluaran" />

        {{-- Laporan --}}
        <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')"
            icon="assessment" label="Laporan" />

        {{-- Notifikasi --}}
        <x-sidebar-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')"
            icon="notifications" label="Notifikasi">
            <x-slot name="badge">
                @if($unreadNotifCount > 0)
                    <span x-show="sidebarOpen || isMobile"
                          class="bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $unreadNotifCount }}</span>
                    <span x-show="!sidebarOpen && !isMobile"
                          class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-primary"></span>
                @endif
            </x-slot>
        </x-sidebar-link>

        {{-- Profile --}}
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')"
            icon="account_circle" label="Profile" />

    </nav>

    {{-- Bottom: Logout + User Profile Card --}}
    <div class="border-t border-gray-100 px-3 py-3 space-y-1">
        {{-- Logout --}}
        <button type="button"
            @click="$dispatch('open-modal', 'confirm-logout')"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200
                   text-on-secondary-container/70 hover:bg-surface-container-high"
            :class="sidebarOpen || isMobile ? '' : 'justify-center'">
            <span class="material-symbols-outlined text-2xl flex-shrink-0">logout</span>
            <span x-show="sidebarOpen || isMobile" class="text-sm font-medium truncate">Logout</span>
        </button>

        {{-- User Profile Card --}}
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-200 hover:bg-surface-container-low"
           :class="sidebarOpen || isMobile ? '' : 'justify-center'">
            <div class="w-8 h-8 rounded-full bg-[#0F6E8C] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div x-show="sidebarOpen || isMobile" class="truncate">
                <p class="text-sm font-semibold text-on-surface truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-on-surface-variant truncate">Administrator</p>
            </div>
        </a>
    </div>
</aside>

<x-modal name="confirm-logout" maxWidth="sm">
    <form method="POST" action="{{ route('logout') }}" class="p-6">
        @csrf
        <div class="text-center">
            <span class="material-symbols-outlined text-5xl text-[#0F6E8C] mb-4 inline-block">logout</span>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Logout</h2>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin logout?</p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'confirm-logout')"
                class="px-5 py-2.5 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                Batal
            </button>
            <button type="submit"
                class="bg-[#0F6E8C] hover:bg-[#0A4F66] text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
                Ya, Logout
            </button>
        </div>
    </form>
</x-modal>

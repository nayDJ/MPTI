<nav x-data="{ open: false }"
    class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">

    <div class="w-full px-8">

        <div class="flex items-center justify-between h-24">

            {{-- Left Side --}}
            <div class="flex items-center gap-12 h-full">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}"
                    class="text-3xl font-bold tracking-wide text-[#0F6E8C]">
                    NNQUA
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center h-full">

                    <a href="{{ route('dashboard') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('dashboard')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('products.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('products.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Produk
                        @if($criticalStockCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $criticalStockCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('customers.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('customers.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Pelanggan
                    </a>

                    <a href="{{ route('sales.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('sales.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Penjualan
                        @if($pendingPaymentCount > 0)
                            <span class="ml-2 bg-orange-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $pendingPaymentCount }}</span>
                        @endif
                    </a>


                    <a href="{{ route('expenses.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('expenses.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Pengeluaran
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('reports.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Laporan
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-lg
                        {{ request()->routeIs('profile.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Profile
                    </a>

                </div>
            </div>

            {{-- Right Side --}}
            <div class="hidden md:flex items-center gap-4">

                <div class="text-right">
                    <div class="flex items-baseline gap-1.5">
                        <span id="navTime" class="text-2xl font-bold tabular-nums text-slate-800" style="font-family: 'Orbitron', sans-serif;">00:00</span>
                        <span id="navAmPm" class="text-xs font-semibold text-slate-500 uppercase">AM</span>
                    </div>
                    <div id="navDay" class="text-sm text-slate-500 tracking-wide">Monday, January 1st</div>
                </div>

                <div class="text-right">

                    <p class="text-lg font-semibold text-slate-800">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-sm text-slate-500">
                        Administrator
                    </p>

                </div>

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">

                        <button
                            class="w-12 h-12 rounded-full bg-[#0F6E8C] text-white font-bold shadow-sm hover:bg-[#0b5b74] transition">

                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                        </button>

                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <x-dropdown-link
                            href="#"
                            @click.prevent="$dispatch('open-modal', 'confirm-logout'); open = false">

                            Logout

                        </x-dropdown-link>

                    </x-slot>

                </x-dropdown>

            </div>

            {{-- Mobile Button --}}
            <div class="md:hidden">

                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-slate-600 hover:bg-slate-100">

                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            :class="{'hidden': open, 'inline-flex': !open}"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path
                            :class="{'hidden': !open, 'inline-flex': open}"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>

        </div>

    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="open"
        class="md:hidden bg-white border-t border-slate-200">

        <div class="py-2">

            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('products.index')"
                :active="request()->routeIs('products.*')">
                Products
                @if($criticalStockCount > 0)
                    <span class="ml-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $criticalStockCount }}</span>
                @endif
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('customers.index')"
                :active="request()->routeIs('customers.*')">
                Customers
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('sales.index')"
                :active="request()->routeIs('sales.*')">
                Sales
                @if($pendingPaymentCount > 0)
                    <span class="ml-1 bg-orange-400 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingPaymentCount }}</span>
                @endif
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('reports.index')"
                :active="request()->routeIs('reports.*')">
                Reports
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('expenses.index')"
                :active="request()->routeIs('expenses.*')">
                Pengeluaran
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('profile.edit')"
                :active="request()->routeIs('profile.*')">
                Profile
            </x-responsive-nav-link>

        </div>

    </div>

    <x-modal name="confirm-logout" focusable>
        <div class="p-6 text-center">
            <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4m7 14l5-5-5-5m5 5H9"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Konfirmasi Logout</h3>
            <p class="mt-3 text-sm text-gray-600">Apakah Anda yakin ingin logout dari sistem?</p>

            <div class="mt-6 flex justify-center gap-4">
                <button type="button"
                        @click="$dispatch('close-modal', 'confirm-logout')"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-[#0F6E8C] rounded-lg hover:bg-[#0b5b74]">
                        Ya, Logout
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

</nav>
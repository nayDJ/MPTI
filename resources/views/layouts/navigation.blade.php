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
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-[17px]
                        {{ request()->routeIs('dashboard')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('products.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-[17px]
                        {{ request()->routeIs('products.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Products
                    </a>

                    <a href="{{ route('customers.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-[17px]
                        {{ request()->routeIs('customers.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Customers
                    </a>

                    <a href="{{ route('sales.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-[17px]
                        {{ request()->routeIs('sales.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Sales
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="h-full px-6 flex items-center border-b-[3px] transition-all duration-200 text-[17px]
                        {{ request()->routeIs('reports.*')
                            ? 'border-[#0F6E8C] text-[#0F6E8C] font-semibold'
                            : 'border-transparent text-slate-600 hover:text-[#0F6E8C]' }}">
                        Reports
                    </a>

                </div>

            </div>

            {{-- Right Side --}}
            <div class="hidden md:flex items-center gap-4">

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

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault();
                                this.closest('form').submit();">

                                Logout

                            </x-dropdown-link>

                        </form>

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
            </x-responsive-nav-link>

            <x-responsive-nav-link
                :href="route('reports.index')"
                :active="request()->routeIs('reports.*')">
                Reports
            </x-responsive-nav-link>

        </div>

    </div>

</nav>
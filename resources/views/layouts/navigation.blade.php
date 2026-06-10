<nav x-data="{ open: false }" class="bg-white shadow-sm border-b">

    <div class="w-full px-6 lg:px-10">
        <div class="flex justify-between items-center h-16">

            {{-- Left Side --}}
            <div class="flex items-center space-x-10">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                    NNQUA
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden sm:flex items-center space-x-8">

                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link
                        :href="route('products.index')"
                        :active="request()->routeIs('products.*')">
                        Products
                    </x-nav-link>

                    <x-nav-link
                        :href="route('customers.index')"
                        :active="request()->routeIs('customers.*')">
                        Customers
                    </x-nav-link>

                    <x-nav-link
                        :href="route('sales.index')"
                        :active="request()->routeIs('sales.*')">
                        Sales
                    </x-nav-link>

                </div>

            </div>

            {{-- Right Side --}}
            <div class="hidden sm:flex items-center">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600">

                            <div>
                                {{ Auth::user()->name }}
                            </div>

                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4
                                    4a1 1 0 01-1.414 0l-4-4a1 1 0
                                    010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>

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

            {{-- Mobile Hamburger --}}
            <div class="sm:hidden">

                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100">

                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

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
    <div x-show="open" class="sm:hidden border-t bg-white">

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

        </div>

    </div>

</nav>
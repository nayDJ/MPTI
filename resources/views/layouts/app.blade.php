<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'NNQUA')) — NNQUA</title>

        <style>[x-cloak] { display: none !important; }</style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|orbitron:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL@20..48,100..700,0..1&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="sidebarData()" class="min-h-screen bg-[#F3F6F8] flex">

            @include('layouts.navigation')

            <!-- Mobile Top Bar -->
            <div x-show="isMobile"
                 x-cloak
                 class="fixed top-0 left-0 right-0 h-12 bg-white border-b border-gray-200 z-40 flex items-center justify-between px-4">
                <button @click="sidebarOpen = true" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-on-surface-variant">menu</span>
                </button>
                <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-full bg-[#0F6E8C] text-white flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </a>
            </div>

            <!-- Main Content -->
            <main
                class="flex-1 min-h-screen"
                :class="{
                    'transition-all duration-300': initialized,
                    'pt-12': isMobile,
                    'ml-64': sidebarOpen && !isMobile,
                    'ml-16': !sidebarOpen && !isMobile,
                }"
            >
                <!-- Desktop Top Bar -->
                <div x-show="!isMobile" x-cloak
                     class="sticky top-0 z-10 h-14 bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 flex items-center justify-between px-8 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex items-baseline gap-1.5">
                            <span id="authTime" class="text-xl font-bold tabular-nums text-on-surface" style="font-family: 'Orbitron', sans-serif;">00:00</span>
                            <span id="authAmPm" class="text-xs font-semibold text-on-surface-variant uppercase">AM</span>
                        </div>
                        <div class="h-6 w-px bg-outline-variant/50"></div>
                        <span id="authDay" class="text-sm text-on-surface-variant tracking-wide">Saturday, July 18th</span>
                        <div class="h-6 w-px bg-outline-variant/50"></div>
                        <span class="text-sm font-semibold text-on-surface">@yield('topbar-title', 'Dashboard')</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative" x-data="notifBell()">
                            <span @click="toggle()" class="material-symbols-outlined text-on-surface-variant hover:text-primary cursor-pointer transition-colors">notifications</span>
                            <span x-show="unreadCount > 0" x-cloak
                                  class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-error ring-2 ring-surface"></span>

                            <div x-show="open" @click.outside="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="absolute right-0 mt-2 w-80 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-lg z-50 overflow-hidden">
                                <div class="p-3 border-b border-outline-variant/20 flex items-center justify-between">
                                    <span class="text-sm font-bold text-on-surface">Notifikasi</span>
                                    <span class="text-xs text-on-surface-variant" x-text="unreadCount + ' belum dibaca'"></span>
                                </div>
                                <div class="max-h-72 overflow-y-auto divide-y divide-outline-variant/10">
                                    <template x-for="n in items" :key="n.id">
                                        <div class="px-4 py-3 flex items-start gap-3 hover:bg-primary/5 transition-colors">
                                            <template x-if="n.type === 'success'">
                                                <span class="material-symbols-outlined text-green-600 text-lg mt-0.5">check_circle</span>
                                            </template>
                                            <template x-if="n.type === 'error'">
                                                <span class="material-symbols-outlined text-red-500 text-lg mt-0.5">error</span>
                                            </template>
                                            <template x-if="n.type === 'info'">
                                                <span class="material-symbols-outlined text-primary text-lg mt-0.5">info</span>
                                            </template>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-on-surface truncate" x-text="n.title"></p>
                                                <p class="text-xs text-on-surface-variant truncate" x-text="n.message"></p>
                                                <p class="text-[10px] text-outline mt-0.5" x-text="n.created_at"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <a href="{{ route('notifications.index') }}"
                                   class="block text-center text-sm font-medium text-primary py-2.5 border-t border-outline-variant/20 hover:bg-primary/5 transition-colors">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="h-6 w-px bg-outline-variant/50"></div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 group">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] uppercase tracking-widest text-outline">Admin</p>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-[#0F6E8C] text-white flex items-center justify-center text-sm font-bold ring-2 ring-primary-fixed transition-transform group-hover:scale-105">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </a>
                    </div>
                </div>

                @isset($header)
                    <header class="bg-white border-b border-gray-200">
                        <div class="max-w-full mx-auto py-4 px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{ $slot }}
            </main>

            <!-- Mobile overlay -->
            <div x-show="isMobile && sidebarOpen"
                 x-cloak
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-black/30 z-30 transition-opacity duration-300">
            </div>
        </div>

        {{-- Toast Notifications --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed top-20 right-6 z-[100] bg-green-100 text-green-700 px-5 py-3 rounded-xl shadow-lg border border-green-200 flex items-center gap-3 max-w-md">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed top-20 right-6 z-[100] bg-red-100 text-red-600 px-5 py-3 rounded-xl shadow-lg border border-red-200 flex items-center gap-3 max-w-md">
                <span class="material-symbols-outlined text-red-500">error</span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <script>
            function sidebarData() {
                const saved = localStorage.getItem('sidebarOpen');
                return {
                    initialized: false,
                    sidebarOpen: saved !== null ? saved === 'true' : false,
                    isMobile: window.innerWidth < 768,
                    toggleSidebar() {
                        this.sidebarOpen = !this.sidebarOpen;
                        localStorage.setItem('sidebarOpen', this.sidebarOpen);
                    },
                    init() {
                        this.$nextTick(() => { this.initialized = true; });
                        window.addEventListener('resize', () => {
                            this.isMobile = window.innerWidth < 768;
                            if (this.isMobile) this.sidebarOpen = false;
                        });
                    }
                }
            }
        </script>
    </body>
</html>

@props(['hideLogo' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NNQUA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|orbitron:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL@20..48,100..700,0..1&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface text-on-surface">
        <div class="min-h-screen flex flex-col relative overflow-hidden">

            <div class="absolute inset-0 z-0">
                <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('pics/landingpage.png') }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-surface/20 via-transparent to-transparent"></div>
            </div>

            <div class="fixed top-6 right-6 z-50 text-right">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-white tabular-nums" id="authTime" style="font-family: 'Orbitron', sans-serif;">00:00</span>
                    <span class="text-sm font-semibold text-white/70 uppercase" id="authAmPm">AM</span>
                </div>
                <div class="text-base text-white/70 tracking-wide" id="authDay">Monday, January 1st</div>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center relative z-10 w-full max-w-2xl px-4 mx-auto">
                @if(!$hideLogo)
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto bg-[#0F6E8C] rounded-2xl flex items-center justify-center animate-float">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                        </div>
                        <h1 class="mt-3 text-xl font-bold text-on-surface">NNQUA</h1>
                        <p class="text-sm text-on-surface-variant">Sistem Manajemen Air Minum</p>
                    </div>
                @endif

                <div class="w-full bg-white/70 backdrop-blur-xl border border-white/30 shadow-xl rounded-xl p-8 md:p-10">
                    <a href="/" class="inline-flex items-center gap-1.5 text-xl text-on-surface-variant hover:text-primary transition-colors mb-4">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        Home
                    </a>
                    {{ $slot }}
                </div>
            </div>

            <footer class="relative z-10 w-full py-6 text-center border-t border-outline-variant/20 bg-surface/20 backdrop-blur-md px-margin-mobile md:px-margin-desktop">
                <p class="text-lg text-on-surface-variant">&copy; {{ date('Y') }} NNQUA. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>

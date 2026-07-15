@props(['hideLogo' => false, 'backgroundType' => 'image'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NNQUA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|orbitron:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">

            @if($backgroundType === 'video')
                <video autoplay loop muted playsinline
                    class="absolute inset-0 w-full h-full object-cover">
                    <source src="{{ asset('videos/airr.mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
            @else
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('pics/background1.jpg') }}')">
                    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
                </div>
            @endif

            <div class="relative z-10">
                @if(!$hideLogo)
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto bg-[#0F6E8C] rounded-2xl flex items-center justify-center animate-float">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                        </div>
                        <h1 class="mt-3 text-xl font-bold text-white">NNQUA</h1>
                        <p class="text-sm text-gray-200">Sistem Manajemen Air Minum</p>
                    </div>
                @endif

                <div class="w-full px-16 py-10 bg-white/10 backdrop-blur-2xl rounded-2xl shadow-lg border border-white/30">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

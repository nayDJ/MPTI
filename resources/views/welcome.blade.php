<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NNQUA</title>
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|orbitron:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL@20..48,100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface text-on-surface">

<nav class="fixed top-0 left-0 w-full z-50 bg-surface/15 backdrop-blur-md shadow-sm transition-all duration-200 px-margin-mobile md:px-margin-desktop"
     x-data="scrollSpy()">
    <div class="h-20 flex items-center justify-between relative">
        <div class="pl-2 md:pl-4">
            <a href="/" class="inline-flex items-center gap-3 text-3xl font-bold text-on-surface" aria-label="logo">
                <div class="w-12 h-12 bg-[#0f6e8c] rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                </div>
                NNQUA
            </a>
        </div>

        <div class="absolute left-1/2 -translate-x-1/2 hidden md:flex gap-12 items-center">
            <a href="#home"
               @click.prevent="document.getElementById('home').scrollIntoView({behavior:'smooth'}); active='home'"
               :class="active === 'home' ? 'text-[#0f6e8c] font-semibold' : 'text-on-surface-variant hover:text-on-surface'"
               class="text-lg transition-colors py-2">Home</a>
            <a href="#about"
               @click.prevent="document.getElementById('about').scrollIntoView({behavior:'smooth'}); active='about'"
               :class="active === 'about' ? 'text-[#0f6e8c] font-semibold' : 'text-on-surface-variant hover:text-on-surface'"
               class="text-lg transition-colors py-2">About</a>
            <a href="#features"
               @click.prevent="document.getElementById('features').scrollIntoView({behavior:'smooth'}); active='features'"
               :class="active === 'features' ? 'text-[#0f6e8c] font-semibold' : 'text-on-surface-variant hover:text-on-surface'"
               class="text-lg transition-colors py-2">Features</a>
            <a href="#start"
               @click.prevent="document.getElementById('start').scrollIntoView({behavior:'smooth'}); active='start'"
               :class="active === 'start' ? 'text-[#0f6e8c] font-semibold' : 'text-on-surface-variant hover:text-on-surface'"
               class="text-lg transition-colors py-2">Start</a>
        </div>

        <div class="pr-2 md:pr-4">
            <div class="flex items-center gap-6">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-[#0f6e8c] text-white rounded-full px-10 py-3 text-lg font-semibold hover:opacity-90 transition-all">
                    Dashboard
                </a>
            @else
                <div class="hidden md:flex items-center gap-6">
                    <div class="text-right">
                        <div class="flex items-baseline gap-1.5">
                            <span id="navTime" class="text-2xl font-bold tabular-nums" style="font-family: 'Orbitron', sans-serif;">00:00</span>
                            <span id="navAmPm" class="text-sm font-semibold text-on-surface-variant uppercase">AM</span>
                        </div>
                        <div id="navDay" class="text-base text-on-surface-variant tracking-wide">Monday, January 1st</div>
                    </div>
                    <div class="h-8 w-px bg-outline-variant/50"></div>
                    <a href="{{ route('login') }}" class="bg-[#0f6e8c] text-white rounded-full px-10 py-3 text-lg font-semibold hover:opacity-90 transition-all">
                        Masuk
                    </a>
                </div>
                <a href="{{ route('login') }}" class="md:hidden bg-[#0f6e8c] text-white rounded-full px-5 py-2.5 text-base font-semibold">
                    Masuk
                </a>
            @endauth
        </div>
        </div>
    </div>
</nav>

<div data-reveal>
    <section id="home" class="relative min-h-screen flex items-center justify-center pt-16">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('pics/landingpage.png') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-surface/20 via-transparent to-surface"></div>
        </div>
        <div class="relative z-10 text-center max-w-4xl px-margin-mobile md:px-margin-desktop">
            <span class="text-2xl font-semibold text-[#0f6e8c] tracking-widest uppercase mb-4 block">Sistem Manajemen Depo Air Minum</span>
            <h1 class="text-4xl font-bold text-on-surface sm:text-5xl md:text-6xl mb-8 tracking-tight">NNQUA — Air Bersih untuk Semua</h1>
            <p class="text-2xl text-on-surface-variant mb-12 max-w-2xl mx-auto leading-relaxed">Kelola distribusi air, pantau transaksi, dan layani pelanggan dengan satu platform terpadu.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-[#0f6e8c] text-white px-10 py-4 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-[#0f6e8c] text-white px-10 py-4 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Mulai Sekarang
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-surface-container-low text-on-surface-variant px-10 py-4 rounded-full font-semibold hover:bg-surface-container-high transition-all">
                            Daftar
                        </a>
                    @endif
                @endauth
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce opacity-40">
            <span class="material-symbols-outlined text-4xl">expand_more</span>
        </div>
    </section>
</div>

<div data-reveal>
    <section id="about" class="py-section-gap px-margin-mobile md:px-margin-desktop min-h-screen flex flex-col justify-center">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div>
                <h2 class="text-4xl md:text-4xl font-bold text-on-surface mb-6">Tentang NNQUA</h2>
                <p class="text-xl text-justify text-on-surface-variant leading-relaxed mb-8">
                    NNQUA adalah sistem manajemen distribusi air minum yang dirancang untuk membantu perusahaan air minum dalam mengelola operasional sehari-hari — mulai dari pencatatan produk, data pelanggan, transaksi penjualan, hingga pelaporan keuangan secara terintegrasi.
                </p>
                <div class="flex flex-col gap-6">
                    <div class="flex items-start gap-4">
                        <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">visibility</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-on-surface">Visi</h4>
                            <p class="text-xl text-on-surface-variant leading-relaxed mt-1 text-justify">Menjadi platform terdepan dalam manajemen distribusi air minum yang memudahkan setiap perusahaan untuk melayani masyarakat dengan air bersih.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">target</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-on-surface text-justify">Misi</h4>
                            <ul class="text-xl text-on-surface-variant space-y-1.5 mt-1">
                                <li class="flex gap-2"><span class="text-[#0f6e8c]">•</span> Menyediakan sistem pencatatan yang akurat dan real-time.</li>
                                <li class="flex gap-2"><span class="text-[#0f6e8c]">•</span> Memudahkan monitoring stok, piutang, dan pendapatan.</li>
                                <li class="flex gap-2"><span class="text-[#0f6e8c]">•</span> Mendukung pembayaran fleksibel (tunai, tempo, cicil).</li>
                                <li class="flex gap-2"><span class="text-[#0f6e8c]">•</span> Menghadirkan laporan yang informatif untuk pengambilan keputusan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative group">
                <div class="absolute -inset-4 bg-[#0f6e8c]/5 rounded-[40px] blur-2xl group-hover:bg-[#0f6e8c]/10 transition-colors"></div>
                <div class="relative rounded-[32px] overflow-hidden shadow-2xl">
                    <img class="w-full aspect-[4/5] object-cover" src="{{ asset('pics/depoair.png') }}" alt="NNQUA Water">
                </div>
            </div>
        </div>
    </section>
</div>

<div data-reveal>
    <section id="features" class="py-section-gap px-margin-mobile md:px-margin-desktop min-h-screen flex flex-col justify-center">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-on-surface mb-4">Fitur Unggulan</h2>
                <p class="text-xl text-on-surface-variant max-w-xl mx-auto">Semua yang Anda butuhkan untuk mengelola bisnis air minum, dalam satu platform terpadu.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">monitoring</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Dashboard</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Pantau total penjualan, pendapatan, stok kritis, dan piutang pelanggan dalam satu layar terpadu. Dilengkapi grafik harian yang menampilkan tren pendapatan dan daftar aktivitas terbaru.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">inventory_2</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Produk</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Kelola produk air minum — galon, botol, dan aksesoris — dengan pencatatan stok dan harga yang akurat. Setiap perubahan stok tercatat otomatis saat transaksi terjadi.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">group</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Pelanggan</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Database pelanggan terpusat dengan informasi kontak dan riwayat transaksi lengkap. Cari pelanggan dengan cepat berdasarkan nama atau nomor telepon.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">receipt_long</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Transaksi</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Buat transaksi penjualan dengan antarmuka yang cepat dan intuitif. Dukung tiga status pembayaran fleksibel — lunas, tempo, atau cicil — sesuai kebutuhan pelanggan.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">analytics</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Laporan</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Lihat perkembangan bisnis melalui grafik dan laporan yang informatif. Analisis data penjualan untuk pengambilan keputusan yang lebih baik.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white hover:shadow-xl transition-shadow duration-300">
                    <span class="material-symbols-outlined text-[#0f6e8c] text-4xl mb-6">file_download</span>
                    <h3 class="text-lg font-semibold text-on-surface mb-3">Ekspor</h3>
                    <p class="text-lg text-justify text-on-surface-variant leading-relaxed">Export laporan penjualan ke format PDF dengan tampilan profesional siap cetak. Lengkap dengan kop surat NNQUA dan ringkasan total pendapatan.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<div data-reveal>
    <section id="start" class="py-section-gap px-margin-mobile md:px-margin-desktop min-h-screen flex flex-col justify-center">
        <div class="flex justify-center px-4">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch">
                <div class="lg:w-[540px] w-full bg-[#0f6e8c] rounded-[48px] p-8 md:p-16 relative overflow-hidden flex flex-col justify-center">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
                    </div>
                    <div class="relative z-10 text-center">
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap mengoptimalkan bisnis air Anda?</h2>
                        <p class="text-white/80 max-w-2xl mx-auto mb-10">Bergabunglah dengan NNQUA dan kelola distribusi air minum dengan sistem yang terintegrasi, akurat, dan mudah digunakan.</p>
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-block bg-white text-[#0f6e8c] px-10 py-4 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-block bg-white text-[#0f6e8c] px-10 py-4 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                Daftar Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="lg:w-[540px] w-full bg-white rounded-[48px] p-8 md:p-16 shadow-sm flex flex-col justify-center">
                    <h3 class="text-2xl md:text-3xl font-bold text-on-surface mb-8">Hubungi Kami</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                                <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">group</span>
                            </div>
                            <div>
                                <p class="text-sm text-on-surface-variant">Tim</p>
                                <p class="font-semibold text-on-surface text-lg">The Gunners</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                                <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">call</span>
                            </div>
                            <div>
                                <p class="text-sm text-on-surface-variant">Telepon</p>
                                <p class="font-semibold text-on-surface text-lg">085828237777</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                                <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">mail</span>
                            </div>
                            <div>
                                <p class="text-sm text-on-surface-variant">Email</p>
                                <p class="font-semibold text-on-surface text-lg">gunnersdeveloper@gmail.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-[#0f6e8c]/10 p-3 rounded-xl text-[#0f6e8c] flex-shrink-0">
                                <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">location_on</span>
                            </div>
                            <div>
                                <p class="text-sm text-on-surface-variant">Alamat</p>
                                <p class="font-semibold text-on-surface text-lg">Yogyakarta, Umbulharjo</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 pt-8 border-t border-outline-variant/20">
                        <p class="text-sm font-semibold text-on-surface mb-4">Ikuti Kami</p>
                        <div class="flex gap-4">
                            <a href="https://www.instagram.com/buildwithgunners" target="_blank" class="w-11 h-11 bg-[#0f6e8c]/10 rounded-xl flex items-center justify-center text-[#0f6e8c] hover:bg-[#0f6e8c] hover:text-white transition-all">
                                <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="https://wa.me/6285828237071?text=Halo%20admin%20Gunners" target="_blank" class="w-11 h-11 bg-[#0f6e8c]/10 rounded-xl flex items-center justify-center text-[#0f6e8c] hover:bg-[#0f6e8c] hover:text-white transition-all">
                                <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.52 0-10 4.48-10 10 0 1.97.57 3.8 1.55 5.36L2.5 22l4.73-1.45A9.97 9.97 0 0012.04 22c5.52 0 10-4.48 10-10s-4.48-10-10-10zm0 18.4c-1.67 0-3.31-.5-4.71-1.44l-.34-.21-2.81.87.86-2.77-.21-.34a8.4 8.4 0 01-1.3-4.51c0-4.64 3.78-8.42 8.42-8.42s8.42 3.78 8.42 8.42-3.78 8.42-8.42 8.42zm4.63-6.24c-.25-.13-1.5-.74-1.73-.82-.23-.09-.4-.13-.57.13-.17.25-.66.82-.81.99-.15.17-.3.19-.55.07-.25-.13-1.06-.39-2.02-1.24-.75-.66-1.25-1.46-1.4-1.71-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.13-.14.17-.25.25-.41.09-.17.04-.31-.02-.43-.06-.13-.56-1.34-.77-1.83-.2-.48-.4-.42-.56-.42-.14 0-.3-.01-.47-.01s-.43.06-.66.31c-.22.25-.86.84-.86 2.05 0 1.21.88 2.38 1.01 2.54.12.17 1.74 2.66 4.21 3.34.59.16 1.05.25 1.41.31.59.1 1.13.08 1.55.03.48-.05 1.48-.6 1.69-1.18.2-.58.2-1.08.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>
                            </a>
                            <a href="https://github.com/nayDJ" target="_blank" class="w-11 h-11 bg-[#0f6e8c]/10 rounded-xl flex items-center justify-center text-[#0f6e8c] hover:bg-[#0f6e8c] hover:text-white transition-all">
                                <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<footer class="py-8 px-margin-mobile md:px-margin-desktop border-t border-outline-variant/20 bg-surface-container-low">
    <div class="max-w-7xl mx-auto text-center">
        <p class="text-sm text-on-surface-variant">&copy; {{ date('Y') }} NNQUA. All rights reserved.</p>
    </div>
</footer>

<style>
    html { scroll-behavior: smooth; }
</style>

</body>
</html>

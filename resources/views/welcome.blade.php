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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('pics/background1.jpg') }}')">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-50 w-full" x-data="scrollSpy()">
        <div class="w-full px-4 md:px-8">
            <div class="flex items-center justify-between py-4 md:py-5">
                <a href="/" class="inline-flex items-center gap-2.5 text-2xl font-bold text-white md:text-3xl" aria-label="logo">
                    <div class="w-8 h-8 bg-[#0F6E8C] rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                    </div>
                    NNQUA
                </a>

                <nav class="hidden items-center gap-8 lg:flex">
                    <a href="#home" @click.prevent="document.getElementById('home').scrollIntoView({behavior:'smooth'}); active='home'"
                       :class="active === 'home' ? 'text-[#0F6E8C]' : 'text-gray-200'"
                       class="text-lg font-semibold hover:text-white transition duration-100 cursor-pointer">Home</a>
                    <a href="#about" @click.prevent="document.getElementById('about').scrollIntoView({behavior:'smooth'}); active='about'"
                       :class="active === 'about' ? 'text-[#0F6E8C]' : 'text-gray-200'"
                       class="text-lg font-semibold hover:text-white transition duration-100 cursor-pointer">About</a>
                    <a href="#features" @click.prevent="document.getElementById('features').scrollIntoView({behavior:'smooth'}); active='features'"
                       :class="active === 'features' ? 'text-[#0F6E8C]' : 'text-gray-200'"
                       class="text-lg font-semibold hover:text-white transition duration-100 cursor-pointer">Features</a>
                </nav>

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden rounded-lg bg-[#0F6E8C] px-6 py-2.5 text-center text-sm font-semibold text-white ring-[#0F6E8C] transition duration-100 outline-none hover:bg-[#0b5b74] focus-visible:ring-2 lg:inline-block">
                        Dashboard
                    </a>
                @else
                    <div class="hidden lg:flex items-center gap-5 text-white">
                        <div class="text-right">
                            <div class="flex items-baseline gap-1.5">
                                <span id="navTime" class="text-2xl font-bold tabular-nums" style="font-family: 'Orbitron', sans-serif;">00:00</span>
                                <span id="navAmPm" class="text-xs font-semibold text-gray-400 uppercase">AM</span>
                            </div>
                            <div id="navDay" class="text-sm text-white tracking-wide">Monday, January 1st</div>
                        </div>
                        <div class="h-8 w-px bg-white/20"></div>
                        <svg class="h-5 w-5 text-[#0F6E8C]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                @endauth

                <button type="button" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-2.5 py-2 text-sm font-semibold text-white transition duration-100 hover:bg-white/20 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="h-px bg-gray-400"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="relative z-10">

        {{-- #home --}}
        <section id="home" class="mx-auto max-w-screen-2xl px-4 md:px-8 min-h-screen flex flex-col justify-center">
            <div class="flex flex-col items-center">
                <div class="flex max-w-xl flex-col items-center text-center">
                    <p class="mb-4 font-semibold text-white/80 md:mb-6 md:text-lg xl:text-xl">Sistem Manajemen Air Minum</p>

                    <h1 class="mb-8 text-4xl font-bold text-white sm:text-5xl md:mb-12 md:text-6xl">NNQUA — Air Bersih untuk Semua</h1>

                    <p class="mb-8 text-base text-gray-200 md:mb-12 md:text-lg">Kelola distribusi air, pantau transaksi, dan layani pelanggan dengan satu platform terpadu.</p>

                    <div class="flex w-full flex-col gap-2.5 sm:flex-row sm:justify-center sm:gap-8">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-animated">
                                Dashboard
                                <div class="icon">
                                    <svg height="20" width="20" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z" fill="currentColor"/>
                                    </svg>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-animated">
                                Masuk
                                <div class="icon">
                                    <svg height="20" width="20" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z" fill="currentColor"/>
                                    </svg>
                                </div>
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-animated-outline">
                                    Daftar
                                    <div class="icon">
                                        <svg height="20" width="20" viewBox="0 0 24 24">
                                            <path d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        {{-- #about --}}
        <section id="about" class="mx-auto max-w-screen-2xl px-4 md:px-8 py-24 lg:py-32 min-h-screen flex flex-col justify-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-8 text-center">Tentang NNQUA</h2>
                <p class="text-lg text-gray-200 mb-12 text-center max-w-2xl mx-auto leading-relaxed">
                    NNQUA adalah sistem manajemen distribusi air minum yang dirancang untuk membantu perusahaan air minum dalam mengelola operasional sehari-hari — mulai dari pencatatan produk, data pelanggan, transaksi penjualan, hingga pelaporan keuangan secara terintegrasi.
                </p>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <span class="w-8 h-8 bg-[#0F6E8C] rounded-lg flex items-center justify-center text-white text-sm font-bold">V</span>
                            Visi
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            Menjadi platform terdepan dalam manajemen distribusi air minum yang memudahkan setiap perusahaan untuk melayani masyarakat dengan air bersih.
                        </p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <span class="w-8 h-8 bg-[#0F6E8C] rounded-lg flex items-center justify-center text-white text-sm font-bold">M</span>
                            Misi
                        </h3>
                        <ul class="text-gray-300 space-y-3 leading-relaxed">
                            <li class="flex gap-2"><span class="text-[#0F6E8C] mt-1 shrink-0">•</span> Menyediakan sistem pencatatan yang akurat dan real-time.</li>
                            <li class="flex gap-2"><span class="text-[#0F6E8C] mt-1 shrink-0">•</span> Memudahkan monitoring stok, piutang, dan pendapatan.</li>
                            <li class="flex gap-2"><span class="text-[#0F6E8C] mt-1 shrink-0">•</span> Mendukung pembayaran fleksibel (tunai, tempo, cicil).</li>
                            <li class="flex gap-2"><span class="text-[#0F6E8C] mt-1 shrink-0">•</span> Menghadirkan laporan yang informatif untuk pengambilan keputusan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- #features --}}
        <section id="features" class="mx-auto max-w-screen-2xl px-4 md:px-8 py-24 lg:py-32 min-h-screen flex flex-col justify-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-12">Fitur Unggulan</h2>

            <div class="feature-row">
                <div class="feature-card">
                    <div class="fc-title">Dashboard</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Dashboard Monitoring</div>
                        <p class="fc-desc">Pantau total penjualan, pendapatan, stok kritis, dan piutang pelanggan dalam satu layar terpadu. Dilengkapi grafik harian yang menampilkan tren pendapatan dan daftar aktivitas terbaru.</p>
                        <ul class="fc-list">
                            <li>Total transaksi harian &amp; bulanan</li>
                            <li>Pendapatan real-time</li>
                            <li>Produk stok menipis (&le;10 unit)</li>
                            <li>Daftar debitur outstanding tertinggi</li>
                        </ul>
                        <p class="fc-desc-sec">Semua metrik penting tampil ringkas di halaman utama tanpa perlu membuka halaman lain.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <div class="fc-title">Produk</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Manajemen Produk</div>
                        <p class="fc-desc">Kelola produk air minum — galon, botol, dan aksesoris — dengan pencatatan stok dan harga yang akurat. Setiap perubahan stok tercatat otomatis saat transaksi terjadi.</p>
                        <ul class="fc-list">
                            <li>Kategori produk (galon, botol, aksesoris)</li>
                            <li>Lacak stok masuk &amp; keluar</li>
                            <li>Notifikasi stok menipis (&le;10 unit)</li>
                            <li>Harga jual fleksibel per produk</li>
                        </ul>
                        <p class="fc-desc-sec">Pastikan ketersediaan produk selalu terjaga dengan sistem monitoring stok otomatis.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <div class="fc-title">Pelanggan</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Manajemen Pelanggan</div>
                        <p class="fc-desc">Database pelanggan terpusat dengan informasi kontak dan riwayat transaksi lengkap. Cari pelanggan dengan cepat berdasarkan nama atau nomor telepon.</p>
                        <ul class="fc-list">
                            <li>Data kontak lengkap (nama, telepon, alamat)</li>
                            <li>Riwayat transaksi per pelanggan</li>
                            <li>Total pembelian &amp; status piutang</li>
                            <li>Pencarian cepat (nama / telepon)</li>
                        </ul>
                        <p class="fc-desc-sec">Kelola hubungan pelanggan lebih baik dengan data transparan dan mudah diakses.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <div class="fc-title">Transaksi</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Transaksi Penjualan</div>
                        <p class="fc-desc">Buat transaksi penjualan dengan antarmuka yang cepat dan intuitif. Dukung tiga status pembayaran fleksibel sesuai kebutuhan pelanggan.</p>
                        <ul class="fc-list">
                            <li>Pembayaran Lunas (full payment)</li>
                            <li>Pembayaran Tempo (belum dibayar)</li>
                            <li>Pembayaran Cicil (angsuran)</li>
                            <li>Validasi stok otomatis saat transaksi</li>
                        </ul>
                        <p class="fc-desc-sec">Setiap transaksi tercatat otomatis dan langsung mempengaruhi stok produk.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <div class="fc-title">Laporan</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Laporan &amp; Analitik</div>
                        <p class="fc-desc">Lihat perkembangan bisnis melalui grafik dan laporan yang informatif. Analisis data penjualan untuk pengambilan keputusan yang lebih baik.</p>
                        <ul class="fc-list">
                            <li>Grafik pendapatan harian</li>
                            <li>5 produk terlaris</li>
                            <li>5 pelanggan terbanyak</li>
                            <li>Daftar debitur &amp; total outstanding</li>
                        </ul>
                        <p class="fc-desc-sec">Wawasan bisnis yang jelas untuk mengembangkan usaha air minum Anda.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <div class="fc-title">Ekspor</div>
                    <div class="fc-body">
                        <div class="fc-full-title">Ekspor PDF</div>
                        <p class="fc-desc">Export laporan penjualan ke format PDF dengan tampilan profesional siap cetak. Lengkap dengan kop surat NNQUA dan ringkasan total pendapatan.</p>
                        <ul class="fc-list">
                            <li>Filter tanggal (dari &mdash; sampai)</li>
                            <li>Filter status pembayaran</li>
                            <li>Tabel data penjualan lengkap</li>
                            <li>Ringkasan total pendapatan</li>
                        </ul>
                        <p class="fc-desc-sec">Laporan siap dibagikan ke manajemen atau klien dalam format standar.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Spacer for fixed social footer --}}
        <div class="h-24"></div>
    </div>

    {{-- Fixed Social Footer --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-black/40 backdrop-blur-lg">
        <div class="mx-auto max-w-screen-2xl pl-2 md:pl-4 pr-4 md:pr-8">
            <div class="flex items-center justify-center lg:justify-start gap-4 py-3">
                <span class="text-sm font-semibold tracking-widest text-gray-400 uppercase sm:text-base">Sosial</span>
                <span class="h-px w-12 bg-white/20"></span>
                <div class="flex gap-4">
                    <a href="#" target="_blank" class="text-gray-400 transition duration-100 hover:text-white">
                        <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                    <a href="#" target="_blank" class="text-gray-400 transition duration-100 hover:text-white">
                        <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                        </svg>
                    </a>
                    <a href="#" target="_blank" class="text-gray-400 transition duration-100 hover:text-white">
                        <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                    <a href="#" target="_blank" class="text-gray-400 transition duration-100 hover:text-white">
                        <svg class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                </div>
            </div>
            </div>
            <div class="h-px bg-gray-400"></div>
        </div>

    <style>
        html { scroll-behavior: smooth; }
        .feature-row {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        .feature-card {
            display: flex;
            flex-shrink: 0;
            width: 175px;
            height: 620px;
            border-radius: 14px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
            overflow: hidden;
            cursor: pointer;
            transition: width .5s cubic-bezier(0.4, 0, 0.2, 1),
                        background .5s ease,
                        border-color .5s ease;
        }
        .feature-card:hover {
            width: 700px;
            border-color: rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.08);
        }
        .fc-title {
            writing-mode: vertical-lr;
            text-orientation: upright;
            text-transform: uppercase;
            color: white;
            font-weight: 700;
            font-size: 26px;
            letter-spacing: 6px;
            width: 175px;
            min-width: 175px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fc-body {
            width: 0;
            overflow: hidden;
            transition: width .5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .feature-card:hover .fc-body {
            width: 525px;
        }
        .fc-full-title {
            color: white;
            font-weight: 700;
            font-size: 30px;
            padding: 0 28px 16px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity .4s ease .2s;
        }
        .fc-desc {
            color: rgba(255,255,255,0.8);
            font-size: 20px;
            line-height: 1.6;
            padding: 0 28px;
            white-space: normal;
            opacity: 0;
            transition: opacity .4s ease .2s;
        }
        .fc-list {
            list-style: none;
            padding: 4px 28px;
            margin: 0;
        }
        .fc-list li {
            color: rgba(255,255,255,0.8);
            font-size: 18px;
            line-height: 1.6;
            padding-left: 20px;
            position: relative;
            opacity: 0;
            transition: opacity .4s ease;
        }
        .fc-list li::before {
            content: "•";
            color: #0F6E8C;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        .fc-desc-sec {
            color: rgba(255,255,255,0.7);
            font-size: 18px;
            font-style: italic;
            padding: 8px 28px 0;
            opacity: 0;
            transition: opacity .4s ease;
        }
        .fc-list li:nth-child(1) { transition-delay: .1s; }
        .fc-list li:nth-child(2) { transition-delay: .15s; }
        .fc-list li:nth-child(3) { transition-delay: .2s; }
        .fc-list li:nth-child(4) { transition-delay: .25s; }
        .fc-desc-sec { transition-delay: .3s; }
        .feature-card:hover .fc-full-title,
        .feature-card:hover .fc-desc {
            opacity: 1;
        }
        .feature-card:hover .fc-list li,
        .feature-card:hover .fc-desc-sec {
            opacity: 1;
        }
    </style>
</div>

</body>
</html>
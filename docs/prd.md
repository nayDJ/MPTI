# PRD — NNQUA Water Management System

> Versi: 1.0  
> Tanggal: 17 Juli 2026  
> Status: Development (production-ready dengan beberapa item konsolidasi)

---

## 1. Ringkasan Eksekutif

NNQUA adalah sistem manajemen distribusi air minum berbasis web untuk satu pengguna (owner/operator tunggal). Sistem mencakup pencatatan produk, data pelanggan, transaksi penjualan multi-status pembayaran, pengeluaran operasional, serta laporan dan dashboard monitoring. Target: usaha depot air minum skala kecil-menengah yang membutuhkan sistem pencatatan digital terpadu.

**Tech stack:** Laravel 11, Alpine.js 3, Tailwind CSS 3, Chart.js, MySQL.

---

## 2. Visi Produk

Menjadi platform manajemen air minum yang cukup untuk UKM — sederhana, cepat, dan tidak memerlukan pelatihan. Satu layar untuk lihat omzet, utang, stok, dan pengeluaran. Tidak ada role, tidak ada approval flow, tidak ada onboarding yang berbelit.

---

## 3. User Persona

| Aspek | Detail |
|-------|--------|
| **Persona** | Owner (pemilik usaha depot air minum) |
| **Peran** | Admin + kasir + gudang sekaligus |
| **Kebutuhan** | Catat penjualan harian, lihat omzet & piutang, monitor stok, catat pengeluaran |
| **Sesi** | Biasanya login sekali di pagi hari, stand by seharian |
| **Device** | Laptop/PC (desktop-first), kadang tablet |

Sistem dirancang untuk **single user**. Tidak ada multi-role, multi-tenant, atau permission system.

---

## 4. Feature Registry

### 4.1 Auth

| Item | Detail |
|------|--------|
| **Login** | Form glass card dengan background video air. Floating label input (underline style). Tombol Masuk dengan animasi hover. Link lupa password & daftar. |
| **Register** | Layout sama dengan login (glass card + video). Form 2 kolom: Nama | Email, Kata Sandi | Konfirmasi Kata Sandi. |
| **Logout** | Modal konfirmasi dari navbar, bukan langsung logout. |
| **Constraint** | Tidak ada pendaftaran publik — register hanya untuk owner. Password minimum via Laravel default. |

### 4.2 Landing Page

| Item | Detail |
|------|--------|
| **Hero** | Background image `background1.jpg` + overlay `bg-black/30 backdrop-blur-sm`. Logo, tagline, judul, deskripsi, CTA (login/register). |
| **About** | Visi & Misi dalam 2 card dengan backdrop blur. |
| **Features** | 6 card expandable (Dashboard, Produk, Pelanggan, Transaksi, Laporan, Ekspor). Hover: card melebar + konten fade in. |
| **Nav** | Sticky header dengan scroll spy. Jam Orbitron real-time (update tiap 10 detik, format 12h + AM/PM + hari tanggal). |
| **Footer** | Fixed bottom: link sosial media (Instagram, Twitter, LinkedIn, GitHub). |
| **Clock** | Font Orbitron, update via `updateNavClock()` di app.js. Tampil di nav (guest) dan navigation.blade.php (auth). |

### 4.3 Dashboard

| Item | Detail |
|------|--------|
| **KPI Cards** | Total Customer, Total Produk, Total Penjualan, Total Pendapatan (lunas), Total Pengeluaran (bulanan) |
| **Chart** | 2 chart (Penjualan vs Pengeluaran) dengan Chart.js |
| **Widget** | Penjualan terbaru (5), Stok kritis (≤10), Top debitur (5), Pengeluaran terbaru (5) |
| **Modal Sale** | Tombol "+ Penjualan Baru" buka modal sale yang sama dengan halaman sales — tidak perlu pindah halaman |
| **Redirect** | Submit dari dashboard → redirect balik ke dashboard |

### 4.4 Sales

| Item | Detail |
|------|--------|
| **Trigger** | Via modal "Tambah Penjualan" dari dashboard atau halaman sales |
| **Flow** | Form → input customer (searchable + quick-add), tanggal, multi-item produk + qty, status bayar → Konfirmasi → Simpan |
| **Customer** | Searchable combobox dengan dropdown + quick-add inline (POST `/customers/quick-add`) |
| **Products** | Select dropdown per baris, data harga/stok dari data attribute, validasi stok otomatis |
| **Payment** | 3 status: Lunas, Cicil (input jumlah dibayar), Belum |
| **Konfirmasi** | Step 2: ringkasan customer, tanggal, status, daftar produk + total |
| **List** | Table dengan filter (belum ada), action: detail (eye), edit status bayar (modal), hapus (modal confirm) |
| **Detail** | Halaman show: informasi customer, item, status, timestamps |
| **Delete** | Modal konfirmasi (konsisten dengan entity lain) |
| **Edit** | Tidak diimplementasikan (no-op) — hanya edit status bayar via modal |

### 4.5 Products

| Item | Detail |
|------|--------|
| **Trigger** | Modal add/edit dari halaman products |
| **List** | Table: nama, kategori, stok, harga, action |
| **Filter** | Belum ada (search via browser) |
| **Standalone** | `ProductController@{create,edit}` redirect ke index (view tidak dipakai) |
| **Stock** | Otomatis berkurang saat transaksi. Notifikasi stok kritis (≤10) di nav & dashboard |

### 4.6 Customers

| Item | Detail |
|------|--------|
| **Trigger** | Modal add/edit dari halaman customers |
| **List** | Table: nama, telepon, alamat, action |
| **Quick-add** | Dari form sale modal → POST `/customers/quick-add` → return JSON |
| **Detail** | Halaman show: info customer + riwayat transaksi |
| **Standalone** | `CustomerController@{create,edit}` redirect ke index (view tidak dipakai) |

### 4.7 Expenses

| Item | Detail |
|------|--------|
| **Trigger** | Modal add/edit dari halaman expenses |
| **List** | Table: deskripsi, kategori, jumlah, tanggal, action |
| **Filter** | Period toggle (hari/minggu/bulan/tahun), date range, kategori dropdown, search |
| **Kategori** | Inline add via modal + master categories table |
| **Detail** | Halaman show: deskripsi, kategori, amount, tanggal, timestamps |

### 4.8 Reports

| Item | Detail |
|------|--------|
| **Tab** | Pemasukan (chart sales per produk, top 5 produk, top 5 customer, debitur) |
| **Aktif** | Hanya tab pemasukan yang terimplementasi |
| **Chart** | Chart.js dengan filter tanggal |
| **Export** | PDF via dompdf (filter tanggal + status bayar) |

### 4.9 Navigation

| Item | Detail |
|------|--------|
| **Menu** | Dashboard, Produk, Pelanggan, Penjualan, Pengeluaran, Laporan, Profile |
| **Clock** | Font Orbitron, format 12h, update real-time |
| **Badge** | Stok kritis (nav Produk), pending payment (nav Penjualan) |
| **User** | Avatar inisial + dropdown (Profile, Logout) |
| **Mobile** | Hamburger menu dengan daftar link yang sama |

---

## 5. Design System

### 5.1 Warna

| Token | Value | Penggunaan |
|-------|-------|------------|
| `--brand` | `#0F6E8C` | Tombol, link, aksen, active nav |
| `--brand-hover` | `#0b5b74` | Hover state |
| `--bg-primary` | `#F3F6F8` | Background utama dashboard |
| `--glass` | `bg-white/10 backdrop-blur-2xl` | Card login/register |
| `--text-body` | `#1e293b` (slate-800) | Teks utama |
| `--text-muted` | `#64748b` (slate-500) | Teks sekunder |
| Overlay | `bg-black/30 backdrop-blur-sm` | Di atas background video/image |

### 5.2 Tipografi

| Elemen | Font | Ukuran |
|--------|------|--------|
| Body | Figtree (default Tailwind) | 14-16px |
| Judul halaman | Figtree bold | 24-30px |
| Clock | Orbitron | 24px (nav), 16px (auth nav) |
| Label form | Figtree | 14px (floating) |
| Input teks | Figtree | 16-18px |

### 5.3 Komponen UI

| Komponen | Style |
|----------|-------|
| **Card glass** | `bg-white/10 backdrop-blur-2xl border-white/30 shadow-lg` |
| **Floating input** | No border, underline `rgba(255,255,255,0.3)`. Label float ke atas saat fokus/isi. Warna label: putih. Autofill: override transparan. |
| **Button primary** | `btn-animated` — bg `#0F6E8C`, hover slide putih dari kanan, teks berubah teal |
| **Button outline** | `btn-animated-outline` — bg putih, teks teal, hover slide teal dari kanan |
| **Modal** | Breeze `x-modal` + Alpine. Overlay gelap, transisi fade/scale. |
| **Table** | Stripped row, border-b, responsive wrapper |
| **Badge** | Rounded-full, bg merah/orange, teks putih kecil |

### 5.4 Alpine Components

| Component | Lokasi | Fungsi |
|-----------|--------|--------|
| `scrollSpy()` | `app.js:8` | Update nav active berdasarkan scroll position (landing page) |
| `saleForm(customers)` | `app.js:24` | Manajemen form sale: item rows, customer search+quick-add, payment, confirm, submit |
| `updateNavClock()` | `app.js:203` | Update jam & tanggal di nav (setiap 10 detik) |

---

## 6. Status Pengerjaan

### ✅ Selesai

- Auth: login video bg + glass card, register sync
- Dashboard: KPI, chart, widget, modal sale inline
- Sales: multi-item form, search customer + quick-add, 3 status bayar, confirm step, delete modal
- Products: CRUD modal, stock tracking, badge nav
- Customers: CRUD modal, quick-add, detail page
- Expenses: CRUD modal, filter lengkap, detail page, kategori inline
- Navigation: clock orbitron, user menu, stok & payment badge
- Landing page: hero, about, 6 feature cards expandable, scroll spy
- Bug fixes: Customer form, Product show, Flash messages, Log rotation
- Expense `@json`→`@js` fix (root cause Alpine component broken)
- Dashboard sale modal inline + redirect balik ke dashboard

### 🔄 Perlu Konsolidasi

| Item | Detail |
|------|--------|
| Products standalone views | `create.blade.php`, `edit.blade.php` masih ada tapi controller redirect |
| Customers standalone views | `create.blade.php`, `edit.blade.php` masih ada tapi controller redirect |
| Sales edit | Route no-op (`//`), tidak ada implementasi |
| Multi-user readiness | Sale & Expense tidak punya relasi ke User |

### 📋 Belum Dimulai

- Filter/search di Products & Customers index
- Export CSV
- Unit test
- Optimasi landing page entry animation

---

## 7. Roadmap

| Fase | Item |
|------|------|
| **Fase 1** (selesai) | Bug fix, konsolidasi modal, quick-add customer, expense detail, login glass + video, register sync, dashboard sale modal |
| **Fase 2** (sekarang) | Cleanup standalone views, landing page entry animation, filter products/customers, dokumentasi (PRD) |
| **Fase 3** (future) | Multi-user readiness, export CSV, test coverage, deployment |

---

## 8. Arsitektur Teknis

### 8.1 Stack

```
Frontend:  Blade + Alpine.js 3 + Tailwind CSS 3 + Chart.js 4
Backend:   Laravel 11 (PHP 8.2+)
Database:  MySQL
Build:     Vite
Email:     SMTP (Laravel default)
PDF:       dompdf
```

### 8.2 Struktur Data

```
customers ──┐
            ├──< sales ──< sale_items >── products
            │
User ───────┤
            ├── expenses (standalone, category string + categories table)
            │
            └── categories (master list untuk dropdown expense)
```

### 8.3 Route

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/` | welcome (landing) |
| GET | `/dashboard` | DashboardController@index |
| GET | `/reports` | ReportController@index |
| POST | `/categories` | CategoryController@store |
| POST | `/customers/quick-add` | CustomerController@quickStore |
| Resource | `sales` | SaleController (full) |
| Resource | `products` | ProductController (full) |
| Resource | `customers` | CustomerController (full) |
| Resource | `expenses` | ExpenseController (full) |
| Auth | — | Breeze default (login, register, password, verify) |

---

## 9. Catatan Teknis

| Temuan | Keterangan |
|--------|------------|
| `@json` vs `@js` | Jangan pakai `@json()` di dalam `x-data="..."` atau `@click="..."` — Laravel `json_encode` menghasilkan string dengan `"` yang memecah HTML attribute. Gunakan `@js()` (via `Js::from()`) yang aman di dalam double-quote. |
| Autofill override | Chrome/Safari kasih background putih ke field autofill. CSS `-webkit-autofill` dengan `box-shadow: 0 0 0 1000px transparent inset` diperlukan untuk glass card. |
| Clock font | Orbitron hanya di-load di layout yang butuh: `welcome.blade.php` dan `layouts/app.blade.php` serta `layouts/guest.blade.php`. |
| Video size | `airr.mp4` (7MB) — cukup ringan untuk background. Fallback ke `background1.jpg` jika video tidak didukung. |

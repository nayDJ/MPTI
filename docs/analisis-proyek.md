# Analisis Proyek nnqua-web

> Dibuat: 15 Juli 2026
> Total: ~5.771 baris kode (PHP + Blade)

---

## 1. Struktur Aplikasi

| Lapisan | Jumlah | Detail |
|---|---|---|
| Controllers | 18 (9 custom + 9 Auth) | Sales, Products, Customers, Expenses, Categories, Reports, Dashboard, Profile, Auth |
| Models | 7 | Sale, SaleItem, Product, Customer, Expense, Category, User |
| Views | 40 files (12 dir) | Blade + Tailwind + Alpine.js |
| Migrations | 11 | users → expenses → categories |
| Routes | 54 endpoint | resource penuh: customers, products, expenses, sales |
| PHP Deps | laravel/dompdf | Laravel 11 |
| JS Deps | chart.js ^4.5.1 | npm |

## 2. Arsitektur Data

```
customers ──┐
            ├──< sales ──< sale_items >── products
            │
User ───────┤
            │
            ├── expenses (standalone, category string + categories table reference)
            │
            └── categories (master list untuk dropdown expense)
```

### Relasi Model

| Model | Relasi |
|---|---|
| **Sale** | `belongsTo(Customer)`, `hasMany(SaleItem)` |
| **SaleItem** | `belongsTo(Sale)`, `belongsTo(Product)` |
| **Product** | `hasMany(SaleItem)` |
| **Customer** | `hasMany(Sale)` |
| **Expense** | standalone (tidak ada relasi eloquent) |
| **Category** | standalone |
| **User** | standalone (tidak ada relasi ke Sale/Expense) |

**Catatan:** Tidak ada `belongsTo(User)` di Sale maupun Expense — implikasi untuk multi-user di masa depan.

## 3. Feature Completeness Matrix

| Fitur | Create | Read | Update | Delete | Filter | Export |
|-------|--------|------|--------|--------|--------|--------|
| **Sales** | ✅ multi-item modal | ✅ index + show | ❌ no-op | ⚠️ `confirm()` | ❌ | ✅ PDF |
| **Products** | ⚠️ modal + dead page | ✅ index | ⚠️ modal + dead page | ✅ modal | ❌ | ❌ |
| **Customers** | ⚠️ modal + dead page | ✅ index + show | ⚠️ modal + dead page | ✅ modal | ❌ | ❌ |
| **Expenses** | ✅ modal inline | ✅ index | ✅ modal | ✅ modal | ✅ period/cat/date | ❌ |
| **Categories** | ✅ fetch POST | ❌ no page | ❌ | ❌ | ❌ | ❌ |
| **Reports** | N/A | ✅ charts + tables | N/A | N/A | ✅ tab toggle | ❌ |
| **Dashboard** | N/A | ✅ 5 KPI + 2 charts | N/A | N/A | ❌ | ❌ |

## 4. Temuan Kritis

### 🔴 Bug / Broken

| # | Temuan | Lokasi | Dampak |
|---|---|---|---|
| 1 | `customers/create.blade.php` — form tanpa `x-input-error` & `x-input-label` | `resources/views/customers/create.blade.php:16-48` | Validasi gagal: user tidak dapat error feedback |
| 2 | `ProductController@show` — method kosong (`//`) | `app/Http/Controllers/ProductController.php:69-72` | Rute `products/{product}` blank page |
| 3 | `customers/show`, `sales/show` — tidak ada flash messages | `resources/views/customers/show.blade.php` & `sales/show.blade.php` | Feedback sukses/error tidak tampil |
| 4 | Log file ~331KB tanpa rotasi | `storage/logs/laravel.log` | Akumulasi error |
| 5 | 101+ debug calls (`dd`/`dump`/`var_dump`) | 16 files (controllers + views) | Debug code terlanjur terdeploy |http://127.0.0.1:8001/dashboard

### 🟡 Inkonsistensi UI/UX

| # | Temuan | Lokasi |
|---|---|---|
| 6 | Dual-path: 4 standalone pages + modal di index untuk Products & Customers | `resources/views/products/{create,edit}.blade.php`<br>`resources/views/customers/{create,edit}.blade.php` |
| 7 | Sales delete pakai `confirm()` native — satu-satunya tanpa styled modal | `resources/views/sales/index.blade.php:276` |
| 8 | Sales create redirect ke index — dashboard link confusing | `app/Http/Controllers/SaleController.php:88`<br>`resources/views/dashboard.blade.php:18` |
| 9 | Sales edit route no-op (`//`) — resource route tidak implementasi | `app/Http/Controllers/SaleController.php:235` |
| 10 | Indentasi campuran (0/3/4/8 spasi) | `app/Http/Controllers/DashboardController.php:16-87`<br>`app/Http/Controllers/ProductController.php:78-79` |

### 🟢 Sudah Baik

| # | Aspek | Detail |
|---|---|---|
| 11 | XSS Safety — 0 penggunaan `{!! $var !!}` | Semua output Blade pakai `{{ }}` (auto-escaped) |
| 12 | Modal CRUD pattern konsisten (Expenses, Products, Customers) | 3 dari 4 entity pakai modal add/edit/delete |
| 13 | CSRF protection konsisten | Semua POST form & fetch punya token |
| 14 | Chart.js — instance tracking, $nextTick, destroy pattern | `resources/views/reports/index.blade.php` + `dashboard.blade.php` |
| 15 | Expense filtering — period toggle, date range, category, search | `app/Http/Controllers/ExpenseController.php` |

## 5. Kualitas Kode per File

### Controllers

| File | Lines | Catatan |
|-------|-------|---------|
| `SaleController.php` | 284 | `create()` redirect, `edit()` no-op, `$e` tidak dipakai |
| `ExpenseController.php` | 126 | Terbersih — filtering lengkap, CRUD rapi |
| `DashboardController.php` | 89 | Indentasi campuran (0/3/4/8 spasi) |
| `ProductController.php` | 104 | `show()` kosong, indentasi `edit()` campuran |
| `CustomerController.php` | 99 | show/create/edit standalone pages tidak terpakai |
| `ReportController.php` | 93 | OK — transform chart data rapi |
| `CategoryController.php` | 20 | Minimal — hanya store |
| `ProfileController.php` | 60 | Default Breeze |

### Views Terbesar

| File | Lines | Kompleksitas |
|-------|-------|--------------|
| `sales/index.blade.php` | 582 | Tertinggi — multi-item modal, confirm, payment |
| `dashboard.blade.php` | 450 | 5 KPI + 2 chart + expense table |
| `expenses/index.blade.php` | 436 | Modal CRUD + filter + inline kategori |
| `navigation.blade.php` | 266 | Desktop + mobile nav, logout modal |
| `customers/index.blade.php` | 377 | Modal CRUD + search |

## 6. Rekomendasi Prioritas

### Segera (critical path)

1. **Hapus semua debug code** — `dd()`/`dump()`/`var_dump()` di 16 files, ~101 calls
2. **Bersihkan atau rotate** `storage/logs/laravel.log` (331KB)
3. **Fix `ProductController@show`** — return 404 atau implement view
4. **Tambah flash messages** di `sales/show.blade.php` & `customers/show.blade.php`
5. **Fix `customers/create.blade.php`** — tambah `x-input-error` + styling, atau hapus

### Konsolidasi (medium)

6. **Pilih satu pola: modal-only**. Hapus 4 standalone views + controller methods yang return view:
   - `resources/views/products/{create,edit}.blade.php`
   - `resources/views/customers/{create,edit}.blade.php`
   - `ProductController@{create,edit}` return view → redirect ke index
   - `CustomerController@{create,edit}` return view → redirect ke index
7. **Sales delete → styled modal** konsisten dengan entity lain
8. **Sales edit route** — hapus dari resource definition atau implementasi
9. **Sales create route** — implementasi dedicated page atau hapus route, konsistenkan dashboard link
10. **Rapikan indentation** — `DashboardController.php`, `ProductController.php`

### Enhancement (low)

11. Filter/sort di Products & Customers index
12. Relasi `Sale→User` dan `Expense→User` untuk multi-user readiness
13. Export CSV untuk laporan
14. Log rotation otomatis (Laravel daily channel)
15. Unit test untuk ExpenseController, CategoryController

---

## 7. Lampiran

### Semua Route (54)

```
GET|HEAD /                    → welcome
GET|HEAD dashboard            → DashboardController@index
GET|HEAD reports              → ReportController@index
POST   categories             → CategoryController@store

Resource: sales               → SaleController
Resource: products            → ProductController
Resource: customers           → CustomerController
Resource: expenses            → ExpenseController

Auth routes                   → Breeze default (login, register, password, verify)
```

### Semua Migration (11)

| File | Tabel |
|------|-------|
| `0001_01_01_000000` | users |
| `0001_01_01_000001` | cache |
| `0001_01_01_000002` | jobs |
| `2026_05_22_160355` | customers |
| `2026_05_22_160356` | products |
| `2026_06_10_124904` | sales |
| `2026_06_10_124914` | sale_items |
| `2026_07_07_192306` | add payment_status to sales |
| `2026_07_08_192203` | add category to products (rolled back) |
| `2026_07_11_205526` | expenses |
| `2026_07_11_210707` | categories |

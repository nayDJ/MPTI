# NNQUA Testing Report

| Date | Branch | PHPUnit | Dusk | Total |
|------|--------|---------|------|-------|
| 2026-07-23 | `testing` | 77 tests ✅ | 10 tests ✅ | **87/87 PASS** |

---

## 1. Whitebox Testing — PHPUnit Feature Tests

Framework: PHPUnit 12 via `vendor/bin/phpunit`
Database: SQLite in-memory (per-test isolated)
Base: `Tests\TestCase` with `RefreshDatabase`

### 1.1 CustomerTest (`tests/Feature/CustomerTest.php`) — 8 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /customers → 200 |
| `test_store_creates_customer_and_redirects` | Integration | POST /customers → DB record + redirect |
| `test_store_validates_required_fields` | Validation | Empty POST → session errors |
| `test_show_displays_customer` | Response | GET /customers/{id} → 200 |
| `test_update_modifies_customer` | Integration | PUT /customers/{id} → DB updated |
| `test_destroy_deletes_customer` | Integration | DELETE /customers/{id} → DB missing |
| `test_toggle_status_activates_inactive_customer` | Business Logic | POST toggle → is_active flips |
| `test_quick_store_returns_json` | API | POST quick-add → JSON response |

### 1.2 ProductTest (`tests/Feature/ProductTest.php`) — 6 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /products → 200 |
| `test_store_creates_product_and_redirects` | Integration | POST /products → DB record |
| `test_store_validates_required_fields` | Validation | Empty POST → errors on name, price |
| `test_update_modifies_product` | Integration | PUT /products/{id} → DB updated |
| `test_destroy_deletes_product` | Integration | DELETE /products/{id} → DB missing |
| `test_toggle_status_changes_active_state` | Business Logic | POST toggle → is_active flips |

### 1.3 SaleTest (`tests/Feature/SaleTest.php`) — 7 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /sales → 200 |
| `test_store_creates_sale_with_items` | Integration | POST /sales → sale + sale_items created |
| `test_store_validates_required_fields` | Validation | Empty POST → errors |
| `test_store_fails_when_stock_insufficient` | Business Logic | qty > stock → validation error |
| `test_show_displays_sale` | Response | GET /sales/{id} → 200 |
| `test_update_payment_status` | Integration | PUT payment_status lunas → paid_amount = total |
| `test_destroy_deletes_sale_and_items` | Integration | DELETE → cascade deletes items |

### 1.4 ExpenseTest (`tests/Feature/ExpenseTest.php`) — 6 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /expenses → 200 |
| `test_store_creates_expense_and_redirects` | Integration | POST /expenses → DB record |
| `test_store_validates_required_fields` | Validation | Empty → 4 errors |
| `test_show_displays_expense` | Response | GET /expenses/{id} → 200 |
| `test_update_modifies_expense` | Integration | PUT → DB updated |
| `test_destroy_deletes_expense` | Integration | DELETE → DB missing |

### 1.5 ReportTest (`tests/Feature/ReportTest.php`) — 3 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /reports → 200 |
| `test_index_accepts_period_filter` | Response | `?period=bulan` → 200 |
| `test_index_accepts_view_filter` | Response | `?view=income` → 200 |

### 1.6 DashboardTest (`tests/Feature/DashboardTest.php`) — 2 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_authenticated_user_can_view_dashboard` | Auth | Authed GET /dashboard → 200 |
| `test_guest_is_redirected_to_login` | Auth | Guest GET /dashboard → /login |

### 1.7 CategoryTest (`tests/Feature/CategoryTest.php`) — 2 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_store_creates_category_and_returns_json` | API | POST /categories → JSON + DB |
| `test_store_validates_unique_name` | Validation | Duplicate name → error |

### 1.8 NotificationTest (`tests/Feature/NotificationTest.php`) — 3 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_index_returns_view` | Response | GET /notifications → 200 |
| `test_unread_returns_json` | API | GET /notifications/unread → JSON unread_count |
| `test_mark_as_read_updates_all_notifications` | Integration | POST mark-read → is_read = true |

### 1.9 AuthRouteTest (`tests/Feature/AuthRouteTest.php`) — 7 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_guest_cannot_access_dashboard` | Auth Guard | Redirect to login |
| `test_guest_cannot_access_customers` | Auth Guard | index + show redirect |
| `test_guest_cannot_access_products` | Auth Guard | index redirect |
| `test_guest_cannot_access_sales` | Auth Guard | index + show redirect |
| `test_guest_cannot_access_expenses` | Auth Guard | index + show redirect |
| `test_guest_cannot_access_reports` | Auth Guard | index redirect |
| `test_guest_cannot_access_notifications` | Auth Guard | index redirect |

### 1.10 ModelTest (`tests/Feature/ModelTest.php`) — 8 tests

| Test | Type | What it verifies |
|------|------|-----------------|
| `test_sale_scope_collectable_revenue_*` (3x) | Model | Scope logic for lunas/cicil/belum |
| `test_notification_scope_unread` | Model | Scope returns only unread |
| `test_notification_scope_by_type` | Model | Scope filters by action_type prefix |
| `test_product_has_sale_items_relationship` | Model | HasMany relationship |
| `test_customer_has_sales_relationship` | Model | HasMany relationship |
| `test_sale_has_items_relationship` | Model | HasMany relationship |

### Auth Tests (pre-existing, 18 tests)

| File | Tests | Coverage |
|------|-------|----------|
| `Auth/AuthenticationTest.php` | 4 | Login screen, authenticate, invalid pw, logout |
| `Auth/EmailVerificationTest.php` | 3 | Verification screen, verify, invalid hash |
| `Auth/PasswordConfirmationTest.php` | 3 | Confirm screen, confirm, wrong password |
| `Auth/PasswordResetTest.php` | 4 | Reset link screen, request, reset screen, reset |
| `Auth/PasswordUpdateTest.php` | 2 | Update, wrong current password |
| `Auth/RegistrationTest.php` | 2 | Registration screen, register |

### Other (6 tests)

| File | Tests | Coverage |
|------|-------|----------|
| `ExampleTest.php` | 1 | Homepage returns 200 |
| `ProfileTest.php` | 5 | Profile display, update, delete, password check |

---

## 2. Blackbox Testing — Dusk Browser Tests

Framework: Dusk 8.6 + ChromeDriver 150
Database: MySQL `testing` (via `.env.dusk.local`)
Server: `php artisan serve --port=8080`

### 2.1 LoginTest (`tests/Browser/LoginTest.php`) — 2 tests

| Test | What it verifies |
|------|-----------------|
| `test_user_can_login_and_logout` | Navigate /login, fill credentials, assert /dashboard |
| `test_login_fails_with_wrong_password` | Wrong password → error message displayed |

### 2.2 CustomerManagementTest (`tests/Browser/CustomerManagementTest.php`) — 2 tests

| Test | What it verifies |
|------|-----------------|
| `test_customer_index_shows_page` | Page loads, "Pelanggan" text visible |
| `test_admin_can_create_new_customer` | Click "Tambah Pelanggan", fill form, assert record |

### 2.3 ProductManagementTest (`tests/Browser/ProductManagementTest.php`) — 2 tests

| Test | What it verifies |
|------|-----------------|
| `test_product_index_shows_page` | Page loads, "Produk" text visible |
| `test_admin_can_create_product` | Click "Tambah Produk", fill form, assert record |

### 2.4 SaleCreationTest (`tests/Browser/SaleCreationTest.php`) — 2 tests

| Test | What it verifies |
|------|-----------------|
| `test_sale_index_shows_page` | Page loads, "Penjualan" text visible |
| `test_sale_show_page_loads` | Seed sale, navigate show page, assert customer name |

### 2.5 DashboardTest (`tests/Browser/DashboardTest.php`) — 1 test

| Test | What it verifies |
|------|-----------------|
| `test_dashboard_shows_after_login` | Login as user, visit /dashboard, assert "Dashboard" |

### 2.6 ExampleTest — 1 test

| Test | What it verifies |
|------|-----------------|
| `test_welcome_page_loads` | Visit /, assert path is / |

---

## 3. Fixes Applied During Testing

### 3.1 Migration: `add_is_active_to_products_table`
- **Problem:** `after('low_stock_alert_enabled')` referenced a column from migration #4, but migration #3 runs first.
- **Fix:** Removed `after()` clause. Column position uses default (end of table).

### 3.2 Migration: `drop_unused_notification_morphs`
- **Problem:** SQLite cannot `DROP COLUMN` when the column is part of a composite index.
- **Fix:** Added `$table->dropIndex(['notifiable_type', 'notifiable_id'])` before `dropColumn()`.

### 3.3 ReportController: `MONTH()` SQLite incompatibility
- **Problem:** `MONTH(sales_date)` does not exist in SQLite.
- **Fix:** Replaced with driver-detect: `strftime('%m', col)` for SQLite, `MONTH(col)` for MySQL.

### 3.4 `.env.dusk.local` config
- **Problem:** `APP_URL=http://localhost` caused Dusk browser to hit Apache on port 80.
- **Fix:** Set `APP_URL=http://127.0.0.1:8080` to match `php artisan serve`.

---

## 4. Lingkungan Pengujian

| Komponen | Versi |
|----------|-------|
| OS | Fedora 44 |
| PHP | 8.4+ |
| Laravel | 11 |
| PHPUnit | 12.5 |
| Dusk | 8.6 |
| Chromium | 150.0.7871.128 |
| ChromeDriver | 150.0.7871.124 |
| Database (PHPUnit) | SQLite :memory: |
| Database (Dusk) | MySQL 8 (`testing`) |

---

## 5. Cara Menjalankan

```bash
# PHPUnit (whitebox)
vendor/bin/phpunit

# Dusk (blackbox) — perlu PHP server running
php artisan serve --port=8080 &
php artisan dusk
```

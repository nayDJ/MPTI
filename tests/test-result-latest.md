# Test Results — 2026-07-23

## Summary
- **Total Tests**: 105 (90 PHPUnit + 15 Dusk)
- **Status**: ✅ All passing

## PHPUnit — 90 tests, 196 assertions ✅

| File | Tests | Status |
|------|-------|--------|
| Auth/AuthenticationTest | 4 | ✅ |
| Auth/EmailVerificationTest | 3 | ✅ |
| Auth/PasswordConfirmationTest | 3 | ✅ |
| Auth/PasswordResetTest | 4 | ✅ |
| Auth/PasswordUpdateTest | 2 | ✅ |
| Auth/RegistrationTest | 2 | ✅ |
| AuthRouteTest | 7 | ✅ |
| CategoryTest | 2 | ✅ |
| CustomerTest | 11 | ✅ |
| DashboardTest | 2 | ✅ |
| ExampleTest | 1 | ✅ |
| ExpenseTest | 7 | ✅ |
| ModelTest | 10 | ✅ |
| NotificationTest | 3 | ✅ |
| ProductTest | 9 | ✅ |
| ProfileTest | 5 | ✅ |
| ReportTest | 3 | ✅ |
| SaleTest | 12 | ✅ |

## Dusk — 15 tests, 15 assertions ✅

| File | Tests | Status |
|------|-------|--------|
| CustomerManagementTest | 2 | ✅ |
| DashboardTest | 1 | ✅ |
| ExampleTest | 1 | ✅ |
| LoginTest | 2 | ✅ |
| ProductManagementTest | 2 | ✅ |
| SaleCreateTest | 2 | ✅ |
| SaleCreationTest | 2 | ✅ |
| ToggleStatusTest | 3 | ✅ |

## Notes
- DB: MySQL (testing) via `.env.dusk.local`
- Server: `php artisan serve --port=8080` (manual start required)
- Run: `composer run test:all`

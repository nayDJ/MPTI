<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class)->except('create', 'edit');
    Route::post('customers/quick-add', [CustomerController::class, 'quickStore'])->name('customers.quick-add');
    Route::post('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])
        ->name('customers.toggle-status');
    Route::resource('products', ProductController::class)->except('show');
    Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::resource('expenses', ExpenseController::class)->except('create', 'edit');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

Route::get('/sales/export/pdf', [SaleController::class, 'exportPdf'])
    ->middleware('auth')
    ->name('sales.export.pdf');

Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])
    ->middleware('auth')
    ->name('expenses.export.pdf');

Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])
    ->middleware('auth')
    ->name('products.export.pdf');

Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])
    ->middleware('auth')
    ->name('customers.export.pdf');

Route::resource('sales', SaleController::class)
    ->middleware('auth')
    ->except('create', 'edit');

Route::get('/reports',[ReportController::class, 'index'])->name('reports.index')
 ->middleware('auth');

Route::get('/reports/pdf',[ReportController::class, 'exportPdf'])->name('reports.pdf')
 ->middleware('auth');
    
    
require __DIR__.'/auth.php';

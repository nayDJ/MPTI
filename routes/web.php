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
    Route::resource('customers', CustomerController::class);
    Route::post('customers/quick-add', [CustomerController::class, 'quickStore'])->name('customers.quick-add');
    Route::resource('products', ProductController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
});

Route::get('/sales/export/pdf', [SaleController::class, 'exportPdf'])
    ->middleware('auth')
    ->name('sales.export.pdf');

Route::resource('sales', SaleController::class)
    ->middleware('auth');

Route::get('/reports',[ReportController::class, 'index'])->name('reports.index')
 ->middleware('auth');
    
    
require __DIR__.'/auth.php';

<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $view->with('criticalStockCount', Product::where('stock', '<=', 10)->count());
            $view->with('pendingPaymentCount', Sale::whereIn('payment_status', ['belum', 'cicil'])->count());
        });
    }
}

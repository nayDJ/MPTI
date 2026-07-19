<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Notification;
use App\Listeners\LogLogin;
use App\Listeners\LogLogout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
        Event::listen(Login::class, LogLogin::class);
        Event::listen(Logout::class, LogLogout::class);

        View::composer('layouts.navigation', function ($view) {
            $view->with('criticalStockCount', Product::where('stock', '<=', 10)->count());
            $view->with('pendingPaymentCount', Sale::whereIn('payment_status', ['belum', 'cicil'])->count());
            $view->with('unreadNotifCount', Notification::where('is_read', false)->count());
        });
    }
}

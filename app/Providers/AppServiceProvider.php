<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (auth()->check() && auth()->user()->role === 'farmer') {
                $pendingBillsCount = \App\Models\Bill::where('status', 'pending')
                    ->whereHas('connection', fn($q) => $q->where('consumer_id', auth()->id()))
                    ->count();
                $view->with('pendingBillsCount', $pendingBillsCount);
            }
        });
    }
}

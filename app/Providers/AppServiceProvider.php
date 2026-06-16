<?php

namespace App\Providers;

use App\Support\BillingDueReminder;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            if (! $user || ! $user->isAdmin()) {
                $view->with('billingDueAlerts', []);

                return;
            }

            $view->with('billingDueAlerts', BillingDueReminder::alerts());
        });
    }
}

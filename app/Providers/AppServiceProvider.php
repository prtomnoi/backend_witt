<?php

namespace App\Providers;

use App\Services\AuditLogService;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

//        $this->app->singleton(AuditLogService::class, function ($app) {
//            return new AuditLogService();
//        });

        $this->app->bind(AuditLogService::class, function ($app) {
            return new AuditLogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('th');
    }
}

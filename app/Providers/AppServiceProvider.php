<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Quote;
use App\Observers\QuoteObserver;
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
        Quote::observe(QuoteObserver::class);
    }
}

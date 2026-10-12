<?php

namespace App\Providers;

use Illuminate\Support\{Carbon, ServiceProvider};
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

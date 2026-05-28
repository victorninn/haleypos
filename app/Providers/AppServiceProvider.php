<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        Paginator::useTailwind();

        // Share branding to every view
        View::composer('*', function ($view) {
            $view->with('posBrand', [
                'name' => config('pos.brand_name'),
                'logo' => config('pos.brand_logo'),
                'tagline' => config('pos.tagline'),
                'currency_symbol' => config('pos.currency_symbol'),
            ]);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

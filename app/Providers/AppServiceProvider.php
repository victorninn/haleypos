<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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

        /*
         * Branding composer.
         *
         * - Superadmin views ALWAYS see platform branding (config/platform.php)
         *   and never tenant logos/colors.
         * - Tenant views see THEIR business branding (logo, name, primary_color).
         *   Fallback is the global pos.php config.
         */
        View::composer('*', function ($view) {
            $name      = $view->getName();
            $isSuperUi = str_starts_with($name, 'superadmin.');

            if ($isSuperUi) {
                $view->with('posBrand', [
                    'name'            => config('platform.name'),
                    'logo'            => config('platform.logo'),
                    'tagline'         => config('platform.tagline'),
                    'currency_symbol' => config('pos.currency_symbol'),
                    'primary_color'   => config('platform.accent'),
                ]);
                return;
            }

            $business = null;
            if (Auth::check()) {
                $business = Auth::user()->business;
            } elseif (view()->shared('currentBusiness')) {
                $business = view()->shared('currentBusiness');
            }

            if ($business) {
                $view->with('posBrand', [
                    'name'            => $business->name,
                    'logo'            => $business->logoUrl() ?? config('pos.brand_logo'),
                    'tagline'         => config('pos.tagline'),
                    'currency_symbol' => $business->currency_symbol ?: config('pos.currency_symbol'),
                    'primary_color'   => $business->primary_color ?: '#f97316',
                ]);
            } else {
                $view->with('posBrand', [
                    'name'            => config('pos.brand_name'),
                    'logo'            => config('pos.brand_logo'),
                    'tagline'         => config('pos.tagline'),
                    'currency_symbol' => config('pos.currency_symbol'),
                    'primary_color'   => '#f97316',
                ]);
            }
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

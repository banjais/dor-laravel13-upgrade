<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\WebsiteThemeSetting;
use App\Models\WebsiteIdentitySetting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        View::composer(
            [
                'layout.client.head',
                'layout.client.head_2021',
            ],
            function ($view) {
                $themeSettings = Cache::rememberForever(
                    'website_theme_global',
                    fn () => WebsiteThemeSetting::current()->toArray()
                );

                $view->with('websiteTheme', $themeSettings);
            }
        );


        View::composer(
            'layout.client.header_2021',
            function ($view) {
                $identitySettings = Cache::rememberForever(
                    'website_identity_global',
                    fn () => WebsiteIdentitySetting::current()->toArray()
                );

                $view->with('websiteIdentity', $identitySettings);

                $languageId = session('language_id', 1);
                $view->with(
                    'nepalSambat',
                    \App\Services\NepalSambatService::today($languageId)
                );
            }
        );
    }
}

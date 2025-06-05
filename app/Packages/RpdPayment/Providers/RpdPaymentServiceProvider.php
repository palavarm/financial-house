<?php

namespace App\Packages\RpdPayment\Providers;

use Illuminate\Support\Facades\Blade;
use App\Packages\RpdPayment\Abstract\AbstractServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Facades\View;

class RpdPaymentServiceProvider extends AbstractServiceProvider
{
    protected string $namespace = 'rpd_payment';

    protected string $path = __DIR__;

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        parent::boot();

        View::addLocation(app_path('Themes'));

        $this->loadViewsFrom(app_path() . '/Themes', 'themes');

        // Livewire
        Livewire::componentNamespace('App\\Packages\\RpdPayment\\Views', 'rpd_payment');

        Blade::componentNamespace('App\\Packages\\RpdPayment\\Views', 'rpd_payment');

        // Anonymous Admin Components
        Blade::anonymousComponentPath(base_path().'/app/Themes/admin/default/views', 'admin');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        parent::register();

        // Facades
        $this->app->bind('ReportingApiService', 'App\Packages\RpdPayment\Services\ReportingApiService');
    }
}

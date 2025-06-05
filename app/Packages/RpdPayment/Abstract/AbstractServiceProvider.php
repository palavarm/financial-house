<?php

namespace App\Packages\RpdPayment\Abstract;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class AbstractServiceProvider extends BaseServiceProvider
{
    protected string $namespace;

    protected string $path;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (File::exists($this->path . '/../Database/Migrations')) {
            $this->loadMigrationsFrom($this->path . '/../Database/Migrations');
        }

        if (File::exists($this->path . '/../Routes/web.php')) {
            $this->loadRoutesFrom($this->path . '/../Routes/web.php');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        parent::register();

        // Automatically apply the package configuration
        if (File::exists($this->path . '/../Config/package.php')) {
            $this->mergeConfigFrom($this->path . '/../Config/package.php', $this->namespace);
        }
    }
}

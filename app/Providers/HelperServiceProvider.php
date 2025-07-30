<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\RoleHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('rolehelper', function ($app) {
            return new RoleHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 
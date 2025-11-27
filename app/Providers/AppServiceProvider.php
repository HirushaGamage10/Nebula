<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register nonce directive
        Blade::directive('nonce', function () {
            return "<?php echo 'nonce=\"' . app('csp_nonce', '') . '\"'; ?>";
        });
    }
}
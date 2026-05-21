<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request; // <-- Kita pakai Facade Request resmi Laravel

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
        // Deteksi domain ngrok menggunakan Facade statis agar VS Code tidak bingung
        if (str_contains(Request::fullUrl(), 'ngrok-free')) {
            URL::forceScheme('https');
        }
    }
}

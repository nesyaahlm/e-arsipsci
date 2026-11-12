<?php

namespace App\Providers;

use App\Support\PencatatAktivitas;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        PencatatAktivitas::daftarkan();
    }
}
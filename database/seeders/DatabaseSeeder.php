<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LayananSeeder::class,
            JenisDokumenSeeder::class,
            UserSeeder::class,
            DokumenSeeder::class,
            AktivitasSeeder::class,
            KoleksiUserSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach (['Laboratorium', 'Inspeksi', 'Sertifikasi', 'Konsultasi', 'Administrasi dan SDM'] as $nama) {
            DB::table('unit')->insert([
                'nama' => $nama,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

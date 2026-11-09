<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach (['Sertifikasi', 'Inspeksi', 'Pengujian', 'Konsultasi', 'Lainnya'] as $nama) {
            DB::table('layanan')->insert([
                'nama' => $nama,
                'deskripsi' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BagianSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = [
            ['LAB', 'Laboratorium', 10],
            ['INS', 'Inspeksi', 10],
            ['SRT', 'Sertifikasi', 10],
            ['ADM', 'Administrasi', 5],
            ['KEU', 'Keuangan', 5],
        ];

        foreach ($data as [$kode, $nama, $retensi]) {
            DB::table('bagian')->updateOrInsert(
                ['kode' => $kode],
                [
                    'nama' => $nama,
                    'retensi_tahun' => $retensi,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
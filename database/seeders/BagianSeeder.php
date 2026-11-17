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
            ['KEU', 'Keuangan dan Akuntansi', 10],
            ['SDM', 'Sumber Daya Manusia dan Umum', 5],
            ['LAB', 'Pengujian dan Laboratorium Lingkungan', 10],
            ['INS', 'Inspeksi Teknik dan Komoditas', 10],
            ['SRT', 'Sertifikasi dan Sistem Manajemen', 10],
            ['KMS', 'Pemasaran dan Komersil', 5],
            ['QHS', 'Quality, Health, Safety & Environment', 7],
            ['TI', 'Teknologi Informasi dan Digitalisasi', 5],
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
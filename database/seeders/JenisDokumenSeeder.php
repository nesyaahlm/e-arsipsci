<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $layanan = DB::table('layanan')->pluck('id', 'nama');

        $data = [
            ['LHU', 'Laporan Hasil Uji', 10, 'Pengujian'],
            ['BA', 'Berita Acara', 5, 'Sertifikasi'],
            ['SRT', 'Sertifikat', 10, 'Sertifikasi'],
            ['SPH', 'Surat Penawaran', 5, 'Inspeksi'],
            ['LIN', 'Laporan Inspeksi', 10, 'Inspeksi'],
            ['KTR', 'Kontrak dan Perjanjian (SPK)', 5, 'Konsultasi'],
            ['SOP', 'SOP dan Instruksi Kerja', 10, 'Lainnya'],
            ['ABS', 'Dokumen Absensi', 5, 'Lainnya'],
            ['SDM', 'Dokumen Kepegawaian', 10, 'Lainnya'],
            ['SMS', 'Surat Masuk', null, 'Lainnya'],
            ['SKL', 'Surat Keluar', null, 'Pengujian'],
        ];

        foreach ($data as [$kode, $nama, $retensi, $namaLayanan]) {
            $id = DB::table('jenis_dokumen')->insertGetId([
                'kode' => $kode,
                'nama' => $nama,
                'retensi_tahun' => $retensi,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('jenis_dokumen_layanan')->insert([
                'jenis_dokumen_id' => $id,
                'layanan_id' => $layanan[$namaLayanan],
            ]);
        }
    }
}

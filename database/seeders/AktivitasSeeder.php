<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AktivitasSeeder extends Seeder
{
    public function run(): void
    {
        $user = DB::table('users')->pluck('id', 'email');

        $lhu = DB::table('dokumen')->where('nomor', 'LHU/CLC/2026/0418')->value('id');
        $ba = DB::table('dokumen')->where('judul', 'Berita Acara Inspeksi Muatan Kapal MV Nusantara')->value('id');

        $log = [
            ['dewi.l@sucofindo.co.id', 'mengunggah', $lhu, null, '2026-10-28 14:20:00'],
            ['bagas.p@sucofindo.co.id', 'mengelola_user', null, 'Menonaktifkan Rizky Hidayat', '2026-11-06 10:10:00'],
            ['bagas.p@sucofindo.co.id', 'mengunduh', $ba, null, '2026-11-08 11:10:00'],
        ];

        foreach ($log as [$email, $aktivitas, $dokumenId, $keterangan, $waktu]) {
            DB::table('log_aktivitas')->insert([
                'user_id' => $user[$email],
                'aktivitas' => $aktivitas,
                'dokumen_id' => $dokumenId,
                'keterangan' => $keterangan,
                'created_at' => $waktu,
            ]);
        }
    }
}

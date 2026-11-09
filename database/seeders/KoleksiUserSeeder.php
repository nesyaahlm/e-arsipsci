<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KoleksiUserSeeder extends Seeder
{
    public function run(): void
    {
        $siti = DB::table('users')->where('email', 'siti.r@sucofindo.co.id')->value('id');
        $dokumen = DB::table('dokumen')->whereNotNull('nomor')->pluck('id', 'nomor');

        foreach (['SOP/CLC/2026/0399', 'LHU/CLC/2026/0418'] as $nomor) {
            DB::table('favorit')->insert([
                'user_id' => $siti,
                'dokumen_id' => $dokumen[$nomor],
            ]);
        }

        foreach (['BA/CLC/2026/0211', 'LIN/CLC/2026/0088'] as $nomor) {
            DB::table('riwayat_dibuka')->insert([
                'user_id' => $siti,
                'dokumen_id' => $dokumen[$nomor],
                'dibuka_pada' => now()->subDay(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');
        $layanan = DB::table('layanan')->pluck('id', 'nama');

        $data = [
            ['Bagas Prakoso', 'bagas.p@sucofindo.co.id', 'admin', null, 'aktif'],
            ['Aqeela Calista', 'aqeela@gmail.com', 'admin', 'Konsultasi', 'aktif'],
            ['Fatah Syah', 'fatah@gmail.com', 'admin', 'Pengujian', 'aktif'],
            ['Flavio Zaviera', 'vio@gmail.com', 'admin', 'Inspeksi', 'aktif'],
            ['Salwa R. Ilmi', 'salwa@gmail.com', 'admin', 'Pengujian', 'aktif'],
            ['Sandrinna Michel', 'sandrinna@gmail.com', 'user', 'Sertifikasi', 'aktif'],
            ['Jefan Natanio', 'jefan@gmail.com', 'user', 'Pengujian', 'nonaktif'],
            ['Gema Vyandra', 'gema@gmail.com', 'user', 'Sertifikasi', 'aktif'],
            ['Annisa Nur A', 'amel@gmail.com', 'user', 'Konsultasi', 'nonaktif'],
            ['Nesyabella Halim', 'nesya@gmail.com', 'user', 'Pengujian', 'aktif'],
            ['Siti Rahmawati', 'siti.r@sucofindo.co.id', 'user', 'Pengujian', 'aktif'],
            ['Dewi Lestari', 'dewi.l@sucofindo.co.id', 'user', 'Pengujian', 'aktif'],
            ['Agus Prasetyo', 'agus.p@sucofindo.co.id', 'user', 'Inspeksi', 'aktif'],
            ['Hendra Wijaya', 'hendra.w@sucofindo.co.id', 'user', 'Inspeksi', 'aktif'],
        ];

        foreach ($data as [$name, $email, $role, $namaLayanan, $status]) {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'layanan_id' => $namaLayanan ? $layanan[$namaLayanan] : null,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

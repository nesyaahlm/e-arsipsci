<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@sucofindo.co.id'],
            [
                'name' => 'Superadmin Kantor Pusat',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@sucofindo.co.id'],
            [
                'name' => 'Bagas Prakoso',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@sucofindo.co.id'],
            [
                'name' => 'Staf Arsip',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'aktif',
            ]
        );

        $this->call([
            CabangSeeder::class,
            BagianSeeder::class,
            KodeSuratSeeder::class,
        ]);
    }
}
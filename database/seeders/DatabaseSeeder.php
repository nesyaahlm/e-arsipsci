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
    }
}
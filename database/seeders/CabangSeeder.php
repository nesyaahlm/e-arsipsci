<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Dokumen;
use App\Models\Sampel;
use App\Models\User;
use Illuminate\Database\Seeder;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        $daftarCabang = [
            ['kode' => 'CLC', 'nama' => 'Cabang Cilacap', 'kota' => 'Cilacap', 'alamat' => 'Jl. Ir. H. Juanda No. 12, Cilacap, Jawa Tengah'],
            ['kode' => 'SMG', 'nama' => 'Cabang Semarang', 'kota' => 'Semarang', 'alamat' => 'Jl. Pemuda No. 142, Semarang, Jawa Tengah'],
            ['kode' => 'SUB', 'nama' => 'Cabang Surabaya', 'kota' => 'Surabaya', 'alamat' => 'Jl. Raya Darmo No. 88, Surabaya, Jawa Timur'],
            ['kode' => 'JKT', 'nama' => 'Cabang Utama Jakarta', 'kota' => 'Jakarta Pusat', 'alamat' => 'Jl. Raya Pasar Minggu Kav. 34, Jakarta Selatan'],
            ['kode' => 'BPN', 'nama' => 'Cabang Balikpapan', 'kota' => 'Balikpapan', 'alamat' => 'Jl. Jenderal Sudirman No. 45, Balikpapan, Kalimantan Timur'],
            ['kode' => 'MDN', 'nama' => 'Cabang Medan', 'kota' => 'Medan', 'alamat' => 'Jl. Putri Hijau No. 20, Medan, Sumatera Utara'],
            ['kode' => 'BDG', 'nama' => 'Cabang Bandung', 'kota' => 'Bandung', 'alamat' => 'Jl. Surapati No. 71, Bandung, Jawa Barat'],
        ];

        foreach ($daftarCabang as $item) {
            Cabang::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }

        $cabangCilacap = Cabang::where('kode', 'CLC')->first();

        if ($cabangCilacap) {
            Dokumen::whereNull('cabang_id')->update(['cabang_id' => $cabangCilacap->id]);
            Sampel::whereNull('cabang_id')->update(['cabang_id' => $cabangCilacap->id]);
            User::where('role', '!=', 'superadmin')->whereNull('cabang_id')->update(['cabang_id' => $cabangCilacap->id]);
        }
    }
}
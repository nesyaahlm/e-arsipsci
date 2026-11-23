<?php

namespace Database\Seeders;

use App\Models\Portofolio;
use Illuminate\Database\Seeder;

class PortofolioSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'MNR',
                'nama' => 'Mineral dan Batubara (Minerba)',
                'retensi_tahun' => 3,
                'retensi_satuan' => 'bulan',
            ],
            [
                'kode' => 'MGS',
                'nama' => 'Minyak, Gas dan Energi Terbarukan',
                'retensi_tahun' => 6,
                'retensi_satuan' => 'bulan',
            ],
            [
                'kode' => 'AGR',
                'nama' => 'Pertanian, Perkebunan dan Kelapa Sawit (CPO)',
                'retensi_tahun' => 6,
                'retensi_satuan' => 'bulan',
            ],
            [
                'kode' => 'ENV',
                'nama' => 'Pengujian Lingkungan Hidup dan K3',
                'retensi_tahun' => 1,
                'retensi_satuan' => 'bulan',
            ],
            [
                'kode' => 'FOD',
                'nama' => 'Industri Makanan, Minuman dan Farmasi',
                'retensi_tahun' => 3,
                'retensi_satuan' => 'bulan',
            ],
            [
                'kode' => 'IND',
                'nama' => 'Industri Manufaktur, Kimia dan Logam',
                'retensi_tahun' => 1,
                'retensi_satuan' => 'tahun',
            ],
        ];

        foreach ($data as $item) {
            Portofolio::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'cabang_id' => null,
                    'nama' => $item['nama'],
                    'retensi_tahun' => $item['retensi_tahun'],
                    'retensi_satuan' => $item['retensi_satuan'],
                ]
            );
        }
    }
}
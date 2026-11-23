<?php

namespace Database\Seeders;

use App\Models\Portofolio;
use App\Models\KodeSurat;
use Illuminate\Database\Seeder;

class KodeSampelSeeder extends Seeder
{
    public function run(): void
    {
        $portofolioList = Portofolio::all()->keyBy('kode');

        $data = [
            'MNR' => [
                ['kode' => 'MNR-BB', 'nama' => 'Batubara (Coal Sampling)', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel komoditas batubara ekspor dan domestik'],
                ['kode' => 'MNR-NK', 'nama' => 'Bijih Nikel (Nickel Ore)', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel pengujian kadar nikel dan feronikel'],
                ['kode' => 'MNR-BS', 'nama' => 'Bauksit dan Alumina', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel mineral bijih bauksit smelter'],
                ['kode' => 'MNR-MN', 'nama' => 'Mineral Logam dan Pasir Besi', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel mineral batuan dan konsentrat mineral'],
            ],
            'MGS' => [
                ['kode' => 'MGS-CRU', 'nama' => 'Minyak Mentah (Crude Oil)', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel minyak mentah perkapalan dan tangki timbun'],
                ['kode' => 'MGS-BBM', 'nama' => 'Bahan Bakar Minyak (BBM)', 'retensi_aktif' => 1, 'retensi_inaktif' => 1, 'keterangan' => 'Sampel produk BBM industri dan SPBU'],
                ['kode' => 'MGS-LPG', 'nama' => 'Gas Bumi dan LPG / LNG', 'retensi_aktif' => 1, 'retensi_inaktif' => 1, 'keterangan' => 'Sampel gas cair dan gas alam terkompresi'],
            ],
            'AGR' => [
                ['kode' => 'AGR-CPO', 'nama' => 'Crude Palm Oil (CPO)', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel minyak kelapa sawit mentah'],
                ['kode' => 'AGR-PKO', 'nama' => 'Palm Kernel Oil dan Turunan', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel inti sawit dan minyak olahan'],
                ['kode' => 'AGR-PUP', 'nama' => 'Pupuk dan Hasil Perkebunan', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel pupuk NPK, urea, dan komoditas pertanian'],
            ],
            'ENV' => [
                ['kode' => 'ENV-AIR', 'nama' => 'Air Limbah dan Air Bersih', 'retensi_aktif' => 1, 'retensi_inaktif' => 1, 'keterangan' => 'Sampel uji baku mutu air limbah industri dan air minum'],
                ['kode' => 'ENV-UDR', 'nama' => 'Emisi Cerobong dan Udara Ambien', 'retensi_aktif' => 1, 'retensi_inaktif' => 1, 'keterangan' => 'Sampel kualitas udara lingkungan kerja dan emisi pabrik'],
                ['kode' => 'ENV-TNH', 'nama' => 'Tanah dan Limbah B3 Padat', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel toksikologi tanah dan limbah padat'],
            ],
            'FOD' => [
                ['kode' => 'FOD-FOD', 'nama' => 'Produk Makanan dan Minuman Olahan', 'retensi_aktif' => 1, 'retensi_inaktif' => 1, 'keterangan' => 'Sampel keamanan pangan dan uji mikrobiologi'],
                ['kode' => 'FOD-DRG', 'nama' => 'Produk Farmasi dan Kosmetik', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel sediaan obat, suplemen, dan kosmetik'],
            ],
            'IND' => [
                ['kode' => 'IND-KIM', 'nama' => 'Bahan Kimia Industri', 'retensi_aktif' => 1, 'retensi_inaktif' => 2, 'keterangan' => 'Sampel bahan baku kimia dan pelarut industri'],
                ['kode' => 'IND-MTL', 'nama' => 'Produk Baja dan Komponen Logam', 'retensi_aktif' => 2, 'retensi_inaktif' => 3, 'keterangan' => 'Sampel uji tarik dan komposisi logam manufaktur'],
            ],
        ];

        foreach ($data as $kodePorto => $daftarKlasifikasi) {
            if (! isset($portofolioList[$kodePorto])) {
                continue;
            }

            $portofolioId = $portofolioList[$kodePorto]->id;

            foreach ($daftarKlasifikasi as $item) {
                KodeSurat::updateOrCreate(
                    ['kode' => $item['kode']],
                    [
                        'portofolio_id' => $portofolioId,
                        'bagian_id' => null,
                        'nama' => $item['nama'],
                        'retensi_aktif' => $item['retensi_aktif'],
                        'retensi_inaktif' => $item['retensi_inaktif'],
                        'keterangan' => $item['keterangan'],
                    ]
                );
            }
        }
    }
}
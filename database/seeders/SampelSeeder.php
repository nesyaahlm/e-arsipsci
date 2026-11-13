<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampelSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = DB::table('layanan')->pluck('id', 'nama');
        $user = DB::table('users')->pluck('id', 'email');

        $data = [
            [
                'SMP-2610-001', 'Batubara Lot 2610-C', 'PT Bumi Energi Nusantara', 'MV Nusantara Jaya', 'BL/BEN/2610/0418',
                '2026-10-26', 'Dermaga Pelabuhan Tanjung Intan, Cilacap', 'Penentuan kalori dan kadar sulfur batubara',
                'Pengujian', '01', 'Ruang Arsip Sampel A', '2026-10-28', 'dewi.l@sucofindo.co.id',
                'Sampel batubara hasil sampling dari tongkang sebelum muat.',
            ],
            [
                'SMP-2610-002', 'Air Limbah Outlet IPAL', null, null, null,
                '2026-10-27', 'IPAL Kawasan Industri Cilacap', 'Uji parameter BOD, COD, dan TSS',
                'Pengujian', '01', 'Ruang Arsip Sampel A', '2026-10-28', 'siti.r@sucofindo.co.id',
                'Sampel air limbah diambil dari titik outlet IPAL.',
            ],
            [
                'SMP-2610-003', 'Solar HSD Tanki T-04', 'PT Pertamina Patra Niaga', 'MT Samudra Baru', 'BL/PPN/2610/0092',
                '2026-10-29', 'Terminal BBM Cilacap', 'Inspeksi kualitas dan kuantitas BBM',
                'Inspeksi', '02', 'Ruang Arsip Sampel B', '2026-10-30', 'agus.p@sucofindo.co.id',
                'Sampel BBM jenis solar dari tanki timbun nomor 4.',
            ],
            [
                'SMP-2611-004', 'Pupuk Urea Curah', 'PT Pupuk Nusantara', 'MV Tani Makmur', 'BL/PPN/2611/0031',
                '2026-11-02', 'Gudang Curah Pelabuhan Cilacap', 'Uji kadar nitrogen dan kadar air',
                'Pengujian', '02', 'Ruang Arsip Sampel B', '2026-11-03', 'nesya@gmail.com',
                'Sampel pupuk urea dari kapal sebelum bongkar.',
            ],
            [
                'SMP-2611-005', 'Klinker Semen', 'PT Semen Indonesia Logistik', 'MV Gresik Raya', 'BL/SIL/2611/0107',
                '2026-11-03', 'Dermaga Khusus Semen Cilacap', 'Inspeksi muatan dan uji komposisi kimia',
                'Inspeksi', '03', 'Ruang Arsip Sampel C', '2026-11-04', 'hendra.w@sucofindo.co.id',
                'Sampel klinker diambil dari hold nomor 2 kapal.',
            ],
            [
                'SMP-2611-006', 'Minyak Sawit Mentah CPO', 'PT Sawit Karya Mandiri', 'MT Palma Express', 'BL/SKM/2611/0254',
                '2026-11-04', 'Tanki Timbun CPO Pelabuhan Cilacap', 'Uji kadar asam lemak bebas dan kadar air',
                'Pengujian', '03', 'Ruang Arsip Sampel C', '2026-11-05', 'dewi.l@sucofindo.co.id',
                'Sampel CPO dari tanki timbun nomor 1.',
            ],
            [
                'SMP-2611-007', 'Air Baku Sumur Produksi', null, null, null,
                '2026-11-05', 'Sumur Produksi PT Tirta Jaya, Cilacap', 'Sertifikasi kualitas air baku',
                'Sertifikasi', '04', 'Ruang Arsip Sampel D', '2026-11-06', 'sandrinna@gmail.com',
                'Sampel air baku untuk keperluan sertifikasi mutu air.',
            ],
            [
                'SMP-2611-008', 'Tanah Lokasi Proyek Tangki', null, null, null,
                '2026-11-06', 'Lokasi Proyek Tangki Baru, Kawasan Industri Cilacap', 'Konsultasi uji geoteknik dan kontaminasi tanah',
                'Konsultasi', '04', 'Ruang Arsip Sampel D', '2026-11-07', 'aqeela@gmail.com',
                'Sampel tanah dari tiga titik bor.',
            ],
            [
                'SMP-2611-009', 'Gula Kristal Putih', 'PT Gula Manis Sejahtera', 'MV Sugar Star', 'BL/GMS/2611/0066',
                '2026-11-08', 'Gudang Impor Pelabuhan Cilacap', 'Uji kadar ICUMSA dan kadar air',
                'Pengujian', '05', 'Ruang Arsip Sampel E', '2026-11-09', 'siti.r@sucofindo.co.id',
                'Sampel gula kristal dari karung kemasan 50 kg.',
            ],
            [
                'SMP-2611-010', 'Sampel Pembanding Batubara', null, null, null,
                '2026-11-10', 'Laboratorium Sucofindo Cilacap', 'Arsip sampel pembanding untuk uji banding',
                'Lainnya', '05', 'Ruang Arsip Sampel E', '2026-11-11', 'bagas.p@sucofindo.co.id',
                'Sampel pembanding disimpan sesuai masa retensi.',
            ],
        ];

        foreach ($data as [$kode, $nama, $shipper, $vessel, $bl, $tglAmbil, $tempat, $analysis, $namaLayanan, $nomorRuang, $namaRuang, $tglMasuk, $pengunggah, $deskripsi]) {
            $waktu = Carbon::parse($tglMasuk)->setTime(9, 0);

            DB::table('sampel')->updateOrInsert(
                ['kode_sampel' => $kode],
                [
                    'nama_sampel' => $nama,
                    'shipper' => $shipper,
                    'vessel' => $vessel,
                    'bill_of_lading' => $bl,
                    'tanggal_pengambilan' => $tglAmbil,
                    'tempat_pengambilan' => $tempat,
                    'analysis_for' => $analysis,
                    'layanan_id' => $layanan[$namaLayanan],
                    'nomor_ruang_arsip' => $nomorRuang,
                    'nama_ruang_arsip' => $namaRuang,
                    'tanggal_masuk_arsip' => $tglMasuk,
                    'foto_path' => null,
                    'deskripsi' => $deskripsi,
                    'diunggah_oleh' => $user[$pengunggah],
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]
            );

            $sampelId = DB::table('sampel')->where('kode_sampel', $kode)->value('id');

            $sudahAda = DB::table('log_aktivitas')
                ->where('sampel_id', $sampelId)
                ->where('aktivitas', 'mengunggah_sampel')
                ->exists();

            if (! $sudahAda) {
                DB::table('log_aktivitas')->insert([
                    'user_id' => $user[$pengunggah],
                    'aktivitas' => 'mengunggah_sampel',
                    'dokumen_id' => null,
                    'sampel_id' => $sampelId,
                    'keterangan' => $nama,
                    'created_at' => $waktu,
                ]);
            }
        }
    }
}
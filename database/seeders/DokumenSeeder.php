<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DokumenSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = DB::table('layanan')->pluck('id', 'nama');
        $user = DB::table('users')->pluck('id', 'email');
        $jenis = DB::table('jenis_dokumen')->get()->keyBy('kode');

        $dekat = Carbon::today()->addDays(45);

        $data = [
            ['LHU/CLC/2026/0418', 'Laporan Hasil Uji Batubara Lot 2610-C', 'Pengujian', 'LHU', 'Laboratorium', '2026-10-28', 'dewi.l@sucofindo.co.id', 'Rak A-02 / Box 14', 1677722],
            ['SRT/CLC/2026/0127', 'Sertifikat ISO 9001:2015', 'Sertifikasi', 'SRT', null, '2026-04-15', 'sandrinna@gmail.com', null, null],
            ['LIN/CLC/2026/0088', 'Laporan Inspeksi Tanki Timbun BBM', 'Inspeksi', 'LIN', null, '2026-06-03', 'hendra.w@sucofindo.co.id', null, null],
            ['SOP/CLC/2026/0399', 'SOP Preparasi Sampel Air Limbah', 'Pengujian', 'SOP', 'Laboratorium', '2026-02-11', 'siti.r@sucofindo.co.id', null, null],
            ['BA/CLC/2026/0211', 'Berita Acara Audit Sertifikasi SMK3', 'Sertifikasi', 'BA', null, '2026-05-20', 'gema@gmail.com', null, null],
            ['KTR/CLC/2026/0057', 'Surat Penawaran Jasa Analisis', 'Pengujian', 'KTR', null, '2026-02-27', 'siti.r@sucofindo.co.id', null, null],
            [null, 'Dokumen Absensi Triwulan IV', 'Lainnya', 'ABS', null, $dekat->copy()->subYears(5)->toDateString(), 'nesya@gmail.com', null, null],
            [null, 'Berita Acara Serah Terima Contoh Uji Banding', 'Pengujian', 'BA', null, '2026-11-03', 'dewi.l@sucofindo.co.id', null, null],
            [null, 'Berita Acara Inspeksi Muatan Kapal MV Nusantara', 'Inspeksi', 'BA', null, '2026-11-02', 'agus.p@sucofindo.co.id', null, null],
            [null, 'Surat Masuk Hasil Uji', 'Pengujian', 'SMS', null, '2026-11-01', 'bagas.p@sucofindo.co.id', null, null],
            [null, 'Surat Keluar Hasil Uji Batubara', 'Pengujian', 'SKL', null, '2026-10-10', 'bagas.p@sucofindo.co.id', null, null],
            [null, 'Berita Acara Inspeksi Muatan Kapal Persero', 'Inspeksi', 'BA', null, '2026-10-09', 'agus.p@sucofindo.co.id', null, null],
        ];

        foreach ($data as [$nomor, $judul, $namaLayanan, $kode, $unit, $tgl, $pengunggah, $lokasi, $ukuran]) {
            $j = $jenis[$kode];
            $tanggal = Carbon::parse($tgl);
            $slug = Str::slug($judul);

            if ($nomor === null) {
                $awalan = $kode . '/CLC/' . $tanggal->year . '/';
                $terakhir = DB::table('dokumen')->where('nomor', 'like', $awalan . '%')->max('nomor');
                $urut = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;
                $nomor = $awalan . str_pad($urut, 4, '0', STR_PAD_LEFT);
            }

            DB::table('dokumen')->insert([
                'nomor' => $nomor,
                'judul' => $judul,
                'layanan_id' => $layanan[$namaLayanan],
                'jenis_dokumen_id' => $j->id,
                'unit' => $unit,
                'tanggal_dokumen' => $tanggal->toDateString(),
                'deskripsi' => null,
                'file_path' => 'dokumen/contoh/' . $slug . '.pdf',
                'file_ukuran' => $ukuran ?? (1000000 + crc32($judul) % 1500000),
                'lokasi_arsip_fisik' => $lokasi,
                'diunggah_oleh' => $user[$pengunggah],
                'retensi_sampai' => $j->retensi_tahun ? $tanggal->copy()->addYears($j->retensi_tahun)->toDateString() : null,
                'created_at' => $tanggal->copy()->setTime(9, 0),
                'updated_at' => $tanggal->copy()->setTime(9, 0),
            ]);
        }
    }
}

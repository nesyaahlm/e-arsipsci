<?php

namespace Database\Seeders;

use App\Models\Bagian;
use App\Models\KodeSurat;
use Illuminate\Database\Seeder;

class KodeSuratSeeder extends Seeder
{
    public function run(): void
    {
        $bagianList = Bagian::all()->keyBy('kode');

        $data = [
            'KEU' => [
                ['kode' => 'KEU-INV', 'nama' => 'Invoice dan Faktur Tagihan', 'retensi_aktif' => 2, 'retensi_inaktif' => 8, 'keterangan' => 'Dokumen penagihan resmi jasa inspeksi dan pengujian'],
                ['kode' => 'KEU-KWI', 'nama' => 'Kuitansi Pembayaran', 'retensi_aktif' => 2, 'retensi_inaktif' => 8, 'keterangan' => 'Bukti penerimaan dan pengeluaran kas atau bank'],
                ['kode' => 'KEU-FP', 'nama' => 'Faktur Pajak Standar', 'retensi_aktif' => 2, 'retensi_inaktif' => 8, 'keterangan' => 'Faktur pajak pertambahan nilai (PPN) dan PPh'],
                ['kode' => 'KEU-PO', 'nama' => 'Purchase Order Pengadaan', 'retensi_aktif' => 2, 'retensi_inaktif' => 5, 'keterangan' => 'Surat pesanan pengadaan barang dan operasional'],
            ],
            'SDM' => [
                ['kode' => 'SDM-SK', 'nama' => 'Surat Keputusan Direksi dan Cabang', 'retensi_aktif' => 5, 'retensi_inaktif' => 10, 'keterangan' => 'SK pengangkatan, mutasi, dan penugasan struktural'],
                ['kode' => 'SDM-SPK', 'nama' => 'Surat Perjanjian Kerja', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Kontrak kerja waktu tertentu dan tidak tertentu'],
                ['kode' => 'SDM-SPT', 'nama' => 'Surat Perintah Tugas Operasional', 'retensi_aktif' => 2, 'retensi_inaktif' => 3, 'keterangan' => 'Surat penugasan personil ke lokasi proyek atau lapangan'],
            ],
            'LAB' => [
                ['kode' => 'LAB-LHU', 'nama' => 'Laporan Hasil Uji (LHU)', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Sertifikat hasil uji laboratorium lingkungan dan kimia'],
                ['kode' => 'LAB-COA', 'nama' => 'Certificate of Analysis (CoA)', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Sertifikat analisis laboratorium untuk komoditas dan industri'],
                ['kode' => 'LAB-LOG', 'nama' => 'Logsheet Pengujian Laboratorium', 'retensi_aktif' => 2, 'retensi_inaktif' => 5, 'keterangan' => 'Catatan teknis dan lembar kerja harian analis laboratorium'],
            ],
            'INS' => [
                ['kode' => 'INS-LHI', 'nama' => 'Laporan Hasil Inspeksi Teknis', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Laporan hasil verifikasi dan inspeksi fisik lapangan'],
                ['kode' => 'INS-COI', 'nama' => 'Certificate of Inspection (CoI)', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Sertifikat inspeksi resmi perdagangan ekspor dan impor'],
                ['kode' => 'INS-BA', 'nama' => 'Berita Acara Pemeriksaan Fisik', 'retensi_aktif' => 2, 'retensi_inaktif' => 5, 'keterangan' => 'Berita acara sampling dan pemeriksaan fisik awal'],
            ],
            'SRT' => [
                ['kode' => 'SRT-CS', 'nama' => 'Sertifikat Sistem Manajemen', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Sertifikat pemenuhan standar ISO 9001, 14001, dan 45001'],
                ['kode' => 'SRT-AUD', 'nama' => 'Laporan Audit Sertifikasi', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Dokumentasi audit sertifikasi tahap satu dan tahap dua'],
            ],
            'KMS' => [
                ['kode' => 'KMS-SPH', 'nama' => 'Surat Penawaran Harga', 'retensi_aktif' => 2, 'retensi_inaktif' => 3, 'keterangan' => 'Proposal komersial penawaran jasa kepada pengguna layanan'],
                ['kode' => 'KMS-KTR', 'nama' => 'Kontrak Kerjasama Pelanggan', 'retensi_aktif' => 3, 'retensi_inaktif' => 5, 'keterangan' => 'Perjanjian kontrak kerja dan kesepakatan payung MoU'],
            ],
            'QHS' => [
                ['kode' => 'QHS-SOP', 'nama' => 'Prosedur Mutu dan K3 (SOP)', 'retensi_aktif' => 5, 'retensi_inaktif' => 5, 'keterangan' => 'Dokumen standarisasi operasional dan keselamatan kerja'],
                ['kode' => 'QHS-LAP', 'nama' => 'Laporan Insiden dan K3 Lingkungan', 'retensi_aktif' => 3, 'retensi_inaktif' => 7, 'keterangan' => 'Rekapitulasi pelaksanaan kesehatan dan keselamatan kerja'],
            ],
            'TI' => [
                ['kode' => 'TI-BAST', 'nama' => 'Berita Acara Serah Terima Aset IT', 'retensi_aktif' => 2, 'retensi_inaktif' => 5, 'keterangan' => 'Berita acara serah terima perangkat keras dan sistem informasi'],
                ['kode' => 'TI-LOG', 'nama' => 'Logsheet Pemeliharaan Sistem', 'retensi_aktif' => 1, 'retensi_inaktif' => 3, 'keterangan' => 'Catatan berkala pemeliharaan server, jaringan, dan database'],
            ],
        ];

        foreach ($data as $kodeBagian => $daftarKodeSurat) {
            if (! isset($bagianList[$kodeBagian])) {
                continue;
            }

            $bagianId = $bagianList[$kodeBagian]->id;

            foreach ($daftarKodeSurat as $item) {
                KodeSurat::updateOrCreate(
                    ['kode' => $item['kode']],
                    [
                        'bagian_id' => $bagianId,
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
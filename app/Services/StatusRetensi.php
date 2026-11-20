<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatusRetensi
{
    public const KUNCI_CACHE = 'sinkron-status-retensi';

    public static function sinkron(): array
    {
        $hariIni = now()->toDateString();

        $dariKodeSurat = DB::update("
            UPDATE dokumen d
            JOIN kode_surat k ON k.id = d.kode_surat_id
            SET d.retensi_sampai = DATE_ADD(d.tanggal_dokumen, INTERVAL k.retensi_aktif YEAR)
            WHERE d.deleted_at IS NULL
              AND d.status <> 'pemusnahan'
              AND k.retensi_aktif IS NOT NULL
        ");

        $dariBagian = DB::update("
            UPDATE dokumen d
            JOIN bagian b ON b.id = d.bagian_id
            SET d.retensi_sampai = DATE_ADD(d.tanggal_dokumen, INTERVAL b.retensi_tahun YEAR)
            WHERE d.deleted_at IS NULL
              AND d.status <> 'pemusnahan'
              AND d.kode_surat_id IS NULL
              AND b.retensi_tahun IS NOT NULL
        ");

        $keInaktif = DB::table('dokumen')
            ->whereNull('deleted_at')
            ->where('status', 'aktif')
            ->whereNotNull('retensi_sampai')
            ->where('retensi_sampai', '<', $hariIni)
            ->update(['status' => 'inaktif']);

        $keAktif = DB::table('dokumen')
            ->whereNull('deleted_at')
            ->where('status', 'inaktif')
            ->where(function ($q) use ($hariIni) {
                $q->whereNull('retensi_sampai')->orWhere('retensi_sampai', '>=', $hariIni);
            })
            ->update(['status' => 'aktif']);

        Cache::forget(self::KUNCI_CACHE);

        return [
            'retensi_dihitung_ulang' => $dariKodeSurat + $dariBagian,
            'menjadi_inaktif' => $keInaktif,
            'menjadi_aktif' => $keAktif,
        ];
    }

    public static function sinkronBerkala(): void
    {
        Cache::remember(self::KUNCI_CACHE, now()->addMinutes(10), function () {
            self::sinkron();

            return true;
        });
    }
}
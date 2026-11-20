<?php

namespace App\Console\Commands;

use App\Services\StatusRetensi;
use Illuminate\Console\Command;

class SinkronStatusRetensi extends Command
{
    protected $signature = 'arsip:sinkron-status';

    protected $description = 'Menyesuaikan status dokumen (aktif atau inaktif) dengan masa retensi';

    public function handle(): int
    {
        $hasil = StatusRetensi::sinkron();

        $this->info("Retensi dihitung ulang: {$hasil['retensi_dihitung_ulang']} baris");
        $this->info("Menjadi inaktif: {$hasil['menjadi_inaktif']} dokumen");
        $this->info("Menjadi aktif: {$hasil['menjadi_aktif']} dokumen");

        return self::SUCCESS;
    }
}
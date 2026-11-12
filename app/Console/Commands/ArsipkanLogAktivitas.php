<?php

namespace App\Console\Commands;

use App\Models\LogAktivitas;
use Illuminate\Console\Command;

class ArsipkanLogAktivitas extends Command
{
    protected $signature = 'log:arsipkan {--hari=7 : Umur log (hari) sebelum diarsipkan}';

    protected $description = 'Memindahkan log aktivitas yang lebih lama dari 7 hari ke arsip';

    public function handle(): int
    {
        $jumlah = LogAktivitas::arsipkanLama((int) $this->option('hari'));

        $this->info("{$jumlah} log dipindahkan ke arsip.");

        return self::SUCCESS;
    }
}
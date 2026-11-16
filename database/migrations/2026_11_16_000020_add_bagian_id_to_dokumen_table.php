<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->foreignId('bagian_id')
                ->nullable()
                ->after('judul')
                ->constrained('bagian')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE `dokumen` MODIFY `layanan_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `dokumen` MODIFY `jenis_dokumen_id` BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bagian_id');
        });
    }
};
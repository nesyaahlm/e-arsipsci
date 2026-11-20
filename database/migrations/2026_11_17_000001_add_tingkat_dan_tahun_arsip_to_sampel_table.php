<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->string('nomor_tingkat_arsip', 50)->nullable()->after('nomor_ruang_arsip');
            $table->string('tahun_arsip', 50)->nullable()->after('tanggal_pengambilan');
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropColumn(['nomor_tingkat_arsip', 'tahun_arsip']);
        });
    }
};
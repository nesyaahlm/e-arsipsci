<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->string('kode_rak', 30)->nullable()->after('lokasi_arsip_fisik');
            $table->string('nomor_rak', 30)->nullable()->after('kode_rak');
            $table->string('no_tingkat', 30)->nullable()->after('nomor_rak');
            $table->string('no_box_file', 30)->nullable()->after('no_tingkat');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropColumn(['kode_rak', 'nomor_rak', 'no_tingkat', 'no_box_file']);
        });
    }
};
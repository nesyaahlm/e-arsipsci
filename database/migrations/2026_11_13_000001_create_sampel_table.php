<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sampel', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sampel')->unique();
            $table->string('nama_sampel');
            $table->string('shipper')->nullable();
            $table->string('vessel')->nullable();
            $table->string('bill_of_lading')->nullable();
            $table->date('tanggal_pengambilan');
            $table->string('tempat_pengambilan');
            $table->string('analysis_for');
            $table->foreignId('layanan_id')->constrained('layanan')->restrictOnDelete();
            $table->string('nomor_ruang_arsip');
            $table->string('nama_ruang_arsip');
            $table->date('tanggal_masuk_arsip');
            $table->string('foto_path')->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('diunggah_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->foreignId('sampel_id')
                ->nullable()
                ->after('dokumen_id')
                ->constrained('sampel')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sampel_id');
        });

        Schema::dropIfExists('sampel');
    }
};
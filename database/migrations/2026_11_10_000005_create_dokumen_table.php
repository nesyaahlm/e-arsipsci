<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->string('judul');
            $table->foreignId('layanan_id')->constrained('layanan')->restrictOnDelete();
            $table->foreignId('jenis_dokumen_id')->constrained('jenis_dokumen')->restrictOnDelete();
            $table->string('unit')->nullable();
            $table->date('tanggal_dokumen');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('file_ukuran')->default(0);
            $table->string('lokasi_arsip_fisik')->nullable();
            $table->foreignId('diunggah_oleh')->constrained('users')->restrictOnDelete();
            $table->date('retensi_sampai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};

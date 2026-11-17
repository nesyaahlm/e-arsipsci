<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->date('tanggal');
            $table->string('pelaksana');
            $table->string('bukti_path');
            $table->string('bukti_nama_asli')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('pemusnahan_item', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 10);
            $table->foreignId('dokumen_id')->nullable()->constrained('dokumen')->nullOnDelete();
            $table->foreignId('sampel_id')->nullable()->constrained('sampel')->nullOnDelete();
            $table->string('kode');
            $table->string('judul');
            $table->string('status_sebelumnya', 20)->default('aktif');
            $table->string('lokasi_sebelumnya')->nullable();
            $table->foreignId('diajukan_oleh')->constrained('users')->restrictOnDelete();
            $table->foreignId('pemusnahan_id')->nullable()->constrained('pemusnahan')->restrictOnDelete();
            $table->timestamps();

            $table->unique('dokumen_id');
            $table->unique('sampel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemusnahan_item');
        Schema::dropIfExists('pemusnahan');
    }
};
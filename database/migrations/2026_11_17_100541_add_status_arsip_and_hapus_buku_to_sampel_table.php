<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->enum('status_arsip', ['aktif', 'inaktif', 'hapus_buku'])->default('aktif')->after('deskripsi');
            $table->string('nomor_buku', 100)->nullable()->after('status_arsip');
            $table->date('tanggal_hapus_buku')->nullable()->after('nomor_buku');
            $table->text('keterangan_hapus_buku')->nullable()->after('tanggal_hapus_buku');
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropColumn([
                'status_arsip',
                'nomor_buku',
                'tanggal_hapus_buku',
                'keterangan_hapus_buku',
            ]);
        });
    }
};
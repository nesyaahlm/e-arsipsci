<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_dokumen_layanan', function (Blueprint $table) {
            $table->foreignId('jenis_dokumen_id')->constrained('jenis_dokumen')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();

            $table->primary(['jenis_dokumen_id', 'layanan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumen_layanan');
    }
};

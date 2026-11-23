<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portofolio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->nullable()->constrained('cabang')->nullOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->unsignedSmallInteger('retensi_tahun')->nullable();
            $table->string('retensi_satuan', 10)->default('bulan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portofolio');
    }
};
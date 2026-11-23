<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->foreignId('kode_surat_id')
                ->nullable()
                ->after('portofolio_id')
                ->constrained('kode_surat')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropForeign(['kode_surat_id']);
            $table->dropColumn('kode_surat_id');
        });
    }
};
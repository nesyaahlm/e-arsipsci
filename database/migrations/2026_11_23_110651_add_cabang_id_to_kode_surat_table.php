<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kode_surat', function (Blueprint $table) {
            $table->foreignId('cabang_id')
                ->nullable()
                ->after('portofolio_id')
                ->constrained('cabang')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kode_surat', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
    }
};
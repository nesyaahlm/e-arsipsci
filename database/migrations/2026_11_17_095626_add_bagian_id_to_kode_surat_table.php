<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kode_surat', function (Blueprint $table) {
            $table->foreignId('bagian_id')->after('id')->constrained('bagian')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kode_surat', function (Blueprint $table) {
            $table->dropForeign(['bagian_id']);
            $table->dropColumn('bagian_id');
        });
    }
};
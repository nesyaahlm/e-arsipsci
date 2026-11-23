<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bagian', function (Blueprint $table) {
            $table->string('retensi_satuan', 10)->default('tahun')->after('retensi_tahun');
        });
    }

    public function down(): void
    {
        Schema::table('bagian', function (Blueprint $table) {
            $table->dropColumn('retensi_satuan');
        });
    }
};
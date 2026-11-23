<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->string('keterangan_1')->nullable()->after('tanggal_pengambilan');
            $table->string('keterangan_2')->nullable()->after('keterangan_1');
            $table->string('keterangan_3')->nullable()->after('keterangan_2');
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropColumn(['keterangan_1', 'keterangan_2', 'keterangan_3']);
        });
    }
};
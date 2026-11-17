<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('dokumen', 'status')) {
            DB::statement('ALTER TABLE `dokumen` MODIFY `status` VARCHAR(20) NULL');

            DB::table('dokumen')
                ->where(function ($q) {
                    $q->whereNull('status')->orWhereNotIn('status', ['aktif', 'inaktif', 'pemusnahan']);
                })
                ->update(['status' => 'aktif']);

            DB::statement("ALTER TABLE `dokumen` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'aktif'");
        } else {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->string('status', 20)->default('aktif');
            });
        }

        Schema::table('sampel', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
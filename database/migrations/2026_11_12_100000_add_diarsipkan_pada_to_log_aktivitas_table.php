<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->timestamp('diarsipkan_pada')->nullable()->after('created_at');
            $table->index('diarsipkan_pada');
        });
    }

    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropIndex(['diarsipkan_pada']);
            $table->dropColumn('diarsipkan_pada');
        });
    }
};
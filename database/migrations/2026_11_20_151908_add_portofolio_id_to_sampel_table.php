<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->foreignId('portofolio_id')->nullable()->after('bagian_id')->constrained('portofolio')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sampel', function (Blueprint $table) {
            $table->dropForeign(['portofolio_id']);
            $table->dropColumn('portofolio_id');
        });
    }
};
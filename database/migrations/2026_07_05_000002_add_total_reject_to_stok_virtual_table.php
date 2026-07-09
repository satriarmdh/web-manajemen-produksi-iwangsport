<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_virtual', function (Blueprint $table) {
            $table->integer('total_reject')->default(0)->after('total_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('stok_virtual', function (Blueprint $table) {
            $table->dropColumn('total_reject');
        });
    }
};

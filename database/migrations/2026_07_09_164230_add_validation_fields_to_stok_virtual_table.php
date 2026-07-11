<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stok_virtual', function (Blueprint $table) {
            $table->enum('status_validasi', ['normal', 'flag'])->default('normal')->after('is_selesai');
            $table->text('alasan')->nullable()->after('status_validasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_virtual', function (Blueprint $table) {
            $table->dropColumn(['status_validasi', 'alasan']);
        });
    }
};

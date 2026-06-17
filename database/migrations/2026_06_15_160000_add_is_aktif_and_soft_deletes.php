<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->boolean('is_aktif')->default(true)->after('stok');
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->boolean('is_aktif')->default(true)->after('stok');
        });

        Schema::table('standard_baseline_produksi', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });

        Schema::table('standard_baseline_produksi', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

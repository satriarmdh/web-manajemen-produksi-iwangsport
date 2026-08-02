<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->integer('stok_minimal')->nullable()->default(0)->after('stok');
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->integer('stok_minimal')->nullable()->default(0)->after('stok');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('stok_minimal');
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('stok_minimal');
        });
    }
};

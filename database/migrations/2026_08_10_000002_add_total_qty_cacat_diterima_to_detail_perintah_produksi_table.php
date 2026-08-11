<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_perintah_produksi', function (Blueprint $table) {
            $table->integer('total_qty_cacat_diterima')->default(0)->after('total_qty_diterima');
        });
    }

    public function down(): void
    {
        Schema::table('detail_perintah_produksi', function (Blueprint $table) {
            $table->dropColumn('total_qty_cacat_diterima');
        });
    }
};

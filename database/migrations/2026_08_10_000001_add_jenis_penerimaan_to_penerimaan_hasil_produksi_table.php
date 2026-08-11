<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penerimaan_hasil_produksi', function (Blueprint $table) {
            $table->enum('jenis_penerimaan', ['baik', 'cacat'])->default('baik')->after('qty_diterima');
        });
    }

    public function down(): void
    {
        Schema::table('penerimaan_hasil_produksi', function (Blueprint $table) {
            $table->dropColumn('jenis_penerimaan');
        });
    }
};

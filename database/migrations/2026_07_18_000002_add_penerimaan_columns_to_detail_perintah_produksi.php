<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_perintah_produksi', function (Blueprint $table) {
            $table->unsignedInteger('total_qty_diterima')
                ->default(0)
                ->after('estimasi_pcs')
                ->comment('Total qty yang sudah diterima admin dari finishing');
            
            $table->enum('status_penerimaan', [
                'belum_diterima',
                'sebagian',
                'sesuai',
                'selisih_kurang',
                'selisih_lebih'
            ])->default('belum_diterima')
                ->after('total_qty_diterima')
                ->comment('Status penerimaan hasil produksi dari finishing');
        });
    }

    public function down(): void
    {
        Schema::table('detail_perintah_produksi', function (Blueprint $table) {
            $table->dropColumn(['total_qty_diterima', 'status_penerimaan']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_hasil_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perintah_produksi_detail_id')
                ->constrained('detail_perintah_produksi')
                ->cascadeOnDelete();
            $table->foreignId('admin_user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('dari_karyawan_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('Karyawan finishing yang menyerahkan barang');
            $table->date('tanggal_terima');
            $table->integer('qty_diterima')->comment('Bisa negatif untuk reversal/koreksi');
            $table->text('catatan')->nullable();
            $table->string('bukti_foto')->comment('Mandatory photo evidence');
            $table->timestamps();

            $table->index(['perintah_produksi_detail_id', 'tanggal_terima'], 'idx_detail_tanggal');
            $table->index('dari_karyawan_id', 'idx_dari_karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_hasil_produksi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ajuan')->constrained('ajuan_pengambilan_produksi')->cascadeOnDelete();
            $table->foreignId('id_perintah')->constrained('perintah_produksi')->cascadeOnDelete();
            $table->foreignId('id_detail_perintah')->constrained('detail_perintah_produksi')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('dari_karyawan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ke_karyawan_id')->constrained('users')->cascadeOnDelete();
            $table->enum('dari_tahapan', ['potong', 'jahit', 'finishing']);
            $table->enum('ke_tahapan', ['potong', 'jahit', 'finishing']);
            $table->integer('qty_pindah');
            $table->dateTime('tgl_transaksi');
            $table->string('bukti_foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_produksi');
    }
};

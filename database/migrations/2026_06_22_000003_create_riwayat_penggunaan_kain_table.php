<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penggunaan_kain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perintah_produksi_id')->constrained('perintah_produksi')->onDelete('cascade');
            $table->foreignId('detail_perintah_produksi_id')->constrained('detail_perintah_produksi')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->decimal('jumlah_pakai', 8, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penggunaan_kain');
    }
};

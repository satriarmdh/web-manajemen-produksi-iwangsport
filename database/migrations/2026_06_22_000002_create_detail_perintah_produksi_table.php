<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_perintah_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perintah_produksi_id')->constrained('perintah_produksi')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->integer('qty_roll_pakai');
            $table->integer('estimasi_pcs');
            $table->integer('toleransi_minus');
            $table->integer('qty_pcs_potong')->nullable();
            $table->enum('status_validasi_potong', ['pending', 'normal', 'flag'])->default('pending');
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_perintah_produksi');
    }
};

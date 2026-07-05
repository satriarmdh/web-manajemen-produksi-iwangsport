<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_virtual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perintah')->constrained('perintah_produksi')->onDelete('cascade');
            $table->foreignId('id_detail_perintah')->constrained('detail_perintah_produksi')->onDelete('cascade');
            $table->foreignId('id_karyawan')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->enum('peran', ['potong', 'jahit', 'finishing']);
            $table->integer('qty_hold')->default(0);
            $table->integer('total_selesai')->default(0);
            $table->enum('status_barang', ['Proses', 'Ready'])->default('Proses');
            $table->timestamps();

            $table->unique(['id_detail_perintah', 'id_karyawan', 'peran'], 'stok_virtual_unique_holder');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_virtual');
    }
};

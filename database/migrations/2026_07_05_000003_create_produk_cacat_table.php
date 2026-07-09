<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_cacat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perintah')->constrained('perintah_produksi')->onDelete('cascade');
            $table->foreignId('id_detail_perintah')->constrained('detail_perintah_produksi')->onDelete('cascade');
            $table->foreignId('id_karyawan')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->enum('tahapan', ['potong', 'jahit', 'finishing']);
            $table->integer('qty_reject');
            $table->text('keterangan');
            $table->dateTime('tgl_lapor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_cacat');
    }
};

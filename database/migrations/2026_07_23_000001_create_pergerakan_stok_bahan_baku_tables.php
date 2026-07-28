<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel pergerakan stok 1-item yang lama
        Schema::dropIfExists('stok_masuk_bahan_baku');
        Schema::dropIfExists('stok_keluar_bahan_baku');

        Schema::create('pergerakan_stok_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->enum('jenis_pergerakan', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('penerima')->nullable();
            $table->string('bukti')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('detail_pergerakan_stok_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pergerakan_stok_bahan_baku_id')->constrained('pergerakan_stok_bahan_baku', 'id', 'fk_detail_parent')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
            $table->unsignedInteger('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pergerakan_stok_bahan_baku');
        Schema::dropIfExists('pergerakan_stok_bahan_baku');
    }
};

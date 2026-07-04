<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perintah_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_wo')->unique();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->enum('status_produksi', ['pending', 'disetujui', 'dalam_produksi', 'selesai', 'ditolak'])->default('pending');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perintah_produksi');
    }
};

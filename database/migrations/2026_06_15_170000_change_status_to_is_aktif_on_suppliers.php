<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Tambahkan kolom is_aktif baru (boolean, default true)
            $table->boolean('is_aktif')->default(true)->after('catatan');
        });

        // Migrasi data: konversi status lama ke is_aktif baru
        DB::table('suppliers')->where('status', 'aktif')->update(['is_aktif' => true]);
        DB::table('suppliers')->where('status', 'nonaktif')->update(['is_aktif' => false]);

        // Hapus kolom status lama
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('catatan');
        });

        // Migrasi data balik
        DB::table('suppliers')->where('is_aktif', true)->update(['status' => 'aktif']);
        DB::table('suppliers')->where('is_aktif', false)->update(['status' => 'nonaktif']);

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });
    }
};

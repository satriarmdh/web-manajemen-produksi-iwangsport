<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename tabel stock_movements menjadi riwayat_stok
        if (Schema::hasTable('stock_movements') && !Schema::hasTable('riwayat_stok')) {
            Schema::rename('stock_movements', 'riwayat_stok');
        }

        // 2. Rename kolom di tabel riwayat_stok
        if (Schema::hasTable('riwayat_stok')) {
            Schema::table('riwayat_stok', function (Blueprint $table) {
                // Rename kolom jika masih menggunakan nama lama
                if (Schema::hasColumn('riwayat_stok', 'item_type')) {
                    $table->renameColumn('item_type', 'jenis_item');
                }
                if (Schema::hasColumn('riwayat_stok', 'item_id')) {
                    $table->renameColumn('item_id', 'id_item');
                }
                if (Schema::hasColumn('riwayat_stok', 'movement_type')) {
                    $table->renameColumn('movement_type', 'jenis_pergerakan');
                }
                if (Schema::hasColumn('riwayat_stok', 'quantity')) {
                    $table->renameColumn('quantity', 'jumlah');
                }
                if (Schema::hasColumn('riwayat_stok', 'previous_stock')) {
                    $table->renameColumn('previous_stock', 'stok_sebelum');
                }
                if (Schema::hasColumn('riwayat_stok', 'new_stock')) {
                    $table->renameColumn('new_stock', 'stok_sesudah');
                }
                if (Schema::hasColumn('riwayat_stok', 'reason')) {
                    $table->renameColumn('reason', 'keterangan');
                }
                
                // Tambahkan kolom referensi untuk polymorphic relation ke transaksi
                if (!Schema::hasColumn('riwayat_stok', 'referensi_type')) {
                    $table->string('referensi_type')->nullable()->after('keterangan');
                }
                if (!Schema::hasColumn('riwayat_stok', 'referensi_id')) {
                    $table->unsignedBigInteger('referensi_id')->nullable()->after('referensi_type');
                }
            });
        }

        // 3. Buat tabel stok_masuk_bahan_baku
        if (!Schema::hasTable('stok_masuk_bahan_baku')) {
            Schema::create('stok_masuk_bahan_baku', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
                $table->unsignedInteger('jumlah');
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
                $table->string('bukti_pembelian')->nullable(); // path file invoice/bukti
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('catatan')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Buat tabel stok_keluar_bahan_baku
        if (!Schema::hasTable('stok_keluar_bahan_baku')) {
            Schema::create('stok_keluar_bahan_baku', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');
                $table->unsignedInteger('jumlah');
                $table->string('penerima'); // nama karyawan yang menerima
                $table->string('bukti_pengeluaran')->nullable(); // path file bukti penyerahan
                $table->text('keterangan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // admin yang input
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // Drop tabel baru
        Schema::dropIfExists('stok_keluar_bahan_baku');
        Schema::dropIfExists('stok_masuk_bahan_baku');

        // Kembalikan nama kolom dan tabel riwayat_stok ke stock_movements
        if (Schema::hasTable('riwayat_stok')) {
            Schema::table('riwayat_stok', function (Blueprint $table) {
                if (Schema::hasColumn('riwayat_stok', 'referensi_id')) {
                    $table->dropColumn('referensi_id');
                }
                if (Schema::hasColumn('riwayat_stok', 'referensi_type')) {
                    $table->dropColumn('referensi_type');
                }
                
                // Kembalikan nama kolom ke bahasa Inggris
                if (Schema::hasColumn('riwayat_stok', 'keterangan')) {
                    $table->renameColumn('keterangan', 'reason');
                }
                if (Schema::hasColumn('riwayat_stok', 'stok_sesudah')) {
                    $table->renameColumn('stok_sesudah', 'new_stock');
                }
                if (Schema::hasColumn('riwayat_stok', 'stok_sebelum')) {
                    $table->renameColumn('stok_sebelum', 'previous_stock');
                }
                if (Schema::hasColumn('riwayat_stok', 'jumlah')) {
                    $table->renameColumn('jumlah', 'quantity');
                }
                if (Schema::hasColumn('riwayat_stok', 'jenis_pergerakan')) {
                    $table->renameColumn('jenis_pergerakan', 'movement_type');
                }
                if (Schema::hasColumn('riwayat_stok', 'id_item')) {
                    $table->renameColumn('id_item', 'item_id');
                }
                if (Schema::hasColumn('riwayat_stok', 'jenis_item')) {
                    $table->renameColumn('jenis_item', 'item_type');
                }
            });
            
            Schema::rename('riwayat_stok', 'stock_movements');
        }
    }
};

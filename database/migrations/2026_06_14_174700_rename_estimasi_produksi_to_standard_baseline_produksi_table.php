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
        Schema::rename('estimasi_produksi', 'standard_baseline_produksi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('standard_baseline_produksi', 'estimasi_produksi');
    }
};

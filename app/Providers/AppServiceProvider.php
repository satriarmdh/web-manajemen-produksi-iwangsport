<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Observers\ProdukObserver;
use App\Observers\BahanBakuObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        
        // Daftarkan observer untuk mencatat perubahan stok
        Produk::observe(ProdukObserver::class);
        BahanBaku::observe(BahanBakuObserver::class);

        // Daftarkan morphMap untuk polymorphic relations
        \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap([
            'bahan_baku' => \App\Models\BahanBaku::class,
            'produk' => \App\Models\Produk::class,
        ]);
    }
}

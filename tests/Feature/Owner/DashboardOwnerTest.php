<?php

namespace Tests\Feature\Owner;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\User;
use App\Models\PerintahProduksi;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOwnerTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawan = User::factory()->create(['role' => 'potong']);
    }

    /** @test */
    public function hanya_owner_yang_dapat_mengakses_dashboard_dan_laporan_inventori()
    {
        // Akses Dashboard Utama
        $this->actingAs($this->owner)->get(route('owner.dashboard'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('owner.dashboard'))->assertStatus(403);

        // Akses Halaman Laporan Inventori
        $this->actingAs($this->owner)->get(route('owner.inventori'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('owner.inventori'))->assertStatus(403);
    }

    /** @test */
    public function dashboard_utama_menampilkan_executive_summary_bisnis_secara_akurat()
    {
        // 1. Setup Perintah Produksi
        PerintahProduksi::factory()->create(['status_produksi' => 'pending']);
        PerintahProduksi::factory()->create(['status_produksi' => 'pending']);
        PerintahProduksi::factory()->create(['status_produksi' => 'disetujui']);

        // 2. Setup Staff
        User::factory()->create(['role' => 'potong']);
        User::factory()->create(['role' => 'jahit']);

        // 3. Setup Stok
        BahanBaku::factory()->create(['stok' => 150]);
        Produk::factory()->create(['stok' => 300]);

        // 4. Setup Penjualan untuk Top 5 Produk
        $produk1 = Produk::factory()->create(['nama_produk' => 'Celana A', 'stok' => 10]);
        $produk2 = Produk::factory()->create(['nama_produk' => 'Celana B', 'stok' => 20]);
        $pelanggan = \App\Models\Pelanggan::factory()->create();
        
        $penjualan = Penjualan::create([
            'nomor_invoice' => 'INV-001',
            'tanggal' => now(),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->admin->id,
            'total_harga' => 100000,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'produk_id' => $produk1->id,
            'qty' => 10,
            'harga_satuan' => 10000,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['wo_pending_count'] == 2
                && $stats['total_staff_count'] == \App\Models\User::whereIn('role', ['admin', 'potong', 'jahit', 'finishing'])->count()
                && $stats['total_bahan_count'] == 1
                && $stats['total_produk_count'] == 3;
        });

        $response->assertViewHas('salesTrend', function ($salesTrend) {
            return is_array($salesTrend)
                && array_key_exists('labels', $salesTrend)
                && array_key_exists('values', $salesTrend)
                && array_key_exists('total', $salesTrend)
                && $salesTrend['total'] === 10;
        });

        $response->assertViewHas('recentActivity', function ($recentActivity) {
            return isset($recentActivity['perintahProduksi']) && $recentActivity['perintahProduksi']->count() > 0;
        });

        $wo = PerintahProduksi::first();
        if ($wo) {
            $response->assertSee($wo->nomor_wo);
        }
    }

    /** @test */
    public function endpoint_sales_trend_mengembalikan_agregasi_penjualan_sesuai_periode()
    {
        $produk = Produk::factory()->create(['stok' => 50]);
        $pelanggan = \App\Models\Pelanggan::factory()->create();

        $penjualan = Penjualan::create([
            'nomor_invoice' => 'INV-TREND',
            'tanggal' => now(),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->admin->id,
            'total_harga' => 50000,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'produk_id' => $produk->id,
            'qty' => 7,
            'harga_satuan' => 10000,
            'subtotal' => 70000,
        ]);

        // Owner dapat mengakses & menerima agregasi benar (preset 7 hari).
        $response = $this->actingAs($this->owner)
            ->getJson(route('owner.dashboard.sales-trend', ['range' => '7d']));

        $response->assertStatus(200)
            ->assertJsonStructure(['labels', 'values', 'total', 'granularity'])
            ->assertJson(['total' => 7]);

        // Non-owner ditolak.
        $this->actingAs($this->admin)
            ->getJson(route('owner.dashboard.sales-trend'))
            ->assertStatus(403);
    }

    /** @test */
    public function laporan_inventori_menampilkan_data_statistik_inventori_real_time_secara_akurat()
    {
        // 1. Setup Bahan Baku
        BahanBaku::factory()->create(['stok' => 50]); // normal
        BahanBaku::factory()->create(['stok' => 8]); // menipis (<10)
        BahanBaku::factory()->create(['stok' => 0]); // habis/menipis (<10)

        // 2. Setup Produk
        Produk::factory()->create(['stok' => 200]); // normal
        Produk::factory()->create(['stok' => 80]); // menipis (<100)
        Produk::factory()->create(['stok' => 0]); // habis/menipis (<100)

        $response = $this->actingAs($this->owner)
            ->get(route('owner.inventori'));

        $response->assertStatus(200);
        
        $response->assertViewHas('stats', function ($stats) {
            return $stats['bahan_menipis_count'] == 1
                && $stats['produk_menipis_count'] == 1
                && $stats['bahan_habis_count'] == 1
                && $stats['produk_habis_count'] == 1;
        });

        $response->assertViewHas('bahanBaku');
        $response->assertViewHas('produk');

        // Pastikan ketika filter ?stok=menipis aktif, data terfilter
        $filteredResponse = $this->actingAs($this->owner)
            ->get(route('owner.inventori', ['stok' => 'menipis']));
        
        $filteredResponse->assertStatus(200);
        $filteredBahan = $filteredResponse->original->getData()['bahanBaku'];
        $filteredProduk = $filteredResponse->original->getData()['produk'];

        $this->assertEquals(2, $filteredBahan->count()); // stok 8 dan 0
        $this->assertEquals(2, $filteredProduk->count()); // stok 80 dan 0
    }

    /** @test */
    public function laporan_inventori_menampilkan_riwayat_mutasi_stok_dengan_pagination()
    {
        $bahan = BahanBaku::factory()->create(['nama_bahan' => 'Kain Cotton', 'stok' => 100]);
        $produk = Produk::factory()->create(['nama_produk' => 'Celana Chino', 'stok' => 100]);

        // Buat 15 riwayat stok
        for ($i = 1; $i <= 8; $i++) {
            RiwayatStok::create([
                'jenis_item' => 'bahan_baku',
                'id_item' => $bahan->id,
                'stok_sebelum' => 10,
                'stok_sesudah' => 15,
                'jumlah' => 5,
                'jenis_pergerakan' => 'masuk',
                'keterangan' => "Mutasi bahan ke-$i",
            ]);
        }

        for ($i = 1; $i <= 7; $i++) {
            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'stok_sebelum' => 20,
                'stok_sesudah' => 10,
                'jumlah' => 10,
                'jenis_pergerakan' => 'keluar',
                'keterangan' => "Mutasi produk ke-$i",
            ]);
        }

        $response = $this->actingAs($this->owner)
            ->get(route('owner.inventori'));

        $response->assertStatus(200);
        $response->assertViewHas('mutasiStok');
        
        $mutasi = $response->original->getData()['mutasiStok'];
        $this->assertEquals(10, $mutasi->perPage());
        $this->assertEquals(17, $mutasi->total());
    }

    /** @test */
    public function laporan_inventori_dapat_memfilter_riwayat_mutasi_stok_secara_server_side()
    {
        $bahan = BahanBaku::factory()->create(['nama_bahan' => 'Kain Cotton']);
        $produk = Produk::factory()->create(['nama_produk' => 'Celana Chino']);

        // Buat mutasi bahan baku
        RiwayatStok::create([
            'jenis_item' => 'bahan_baku',
            'id_item' => $bahan->id,
            'stok_sebelum' => 10,
            'stok_sesudah' => 15,
            'jumlah' => 5,
            'jenis_pergerakan' => 'masuk',
            'keterangan' => 'Mutasi bahan masuk',
        ]);

        // Buat mutasi produk
        RiwayatStok::create([
            'jenis_item' => 'produk',
            'id_item' => $produk->id,
            'stok_sebelum' => 20,
            'stok_sesudah' => 10,
            'jumlah' => 10,
            'jenis_pergerakan' => 'keluar',
            'keterangan' => 'Mutasi produk keluar',
        ]);

        // Filter tipe bahan baku
        $responseBahan = $this->actingAs($this->owner)
            ->get(route('owner.inventori', ['jenis_item' => 'bahan_baku']));
        $responseBahan->assertStatus(200);
        $mutasiBahan = $responseBahan->original->getData()['mutasiStok'];
        
        // Pastikan hanya jenis_item = bahan_baku yang terpilih
        foreach ($mutasiBahan as $m) {
            $this->assertEquals('bahan_baku', $m->jenis_item);
        }

        // Filter tipe produk
        $responseProduk = $this->actingAs($this->owner)
            ->get(route('owner.inventori', ['jenis_item' => 'produk']));
        $responseProduk->assertStatus(200);
        $mutasiProduk = $responseProduk->original->getData()['mutasiStok'];
        
        // Pastikan hanya jenis_item = produk yang terpilih
        foreach ($mutasiProduk as $m) {
            $this->assertEquals('produk', $m->jenis_item);
        }
    }
}

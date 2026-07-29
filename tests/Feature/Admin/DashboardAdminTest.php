<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\PerintahProduksi;
use App\Models\PergerakanStokBahanBaku;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $owner;
    protected User $karyawanJahit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
    }

    /**
     * Test admin dapat mengakses halaman dashboard admin
     */
    public function test_admin_dapat_mengakses_dashboard_admin()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard')
            ->assertSee('Dashboard Admin')
            ->assertSee('Perintah Produksi Aktif')
            ->assertSee('Kritis / Stok Menipis')
            ->assertSee('Supplier & Pelanggan')
            ->assertSee('Transaksi Hari Ini');
    }

    /**
     * Test non-admin tidak dapat mengakses halaman dashboard admin
     */
    public function test_non_admin_tidak_dapat_mengakses_dashboard_admin()
    {
        $response = $this->actingAs($this->owner)->get('/admin/dashboard');
        $response->assertStatus(403);

        $response2 = $this->actingAs($this->karyawanJahit)->get('/admin/dashboard');
        $response2->assertStatus(403);
    }

    /**
     * Test data statistik dan daftar data terbaru dimuat dengan benar
     */
    public function test_dashboard_memuat_data_statistik_dan_aktivitas_terbaru()
    {
        // 1. Buat WO Aktif
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'WO-AKTIF-1']);
        PerintahProduksi::factory()->dalamProduksi()->create(['nomor_wo' => 'WO-AKTIF-2']);
        PerintahProduksi::factory()->pending()->create(['nomor_wo' => 'WO-PENDING']); // tidak dihitung aktif

        // 2. Buat stok menipis
        BahanBaku::factory()->create(['stok' => 5]); // menipis (<10)
        BahanBaku::factory()->create(['stok' => 0]); // habis / menipis (<10)
        Produk::factory()->create(['stok' => 50]); // menipis (<100)

        // 3. Buat supplier dan pelanggan
        Supplier::factory()->create();
        $pelanggan = Pelanggan::factory()->create();

        // 4. Buat transaksi hari ini
        PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-STOK-TODAY',
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now(),
            'user_id' => $this->admin->id
        ]);
        Penjualan::factory()->create([
            'nomor_invoice' => 'INV-TODAY',
            'tanggal' => now(),
            'pelanggan_id' => $pelanggan->id
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        // Uji asersi data stat
        $response->assertViewHas('stats', function ($stats) {
            return $stats['active_wo'] === 2 
                && $stats['low_stock'] === 3
                && $stats['partners'] === 2
                && $stats['today_transactions'] === 2;
        });

        // Uji asersi daftar terbaru terlihat di HTML
        $response->assertSee('WO-AKTIF-1')
            ->assertSee('WO-AKTIF-2')
            ->assertSee('TRX-STOK-TODAY')
            ->assertSee('INV-TODAY');
    }
}

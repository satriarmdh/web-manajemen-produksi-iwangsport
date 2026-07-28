<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\StandardBaselineProduksi;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $nonAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->nonAdmin = User::factory()->create(['role' => 'potong']);
    }

    private function createProdukWithStok(int $stok = 50, int $harga = 50000): Produk
    {
        return Produk::factory()->create([
            'stok' => $stok,
            'harga_satuan' => $harga,
            'is_aktif' => true,
        ]);
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_admin_dapat_mengakses_halaman_penjualan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/penjualan');

        $response->assertStatus(200)
            ->assertViewIs('admin.penjualan.index');
    }

    public function test_admin_dapat_mengakses_halaman_buat_penjualan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/penjualan/create');

        $response->assertStatus(200)
            ->assertViewIs('admin.penjualan.create');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_penjualan()
    {
        $response = $this->actingAs($this->nonAdmin)->get('/admin/penjualan');

        $response->assertStatus(403);
    }

    public function test_owner_tidak_dapat_mengakses_halaman_penjualan()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get('/admin/penjualan');

        $response->assertStatus(403);
    }

    // ============================================
    // VALIDATION
    // ============================================

    public function test_validasi_wajib_pelanggan_tanggal_dan_items()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', []);

        $response->assertSessionHasErrors([
            'pelanggan_id',
            'tanggal',
            'items',
        ]);
    }

    public function test_validasi_items_harus_array_dengan_produk_dan_qty()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    [
                        // missing produk_id and qty
                    ],
                ],
            ]);

        $response->assertSessionHasErrors([
            'items.0.produk_id',
            'items.0.qty',
        ]);
    }

    public function test_validasi_qty_minimal_1()
    {
        $produk = $this->createProdukWithStok(50);
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    [
                        'produk_id' => $produk->id,
                        'qty' => 0,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.qty']);
    }

    public function test_validasi_qty_tidak_boleh_melebihi_stok()
    {
        $produk = $this->createProdukWithStok(10);
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    [
                        'produk_id' => $produk->id,
                        'qty' => 15,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.qty']);
    }

    // ============================================
    // HAPPY PATH
    // ============================================

    public function test_admin_dapat_membuat_transaksi_penjualan()
    {
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'catatan' => 'Penjualan test',
                'items' => [
                    [
                        'produk_id' => $produk->id,
                        'qty' => 10,
                    ],
                ],
            ]);

        $response->assertRedirect('/admin/penjualan');
        $response->assertSessionHas('success');

        $penjualan = Penjualan::first();
        $this->assertNotNull($penjualan);
        $this->assertEquals($pelanggan->id, $penjualan->pelanggan_id);
        $this->assertEquals(10, $penjualan->total_item);
        $this->assertEquals(500000, $penjualan->total_harga); // 10 * 50000
    }

    public function test_stok_produk_berkurang_setelah_penjualan()
    {
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 15],
                ],
            ]);

        $this->assertEquals(35, $produk->fresh()->stok);
    }

    public function test_riwayat_stok_tercatat_sebagai_keluar()
    {
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 10],
                ],
            ]);

        $riwayat = RiwayatStok::where('jenis_item', 'produk')
            ->where('id_item', $produk->id)
            ->where('jenis_pergerakan', 'keluar')
            ->first();

        $this->assertNotNull($riwayat);
        $this->assertEquals(10, $riwayat->jumlah);
        $this->assertEquals(50, $riwayat->stok_sebelum);
        $this->assertEquals(40, $riwayat->stok_sesudah);

        // Pastikan tidak ada duplikasi riwayat dengan jenis_pergerakan 'penyesuaian'
        $penyesuaianCount = RiwayatStok::where('jenis_item', 'produk')
            ->where('id_item', $produk->id)
            ->where('jenis_pergerakan', 'penyesuaian')
            ->count();
        $this->assertEquals(0, $penyesuaianCount);
    }

    public function test_detail_penjualan_tercatat_dengan_harga_dan_subtotal()
    {
        $produk = $this->createProdukWithStok(50, 75000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 5],
                ],
            ]);

        $detail = DetailPenjualan::first();
        $this->assertNotNull($detail);
        $this->assertEquals(75000, $detail->harga_satuan);
        $this->assertEquals(375000, $detail->subtotal); // 5 * 75000
    }

    public function test_multiple_items_dalam_satu_transaksi()
    {
        $produk1 = $this->createProdukWithStok(30, 50000);
        $produk2 = $this->createProdukWithStok(20, 80000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk1->id, 'qty' => 10],
                    ['produk_id' => $produk2->id, 'qty' => 5],
                ],
            ]);

        $penjualan = Penjualan::first();
        $this->assertEquals(15, $penjualan->total_item); // 10 + 5
        $this->assertEquals(900000, $penjualan->total_harga); // (10*50000) + (5*80000)

        $this->assertEquals(20, $produk1->fresh()->stok); // 30 - 10
        $this->assertEquals(15, $produk2->fresh()->stok); // 20 - 5
    }

    public function test_nomor_invoice_tergenerate_otomatis()
    {
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 5],
                ],
            ]);

        $penjualan = Penjualan::first();
        $this->assertNotNull($penjualan->nomor_invoice);
        $this->assertStringStartsWith('INV-', $penjualan->nomor_invoice);
        $this->assertStringContainsString(today()->format('Ymd'), $penjualan->nomor_invoice);
    }

    // ============================================
    // EDGE CASES
    // ============================================

    public function test_stok_nol_tidak_bisa_dijual()
    {
        $produk = $this->createProdukWithStok(0);
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 1],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.qty']);
    }

    public function test_produk_non_aktif_tidak_bisa_dijual()
    {
        $produk = Produk::factory()->create([
            'stok' => 50,
            'harga_satuan' => 50000,
            'is_aktif' => false,
        ]);
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 5],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.produk_id']);
    }

    public function test_pelanggan_non_aktif_tidak_bisa_dipilih()
    {
        $produk = $this->createProdukWithStok(50);
        $pelanggan = Pelanggan::factory()->create(['is_aktif' => false]);

        $response = $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 5],
                ],
            ]);

        $response->assertSessionHasErrors(['pelanggan_id']);
    }

    // ============================================
    // SHOW
    // ============================================

    public function test_admin_dapat_melihat_detail_penjualan()
    {
        $penjualan = Penjualan::factory()->create([
            'pelanggan_id' => Pelanggan::factory()->create()->id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/penjualan/{$penjualan->id}");

        $response->assertStatus(200)
            ->assertViewIs('admin.penjualan.show');
    }

    // ============================================
    // EDIT & UPDATE
    // ============================================

    public function test_admin_dapat_mengakses_halaman_edit_penjualan()
    {
        $penjualan = Penjualan::factory()->create([
            'pelanggan_id' => Pelanggan::factory()->create()->id,
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/penjualan/{$penjualan->id}/edit");

        $response->assertStatus(200)
            ->assertViewIs('admin.penjualan.edit');
    }

    public function test_admin_dapat_mengedit_penjualan_dan_stok_disesuaikan()
    {
        // Create initial penjualan
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 10],
                ],
            ]);

        // Stok sekarang: 50 - 10 = 40
        $this->assertEquals(40, $produk->fresh()->stok);
        $penjualan = Penjualan::first();

        // Edit: ubah qty dari 10 → 5
        $response = $this->actingAs($this->admin)
            ->put("/admin/penjualan/{$penjualan->id}", [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 5],
                ],
            ]);

        $response->assertRedirect('/admin/penjualan');
        $response->assertSessionHas('success');

        // Stok harusnya: 50 - 5 = 45 (kembalikan 10, kurang 5)
        $this->assertEquals(45, $produk->fresh()->stok);
        $this->assertEquals(5, $penjualan->fresh()->total_item);
        $this->assertEquals(250000, $penjualan->fresh()->total_harga); // 5 * 50000
    }

    // ============================================
    // DELETE
    // ============================================

    public function test_admin_dapat_menghapus_penjualan_dan_stok_dikembalikan()
    {
        $produk = $this->createProdukWithStok(50, 50000);
        $pelanggan = Pelanggan::factory()->create();

        $this->actingAs($this->admin)
            ->post('/admin/penjualan', [
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => today()->format('Y-m-d'),
                'items' => [
                    ['produk_id' => $produk->id, 'qty' => 10],
                ],
            ]);

        $penjualan = Penjualan::first();
        $this->assertEquals(40, $produk->fresh()->stok);

        $response = $this->actingAs($this->admin)
            ->delete("/admin/penjualan/{$penjualan->id}");

        $response->assertRedirect('/admin/penjualan');
        $response->assertSessionHas('success');

        // Stok dikembalikan: 40 + 10 = 50
        $this->assertEquals(50, $produk->fresh()->stok);
        $this->assertSoftDeleted($penjualan);
    }

    public function test_admin_dapat_mencari_dan_memfilter_transaksi_penjualan()
    {
        $produk = $this->createProdukWithStok(50);
        $pelanggan1 = Pelanggan::factory()->create(['nama_pelanggan' => 'Alice']);
        $pelanggan2 = Pelanggan::factory()->create(['nama_pelanggan' => 'Bob']);

        // Create transaction 1 (Alice)
        $penjualan1 = Penjualan::factory()->create([
            'pelanggan_id' => $pelanggan1->id,
            'user_id' => $this->admin->id,
            'tanggal' => '2026-07-01',
            'nomor_invoice' => 'INV-20260701-0001',
        ]);
        DetailPenjualan::create([
            'penjualan_id' => $penjualan1->id,
            'produk_id' => $produk->id,
            'qty' => 5,
            'harga_satuan' => 50000,
            'subtotal' => 250000,
        ]);

        // Create transaction 2 (Bob)
        $penjualan2 = Penjualan::factory()->create([
            'pelanggan_id' => $pelanggan2->id,
            'user_id' => $this->admin->id,
            'tanggal' => '2026-07-15',
            'nomor_invoice' => 'INV-20260715-0002',
        ]);
        DetailPenjualan::create([
            'penjualan_id' => $penjualan2->id,
            'produk_id' => $produk->id,
            'qty' => 5,
            'harga_satuan' => 50000,
            'subtotal' => 250000,
        ]);

        // Search for Alice
        $response = $this->actingAs($this->admin)
            ->get('/admin/penjualan?search=Alice');
        $response->assertSee($penjualan1->nomor_invoice);
        $response->assertDontSee($penjualan2->nomor_invoice);

        // Filter date range
        $response = $this->actingAs($this->admin)
            ->get('/admin/penjualan?tanggal_mulai=2026-07-10&tanggal_akhir=2026-07-20');
        $response->assertDontSee($penjualan1->nomor_invoice);
        $response->assertSee($penjualan2->nomor_invoice);
    }
}

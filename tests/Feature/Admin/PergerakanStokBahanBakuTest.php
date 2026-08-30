<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\RiwayatStok;
use App\Models\PergerakanStokBahanBaku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PergerakanStokBahanBakuTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $karyawanJahit;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
    }

    // ============================================
    // VIEW & ACCESS
    // ============================================

    public function test_admin_dapat_melihat_halaman_stok_masuk()
    {
        $response = $this->actingAs($this->admin)->get('/admin/pergerakan-stok?tab=masuk');

        $response->assertStatus(200);
        $response->assertViewIs('admin.pergerakan-stok.index');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_stok_masuk()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/pergerakan-stok?tab=masuk');

        $response->assertStatus(403);
    }

    // ============================================
    // CREATE BULK TRANSACTION (MASUK)
    // ============================================

    public function test_admin_dapat_membuat_transaksi_stok_masuk_dengan_data_valid()
    {
        $bahan1 = BahanBaku::factory()->create([
            'nama_bahan' => 'Benang Polyester',
            'stok' => 10,
            'is_aktif' => true
        ]);
        $bahan2 = BahanBaku::factory()->create([
            'nama_bahan' => 'Kancing Plastik',
            'stok' => 5,
            'is_aktif' => true
        ]);

        $data = [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => null,
            'catatan' => 'Pembelian bulk',
            'items' => [
                ['bahan_baku_id' => $bahan1->id, 'quantity' => 5],
                ['bahan_baku_id' => $bahan2->id, 'quantity' => 10],
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=masuk');
        $response->assertSessionHas('success');

        // Stok harus bertambah
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan1->id,
            'stok' => 15
        ]);
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan2->id,
            'stok' => 15
        ]);

        // DetailPergerakanStok harus tersimpan
        $this->assertDatabaseHas('detail_pergerakan_stok_bahan_baku', [
            'bahan_baku_id' => $bahan1->id,
            'jumlah' => 5,
        ]);
        $this->assertDatabaseHas('detail_pergerakan_stok_bahan_baku', [
            'bahan_baku_id' => $bahan2->id,
            'jumlah' => 10,
        ]);

        // RiwayatStok harus tercatat
        $this->assertDatabaseHas('riwayat_stok', [
            'jenis_item' => 'bahan_baku',
            'id_item' => $bahan1->id,
            'jenis_pergerakan' => 'masuk',
            'jumlah' => 5,
            'stok_sebelum' => 10,
            'stok_sesudah' => 15,
        ]);
    }

    public function test_admin_dapat_membuat_transaksi_stok_masuk_dengan_supplier()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 0, 'is_aktif' => true]);
        $supplier = Supplier::factory()->create(['nama_supplier' => 'PT Tekstil Jaya']);

        $data = [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $supplier->id,
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 10]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=masuk');
        $this->assertDatabaseHas('pergerakan_stok_bahan_baku', [
            'supplier_id' => $supplier->id
        ]);
    }

    // ============================================
    // CREATE BULK TRANSACTION (KELUAR)
    // ============================================

    public function test_admin_dapat_membuat_transaksi_stok_keluar_dengan_data_valid()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Kancing Polyester',
            'kategori' => 'kancing',
            'stok' => 50,
            'is_aktif' => true
        ]);

        $data = [
            'jenis_pergerakan' => 'keluar',
            'tanggal' => now()->format('Y-m-d'),
            'penerima' => 'Budi Santoso',
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 20]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=keluar');
        
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'stok' => 30
        ]);
    }

    public function test_stok_keluar_hanya_boleh_untuk_bahan_non_kain()
    {
        $bahanKain = BahanBaku::factory()->create([
            'nama_bahan' => 'Kain Cotton',
            'kategori' => 'kain',
            'stok' => 10,
            'is_aktif' => true
        ]);

        $data = [
            'jenis_pergerakan' => 'keluar',
            'tanggal' => now()->format('Y-m-d'),
            'penerima' => 'Budi',
            'items' => [
                ['bahan_baku_id' => $bahanKain->id, 'quantity' => 5]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertSessionHasErrors('items.0.bahan_baku_id');
    }

    public function test_stok_keluar_tidak_boleh_lebih_dari_stok_tersedia()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Benang Jahit',
            'kategori' => 'bahan_pendukung',
            'stok' => 5,
            'is_aktif' => true
        ]);

        $data = [
            'jenis_pergerakan' => 'keluar',
            'tanggal' => now()->format('Y-m-d'),
            'penerima' => 'Budi',
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 10]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertSessionHasErrors('items.0.quantity');
    }

    // ============================================
    // VALIDASI BULK
    // ============================================

    public function test_pergerakan_stok_wajib_memiliki_minimal_satu_item()
    {
        $data = [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'items' => []
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertSessionHasErrors('items');
    }

    public function test_pergerakan_stok_menolak_quantity_kurang_dari_satu()
    {
        $bahan = BahanBaku::factory()->create(['is_aktif' => true]);

        $data = [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 0]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pergerakan-stok', $data);

        $response->assertSessionHasErrors('items.0.quantity');
    }

    // ============================================
    // DETROY & CANCEL
    // ============================================

    public function test_admin_dapat_membatalkan_transaksi_stok_masuk()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 20, 'is_aktif' => true]);

        // Buat transaksi masuk bulk
        $transaksi = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-BM-TEST',
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now(),
            'user_id' => $this->admin->id
        ]);
        $detail = $transaksi->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 10
        ]);

        // Update manual stok bahan baku untuk mencerminkan transaksi masuk (karena di database factory)
        $bahan->stok = 30;
        $bahan->save();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/pergerakan-stok/{$transaksi->id}");

        $response->assertRedirect('/admin/pergerakan-stok?tab=masuk');
        $response->assertSessionHas('success');

        // Stok harus dikurangi kembali ke 20
        $bahan->refresh();
        $this->assertEquals(20, $bahan->stok);

        // Header dan detail soft-deleted atau deleted
        $this->assertSoftDeleted('pergerakan_stok_bahan_baku', ['id' => $transaksi->id]);
    }

    // ============================================
    // FILTER TANGGAL (MAINTAINED)
    // ============================================

    public function test_filter_rentang_tanggal_stok_masuk_dan_validasinya()
    {
        $bahan = BahanBaku::factory()->create(['is_aktif' => true]);

        // 1. Buat transaksi dengan tanggal kemarin
        $masukKemarin = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-BM-KEMARIN',
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->subDay(),
            'user_id' => $this->admin->id,
        ]);
        $masukKemarin->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 10
        ]);
        \DB::table('pergerakan_stok_bahan_baku')->where('id', $masukKemarin->id)->update([
            'created_at' => now()->subDay()
        ]);

        // 2. Buat transaksi dengan tanggal hari ini
        $masukHariIni = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-BM-HARIINI',
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now(),
            'user_id' => $this->admin->id,
        ]);
        $masukHariIni->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 5
        ]);

        // Akses filter rentang tanggal hari ini saja
        $response = $this->actingAs($this->admin)
            ->get('/admin/pergerakan-stok?tab=masuk&tanggal_mulai_masuk=' . now()->format('Y-m-d') . '&tanggal_akhir_masuk=' . now()->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertViewHas('stokMasuk', function ($paginator) use ($masukHariIni, $masukKemarin) {
            $items = $paginator->items();
            return collect($items)->contains('id', $masukHariIni->id) && !collect($items)->contains('id', $masukKemarin->id);
        });

        // Akses filter rentang tanggal tidak valid (tanggal akhir < tanggal awal)
        $response2 = $this->actingAs($this->admin)
            ->get('/admin/pergerakan-stok?tab=masuk&tanggal_mulai_masuk=' . now()->format('Y-m-d') . '&tanggal_akhir_masuk=' . now()->subDay()->format('Y-m-d'));

        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal.');
    }

    public function test_dapat_membuat_transaksi_baru_setelah_transaksi_lama_di_soft_delete()
    {
        $supplier = Supplier::factory()->create();
        $bahan = BahanBaku::factory()->create([
            'kategori' => 'bahan_pendukung',
            'satuan' => 'pcs',
            'stok' => 50,
            'is_aktif' => true,
        ]);

        // 1. Buat transaksi pertama
        $response1 = $this->actingAs($this->admin)->post('/admin/pergerakan-stok', [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $supplier->id,
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 10]
            ],
        ]);
        $response1->assertRedirect('/admin/pergerakan-stok?tab=masuk');

        $trx1 = PergerakanStokBahanBaku::latest('id')->first();
        $nomor1 = $trx1->nomor_transaksi;

        // 2. Soft delete transaksi pertama
        $this->actingAs($this->admin)->delete('/admin/pergerakan-stok/' . $trx1->id);
        $this->assertSoftDeleted('pergerakan_stok_bahan_baku', ['id' => $trx1->id]);

        // 3. Buat transaksi baru -- tidak boleh error duplicate entry
        $response2 = $this->actingAs($this->admin)->post('/admin/pergerakan-stok', [
            'jenis_pergerakan' => 'masuk',
            'tanggal' => now()->format('Y-m-d'),
            'supplier_id' => $supplier->id,
            'items' => [
                ['bahan_baku_id' => $bahan->id, 'quantity' => 5]
            ],
        ]);
        $response2->assertRedirect('/admin/pergerakan-stok?tab=masuk');

        $trx2 = PergerakanStokBahanBaku::latest('id')->first();
        $this->assertNotEquals($nomor1, $trx2->nomor_transaksi);
        $this->assertDatabaseHas('pergerakan_stok_bahan_baku', [
            'id' => $trx2->id,
            'deleted_at' => null,
        ]);
    }
}

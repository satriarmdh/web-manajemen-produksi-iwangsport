<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\RiwayatStok;
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
    // TAB STOK MASUK - VIEW & ACCESS
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
    // TAB STOK MASUK - CREATE TRANSACTION
    // ============================================

    public function test_admin_dapat_membuat_transaksi_stok_masuk_dengan_data_valid()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Benang Polyester',
            'stok' => 10
        ]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'supplier_id' => null,
            'keterangan' => 'Pembelian dari toko',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=masuk');
        $response->assertSessionHas('success');

        // Stok harus bertambah
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'stok' => 15
        ]);

        // RiwayatStok harus tercatat
        $this->assertDatabaseHas('riwayat_stok', [
            'jenis_item' => 'bahan_baku',
            'id_item' => $bahan->id,
            'jenis_pergerakan' => 'masuk',
            'jumlah' => 5,
            'stok_sebelum' => 10,
            'stok_sesudah' => 15,
            'keterangan' => 'Pembelian dari toko',
        ]);
    }

    public function test_admin_dapat_membuat_transaksi_stok_masuk_dengan_supplier()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 0]);
        $supplier = Supplier::factory()->create(['nama_supplier' => 'PT Tekstil Jaya']);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 10,
            'supplier_id' => $supplier->id,
            'keterangan' => 'Pembelian rutin',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=masuk');

        $this->assertDatabaseHas('riwayat_stok', [
            'id_item' => $bahan->id,
            'jenis_pergerakan' => 'masuk',
            'jumlah' => 10,
        ]);
    }

    public function test_stok_masuk_menambah_stok_bahan_baku_dengan_benar()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 20]);

        // Transaksi pertama
        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
        ]);

        $bahan->refresh();
        $this->assertEquals(25, $bahan->stok);

        // Transaksi kedua
        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 3,
        ]);

        $bahan->refresh();
        $this->assertEquals(28, $bahan->stok);
    }

    // ============================================
    // TAB STOK MASUK - VALIDATION
    // ============================================

    public function test_stok_masuk_wajib_memilih_bahan_baku()
    {
        $data = [
            'bahan_baku_id' => null,
            'quantity' => 5,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_stok_masuk_menolak_bahan_baku_yang_tidak_ada()
    {
        $data = [
            'bahan_baku_id' => 999,
            'quantity' => 5,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_stok_masuk_wajib_memasukkan_quantity()
    {
        $bahan = BahanBaku::factory()->create();

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => null,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_masuk_menolak_quantity_bukan_angka()
    {
        $bahan = BahanBaku::factory()->create();

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 'abc',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_masuk_menolak_quantity_kurang_dari_satu()
    {
        $bahan = BahanBaku::factory()->create();

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 0,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_masuk_menolak_quantity_negatif()
    {
        $bahan = BahanBaku::factory()->create();

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => -5,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_masuk_menolak_supplier_yang_tidak_ada()
    {
        $bahan = BahanBaku::factory()->create();

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'supplier_id' => 999,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pemasukan-bahan', $data);

        $response->assertSessionHasErrors('supplier_id');
    }

    // ============================================
    // TAB STOK KELUAR - VIEW & ACCESS
    // ============================================

    public function test_admin_dapat_melihat_halaman_stok_keluar()
    {
        $response = $this->actingAs($this->admin)->get('/admin/pergerakan-stok?tab=keluar');

        $response->assertStatus(200);
        $response->assertViewIs('admin.pergerakan-stok.index');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_stok_keluar()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/pergerakan-stok?tab=keluar');

        $response->assertStatus(403);
    }

    // ============================================
    // TAB STOK KELUAR - CREATE TRANSACTION
    // ============================================

    public function test_admin_dapat_membuat_transaksi_stok_keluar_dengan_data_valid()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Kancing Putih',
            'kategori' => 'kancing',
            'stok' => 100
        ]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 20,
            'penerima' => 'Budi (Karyawan Jahit)',
            'keterangan' => 'Untuk produksi batch A',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=keluar');
        $response->assertSessionHas('success');

        // Stok harus berkurang
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'stok' => 80
        ]);

        // RiwayatStok harus tercatat
        $this->assertDatabaseHas('riwayat_stok', [
            'jenis_item' => 'bahan_baku',
            'id_item' => $bahan->id,
            'jenis_pergerakan' => 'keluar',
            'jumlah' => 20,
            'stok_sebelum' => 100,
            'stok_sesudah' => 80,
            'keterangan' => 'Diberikan ke: Budi (Karyawan Jahit) | Untuk produksi batch A',
        ]);
    }

    public function test_stok_keluar_mengurangi_stok_bahan_baku_dengan_benar()
    {
        $bahan = BahanBaku::factory()->create([
            'kategori' => 'benang',
            'stok' => 50
        ]);

        // Transaksi pertama
        $this->actingAs($this->admin)->post('/admin/pengeluaran-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 10,
            'penerima' => 'Ahmad',
        ]);

        $bahan->refresh();
        $this->assertEquals(40, $bahan->stok);

        // Transaksi kedua
        $this->actingAs($this->admin)->post('/admin/pengeluaran-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 15,
            'penerima' => 'Siti',
        ]);

        $bahan->refresh();
        $this->assertEquals(25, $bahan->stok);
    }

    public function test_stok_keluar_hanya_boleh_untuk_bahan_non_kain()
    {
        $bahanKain = BahanBaku::factory()->create([
            'nama_bahan' => 'Kain Katun',
            'kategori' => 'kain',
            'stok' => 10
        ]);

        $data = [
            'bahan_baku_id' => $bahanKain->id,
            'quantity' => 2,
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('bahan_baku_id');
        
        // Stok tidak boleh berubah
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahanKain->id,
            'stok' => 10
        ]);
    }

    public function test_stok_keluar_membolehkan_semua_kategori_non_kain()
    {
        $kategoriNonKain = ['benang', 'kancing', 'resleting', 'aksesoris'];

        foreach ($kategoriNonKain as $kategori) {
            $bahan = BahanBaku::factory()->create([
                'kategori' => $kategori,
                'stok' => 10
            ]);

            $response = $this->actingAs($this->admin)
                ->post('/admin/pengeluaran-bahan', [
                    'bahan_baku_id' => $bahan->id,
                    'quantity' => 2,
                    'penerima' => 'Test User',
                ]);

            $response->assertRedirect('/admin/pergerakan-stok?tab=keluar');
            
            $bahan->refresh();
            $this->assertEquals(8, $bahan->stok, "Stok {$kategori} tidak berkurang");
        }
    }

    // ============================================
    // TAB STOK KELUAR - VALIDATION
    // ============================================

    public function test_stok_keluar_wajib_memilih_bahan_baku()
    {
        $data = [
            'bahan_baku_id' => null,
            'quantity' => 5,
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_stok_keluar_menolak_bahan_baku_yang_tidak_ada()
    {
        $data = [
            'bahan_baku_id' => 999,
            'quantity' => 5,
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    public function test_stok_keluar_wajib_memasukkan_quantity()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'benang', 'stok' => 10]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => null,
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_keluar_menolak_quantity_bukan_angka()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'benang', 'stok' => 10]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 'abc',
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_keluar_menolak_quantity_kurang_dari_satu()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'benang', 'stok' => 10]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 0,
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stok_keluar_tidak_boleh_lebih_dari_stok_tersedia()
    {
        $bahan = BahanBaku::factory()->create([
            'kategori' => 'benang',
            'stok' => 10
        ]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 15, // Lebih dari stok
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('quantity');
        
        // Stok tidak boleh berubah
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'stok' => 10
        ]);
    }

    public function test_stok_keluar_boleh_sama_dengan_stok_tersedia()
    {
        $bahan = BahanBaku::factory()->create([
            'kategori' => 'benang',
            'stok' => 10
        ]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 10, // Sama dengan stok
            'penerima' => 'Budi',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertRedirect('/admin/pergerakan-stok?tab=keluar');
        
        // Stok harus jadi 0
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'stok' => 0
        ]);
    }

    public function test_stok_keluar_wajib_memasukkan_penerima()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'benang', 'stok' => 10]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'penerima' => null,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('penerima');
    }

    public function test_stok_keluar_menolak_penerima_kosong()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'benang', 'stok' => 10]);

        $data = [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'penerima' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pengeluaran-bahan', $data);

        $response->assertSessionHasErrors('penerima');
    }

    // ============================================
    // RIWAYAT STOK RECORDS
    // ============================================

    public function test_riwayat_stok_mencatat_user_id_admin()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 0]);

        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('riwayat_stok', [
            'id_item' => $bahan->id,
            'jenis_pergerakan' => 'masuk',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_riwayat_stok_mencatat_keterangan()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 0]);

        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'keterangan' => 'Pembelian dari supplier A',
        ]);

        $riwayat = RiwayatStok::where('id_item', $bahan->id)
            ->where('jenis_pergerakan', 'masuk')
            ->latest()
            ->first();
        
        $this->assertEquals('Pembelian dari supplier A', $riwayat->keterangan);
    }

    public function test_banyak_transaksi_membuat_banyak_riwayat_stok()
    {
        $bahan = BahanBaku::factory()->create(['stok' => 0]);

        // Transaksi 1: Masuk
        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 10,
            'keterangan' => 'Pembelian awal',
        ]);

        // Transaksi 2: Masuk lagi
        $this->actingAs($this->admin)->post('/admin/pemasukan-bahan', [
            'bahan_baku_id' => $bahan->id,
            'quantity' => 5,
            'keterangan' => 'Pembelian kedua',
        ]);

        $this->assertDatabaseCount('riwayat_stok', 2);
        
        $bahan->refresh();
        $this->assertEquals(15, $bahan->stok);
    }
}

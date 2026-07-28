<?php

namespace Tests\Feature\Owner;

use App\Models\BahanBaku;
use App\Models\PergerakanStokBahanBaku;
use App\Models\DetailPergerakanStokBahanBaku;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiBahanBakuOwnerTest extends TestCase
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
    public function owner_dapat_mengakses_halaman_mutasi_bahan_baku()
    {
        $response = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.index'));

        $response->assertStatus(200)
            ->assertViewIs('owner.mutasi-bahan-baku.index');
    }

    /** @test */
    public function owner_dapat_melihat_detail_mutasi_bahan_baku_masuk()
    {
        $supplier = Supplier::factory()->create();
        $bahan = BahanBaku::factory()->create();
        
        $pergerakan = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-BM-20260728-0001',
            'jenis_pergerakan' => 'masuk',
            'tanggal' => today(),
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'catatan' => 'Catatan masuk test',
        ]);

        $pergerakan->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 10,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.show', $pergerakan));

        $response->assertStatus(200)
            ->assertViewIs('owner.mutasi-bahan-baku.show')
            ->assertSee($pergerakan->nomor_transaksi)
            ->assertSee($supplier->nama_supplier)
            ->assertSee($bahan->nama_bahan)
            ->assertSee('10');
    }

    /** @test */
    public function owner_dapat_melihat_detail_mutasi_bahan_baku_keluar()
    {
        $bahan = BahanBaku::factory()->create();
        
        $pergerakan = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-BK-20260728-0001',
            'jenis_pergerakan' => 'keluar',
            'tanggal' => today(),
            'penerima' => 'Budi Potong',
            'user_id' => $this->admin->id,
            'catatan' => 'Catatan keluar test',
        ]);

        $pergerakan->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 5,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.show', $pergerakan));

        $response->assertStatus(200)
            ->assertViewIs('owner.mutasi-bahan-baku.show')
            ->assertSee($pergerakan->nomor_transaksi)
            ->assertSee('Budi Potong')
            ->assertSee($bahan->nama_bahan)
            ->assertSee('5');
    }

    /** @test */
    public function owner_dapat_melihat_penggunaan_kain_wo_pada_mutasi_keluar()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'kain']);
        $produk = \App\Models\Produk::factory()->create();
        $wo = \App\Models\PerintahProduksi::create([
            'nomor_wo' => 'WO-20260728-9999',
            'tgl_mulai' => today(),
            'status_produksi' => 'disetujui',
            'user_id' => $this->admin->id,
            'approved_by' => $this->owner->id,
            'approved_at' => now(),
        ]);

        $detailWo = \App\Models\DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahan->id,
            'qty_roll_pakai' => 4,
            'estimasi_pcs' => 100,
        ]);

        $riwayat = \App\Models\RiwayatPenggunaanKain::create([
            'perintah_produksi_id' => $wo->id,
            'detail_perintah_produksi_id' => $detailWo->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah_pakai' => 4,
            'keterangan' => 'Penggunaan kain untuk WO',
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar']));

        $response->assertStatus(200)
            ->assertSee($wo->nomor_wo)
            ->assertSee('Keperluan Produksi (WO)')
            ->assertSee($bahan->nama_bahan)
            ->assertSee('4 Roll');
    }

    /** @test */
    public function owner_dapat_melihat_detail_mutasi_kain_wo()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'kain']);
        $produk = \App\Models\Produk::factory()->create();
        $wo = \App\Models\PerintahProduksi::create([
            'nomor_wo' => 'WO-20260728-8888',
            'tgl_mulai' => today(),
            'status_produksi' => 'disetujui',
            'user_id' => $this->admin->id,
            'approved_by' => $this->owner->id,
            'approved_at' => now(),
        ]);

        $detailWo = \App\Models\DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahan->id,
            'qty_roll_pakai' => 4,
            'estimasi_pcs' => 100,
        ]);

        $riwayat = \App\Models\RiwayatPenggunaanKain::create([
            'perintah_produksi_id' => $wo->id,
            'detail_perintah_produksi_id' => $detailWo->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah_pakai' => 4,
            'keterangan' => 'Penggunaan kain untuk WO',
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.show', ['pergerakanStok' => $wo->id, 'type' => 'kain']));

        $response->assertStatus(200)
            ->assertViewIs('owner.mutasi-bahan-baku.show-kain')
            ->assertSee($wo->nomor_wo)
            ->assertSee($bahan->nama_bahan)
            ->assertSee('Pantau Progres WO')
            ->assertSee('4 Roll');
    }

    /** @test */
    public function owner_dapat_memfilter_mutasi_keluar_berdasarkan_kategori_kain()
    {
        // 1. Create a Kain WO
        $bahanKain = BahanBaku::factory()->create(['kategori' => 'kain', 'nama_bahan' => 'Kain Cotton Combed']);
        $produk = \App\Models\Produk::factory()->create();
        $wo = \App\Models\PerintahProduksi::create([
            'nomor_wo' => 'WO-FILTER-KAIN',
            'tgl_mulai' => today(),
            'status_produksi' => 'disetujui',
            'user_id' => $this->admin->id,
            'approved_by' => $this->owner->id,
            'approved_at' => now(),
        ]);
        $detailWo = \App\Models\DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanKain->id,
            'qty_roll_pakai' => 10,
        ]);
        \App\Models\RiwayatPenggunaanKain::create([
            'perintah_produksi_id' => $wo->id,
            'detail_perintah_produksi_id' => $detailWo->id,
            'bahan_baku_id' => $bahanKain->id,
            'jumlah_pakai' => 10,
            'keterangan' => 'Penggunaan kain',
        ]);

        // 2. Create a Non-Kain Mutasi (e.g. benang)
        $bahanBenang = BahanBaku::factory()->create(['kategori' => 'benang', 'nama_bahan' => 'Benang Jahit Hitam']);
        $pergerakanNonKain = PergerakanStokBahanBaku::create([
            'nomor_transaksi' => 'TRX-FILTER-BENANG',
            'jenis_pergerakan' => 'keluar',
            'tanggal' => today(),
            'penerima' => 'Budi Potong',
            'user_id' => $this->admin->id,
            'catatan' => 'Mutasi benang',
        ]);
        $pergerakanNonKain->detailPergerakanStok()->create([
            'bahan_baku_id' => $bahanBenang->id,
            'jumlah' => 50,
        ]);

        // 3. Request dengan kategori_keluar=kain
        $responseKain = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar', 'kategori_keluar' => 'kain']));

        $responseKain->assertStatus(200)
            ->assertSee('WO-FILTER-KAIN')
            ->assertDontSee('TRX-FILTER-BENANG');

        // 4. Request dengan kategori_keluar=benang
        $responseBenang = $this->actingAs($this->owner)
            ->get(route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar', 'kategori_keluar' => 'benang']));

        $responseBenang->assertStatus(200)
            ->assertSee('TRX-FILTER-BENANG')
            ->assertDontSee('WO-FILTER-KAIN');
    }

    /** @test */
    public function non_owner_tidak_dapat_mengakses_halaman_mutasi_bahan_baku()
    {
        // Admin
        $responseAdmin = $this->actingAs($this->admin)
            ->get(route('owner.mutasi-bahan-baku.index'));
        $responseAdmin->assertStatus(403);

        // Karyawan
        $responseKaryawan = $this->actingAs($this->karyawan)
            ->get(route('owner.mutasi-bahan-baku.index'));
        $responseKaryawan->assertStatus(403);
    }
}

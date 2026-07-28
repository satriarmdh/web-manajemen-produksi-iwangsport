<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\Pelanggan;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\StandardBaselineProduksi;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenerimaanHasilProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $finishing;
    protected User $nonAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->finishing = User::factory()->create(['role' => 'finishing']);
        $this->nonAdmin = User::factory()->create(['role' => 'potong']);
        Storage::fake('public');
    }

    /** Helper: create a perintah produksi with detail + stok_virtual ready */
    private function createDetailWithReadyStok(int $estimasi = 100, int $toleransi = 5, int $qtyReady = 100): array
    {
        $produk = Produk::factory()->create(['stok' => 0]);
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 120,
            'toleransi_minus' => $toleransi,
        ]);

        $perintah = PerintahProduksi::factory()->dalamProduksi()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $perintah->id,
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'estimasi_pcs' => $estimasi,
            'toleransi_minus' => $toleransi,
        ]);

        $stokVirtual = StokVirtual::create([
            'id_perintah' => $perintah->id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->finishing->id,
            'id_produk' => $produk->id,
            'peran' => 'finishing',
            'qty_hold' => 0,
            'total_selesai' => $qtyReady,
            'total_dikeluarkan' => 0,
            'total_reject' => 0,
            'status_barang' => 'Ready',
            'is_selesai' => true,
        ]);

        return compact('produk', 'bahanBaku', 'perintah', 'detail', 'stokVirtual');
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_admin_dapat_menginput_penerimaan_hasil_produksi()
    {
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'catatan' => 'Penerimaan pertama',
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_karyawan_non_admin_tidak_dapat_menginput_penerimaan()
    {
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($this->nonAdmin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_tidak_dapat_menginput_penerimaan()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($owner)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertStatus(403);
    }

    // ============================================
    // VALIDATION
    // ============================================

    public function test_validasi_wajib_detail_id_qty_diterima_tanggal_dan_bukti_foto()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', []);

        $response->assertSessionHasErrors([
            'perintah_produksi_detail_id',
            'dari_karyawan_id',
            'qty_diterima',
            'tanggal_terima',
            'bukti_foto',
        ]);
    }

    public function test_qty_diterima_minimal_1()
    {
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 0,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasErrors(['qty_diterima']);
    }

    public function test_tanggal_terima_tidak_boleh_masa_depan()
    {
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 10,
                'tanggal_terima' => now()->addDay()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasErrors(['tanggal_terima']);
    }

    public function test_bukti_foto_harus_berupa_gambar()
    {
        $data = $this->createDetailWithReadyStok();

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 10,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ]);

        $response->assertSessionHasErrors(['bukti_foto']);
    }

    public function test_qty_diterima_tidak_boleh_melebihi_stok_ready()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 30);

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasErrors(['dari_karyawan_id']);
    }

    // ============================================
    // HAPPY PATH
    // ============================================

    public function test_stok_produk_bertambah_setelah_penerimaan()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'catatan' => 'Penerimaan pertama',
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $this->assertEquals(50, $data['produk']->fresh()->stok);
    }

    public function test_total_dikeluarkan_di_stok_virtual_bertambah()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 40,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $this->assertEquals(40, $data['stokVirtual']->fresh()->total_dikeluarkan);
    }

    public function test_riwayat_stok_tercatat_sebagai_masuk()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 30,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $riwayat = RiwayatStok::where('jenis_item', 'produk')
            ->where('id_item', $data['produk']->id)
            ->where('jenis_pergerakan', 'masuk')
            ->first();

        $this->assertNotNull($riwayat);
        $this->assertEquals(30, $riwayat->jumlah);
        $this->assertEquals(0, $riwayat->stok_sebelum);
        $this->assertEquals(30, $riwayat->stok_sesudah);

        // Pastikan tidak ada duplikasi riwayat dengan jenis_pergerakan 'penyesuaian'
        $penyesuaianCount = RiwayatStok::where('jenis_item', 'produk')
            ->where('id_item', $data['produk']->id)
            ->where('jenis_pergerakan', 'penyesuaian')
            ->count();
        $this->assertEquals(0, $penyesuaianCount);
    }

    public function test_total_qty_diterima_di_detail_bertambah()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 60,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $this->assertEquals(60, $data['detail']->fresh()->total_qty_diterima);
    }

    public function test_status_penerimaan_sebagian_saat_input_parsial()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        // Masih ada stok ready belum diserahkan → status 'sebagian'
        $this->assertEquals('sebagian', $data['detail']->fresh()->status_penerimaan);
    }

    public function test_status_penerimaan_sesuai_saat_input_menetes()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        // Terima semua 100
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 100,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $this->assertEquals('sesuai', $data['detail']->fresh()->status_penerimaan);
    }

    public function test_status_penerimaan_selisih_lebih_saat_input_melebihi_estimasi()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 110);

        // Terima semua 110, estimasi 100 → selisih_lebih
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 110,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $this->assertEquals('selisih_lebih', $data['detail']->fresh()->status_penerimaan);
    }

    // ============================================
    // EDGE CASES
    // ============================================

    public function test_input_bertahap_sebagian_lalu_sesuai()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        // First: terima 50
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti1.jpg'),
            ]);

        $this->assertEquals('sebagian', $data['detail']->fresh()->status_penerimaan);
        $this->assertEquals(50, $data['produk']->fresh()->stok);

        // Second: terima 50 lagi
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti2.jpg'),
            ]);

        $this->assertEquals('sesuai', $data['detail']->fresh()->status_penerimaan);
        $this->assertEquals(100, $data['produk']->fresh()->stok);
    }

    public function test_tidak_dapat_input_jika_stok_ready_habis()
    {
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        // Terima semua 100 dulu
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 100,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        // Coba terima lagi → stok ready = 0
        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 10,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasErrors(['dari_karyawan_id']);
    }

    public function test_tidak_dapat_input_dari_karyawan_tanpa_stok_virtual()
    {
        $otherFinishing = User::factory()->create(['role' => 'finishing']);
        $data = $this->createDetailWithReadyStok(100, 5, 100);

        $response = $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $otherFinishing->id,
                'qty_diterima' => 10,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $response->assertSessionHasErrors(['dari_karyawan_id']);
    }

    public function test_admin_dapat_melakukan_reversal_penerimaan_hasil_produksi()
    {
        $data = $this->createDetailWithReadyStok();

        // 1. Submit penerimaan first
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $penerimaan = \App\Models\PenerimaanHasilProduksi::first();
        $this->assertNotNull($penerimaan);

        // Verify product stock is 50
        $this->assertEquals(50, $data['produk']->fresh()->stok);

        // 2. Perform reversal
        $response = $this->actingAs($this->admin)
            ->post("/admin/penerimaan-hasil-produksi/{$penerimaan->id}/reversal", [
                'catatan' => 'Salah input qty'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify product stock is back to 0
        $this->assertEquals(0, $data['produk']->fresh()->stok);
    }

    public function test_reversal_validasi_catatan_wajib_dan_max_karakter()
    {
        $data = $this->createDetailWithReadyStok();

        // Submit penerimaan first
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $penerimaan = \App\Models\PenerimaanHasilProduksi::first();

        // Reversal without catatan
        $response = $this->actingAs($this->admin)
            ->post("/admin/penerimaan-hasil-produksi/{$penerimaan->id}/reversal", [
                'catatan' => ''
            ]);
        $response->assertSessionHasErrors(['catatan']);

        // Reversal with too long catatan
        $response = $this->actingAs($this->admin)
            ->post("/admin/penerimaan-hasil-produksi/{$penerimaan->id}/reversal", [
                'catatan' => str_repeat('a', 501)
            ]);
        $response->assertSessionHasErrors(['catatan']);
    }

    public function test_non_admin_tidak_dapat_melakukan_reversal_penerimaan_hasil_produksi()
    {
        $data = $this->createDetailWithReadyStok();

        // Submit penerimaan first
        $this->actingAs($this->admin)
            ->post('/admin/penerimaan-hasil-produksi', [
                'perintah_produksi_detail_id' => $data['detail']->id,
                'dari_karyawan_id' => $this->finishing->id,
                'qty_diterima' => 50,
                'tanggal_terima' => today()->format('Y-m-d'),
                'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
            ]);

        $penerimaan = \App\Models\PenerimaanHasilProduksi::first();

        // Non-admin trying to perform reversal
        $response = $this->actingAs($this->nonAdmin)
            ->post("/admin/penerimaan-hasil-produksi/{$penerimaan->id}/reversal", [
                'catatan' => 'Salah input qty'
            ]);

        $response->assertStatus(403);
    }
}

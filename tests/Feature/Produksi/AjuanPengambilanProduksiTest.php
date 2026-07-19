<?php

namespace Tests\Feature\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AjuanPengambilanProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $potong;
    protected User $jahit;
    protected User $jahitLain;
    protected User $finishing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->potong = User::factory()->create(['role' => 'potong']);
        $this->jahit = User::factory()->create(['role' => 'jahit']);
        $this->jahitLain = User::factory()->create(['role' => 'jahit']);
        $this->finishing = User::factory()->create(['role' => 'finishing']);
    }

    public function test_penjahit_dapat_membuat_ajuan_pengambilan_barang_ke_tukang_potong()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);

        $this->actingAs($this->jahit)
            ->post('/produksi/ajuan-pengambilan', [
                'stok_virtual_id' => 1,
                'qty_ajuan' => 250,
                'catatan_pengaju' => 'Ambil untuk dijahit',
            ])
            ->assertRedirect('/produksi/ajuan-saya');

        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_produk' => $detail->produk_id,
            'dari_karyawan_id' => $this->potong->id,
            'ke_karyawan_id' => $this->jahit->id,
            'dari_tahapan' => 'potong',
            'ke_tahapan' => 'jahit',
            'qty_ajuan' => 250,
            'status' => 'pending',
        ]);
    }

    public function test_penjahit_dapat_membuat_batch_ajuan_pengambilan_dalam_satu_submit()
    {
        $detailPertama = $this->buatDetailProduksiDisetujui();
        $detailKedua = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $detailPertama->perintah_produksi_id,
            'produk_id' => Produk::factory()->create(['nama_produk' => 'Produk B'])->id,
            'estimasi_pcs' => 300,
            'toleransi_minus' => 5,
        ]);

        $this->seedStokVirtual($detailPertama, $this->potong, 'potong', 495);
        DB::table('stok_virtual')->insert([
            'id' => 2,
            'id_perintah' => $detailKedua->perintah_produksi_id,
            'id_detail_perintah' => $detailKedua->id,
            'id_karyawan' => $this->potong->id,
            'id_produk' => $detailKedua->produk_id,
            'peran' => 'potong',
            'qty_hold' => 300,
            'total_selesai' => 300,
            'total_reject' => 0,
            'status_barang' => 'Ready',
            'is_selesai' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->jahit)
            ->post('/produksi/ajuan-pengambilan', [
                'items' => [
                    ['stok_virtual_id' => 1, 'qty_ajuan' => 250],
                    ['stok_virtual_id' => 2, 'qty_ajuan' => 120],
                    ['stok_virtual_id' => 2, 'qty_ajuan' => ''],
                ],
                'catatan_pengaju' => 'Ambil batch untuk jahit hari ini',
            ])
            ->assertRedirect('/produksi/ajuan-saya');

        $this->assertDatabaseCount('ajuan_pengambilan_produksi', 2);
        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'id_detail_perintah' => $detailPertama->id,
            'qty_ajuan' => 250,
            'catatan_pengaju' => 'Ambil batch untuk jahit hari ini',
        ]);
        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'id_detail_perintah' => $detailKedua->id,
            'qty_ajuan' => 120,
            'catatan_pengaju' => 'Ambil batch untuk jahit hari ini',
        ]);
    }

    public function test_finishing_dapat_membuat_ajuan_pengambilan_barang_ke_penjahit()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->jahit, 'jahit', 200);

        $this->actingAs($this->finishing)
            ->post('/produksi/ajuan-pengambilan', [
                'stok_virtual_id' => 1,
                'qty_ajuan' => 120,
            ])
            ->assertRedirect('/produksi/ajuan-saya');

        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'dari_karyawan_id' => $this->jahit->id,
            'ke_karyawan_id' => $this->finishing->id,
            'dari_tahapan' => 'jahit',
            'ke_tahapan' => 'finishing',
            'qty_ajuan' => 120,
            'status' => 'pending',
        ]);
    }

    public function test_ajuan_tidak_boleh_melebihi_stok_ready_sumber()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 100);

        $this->actingAs($this->jahit)
            ->post('/produksi/ajuan-pengambilan', [
                'stok_virtual_id' => 1,
                'qty_ajuan' => 120,
            ])
            ->assertSessionHasErrors(['qty_ajuan']);
    }

    public function test_tukang_potong_dapat_menyetujui_ajuan_dari_penjahit_dan_mutasi_tercatat()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250);

        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertRedirect('/produksi/ajuan-masuk');

        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'id' => $ajuanId,
            'status' => 'disetujui',
        ]);

        // Opsi A: saat approve, qty_hold sumber TIDAK berubah (hanya total_dikeluarkan yang bertambah)
        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'qty_hold' => 0, // Tetap 0 (WIP input, bukan ready stock)
            'total_selesai' => 495,
            'total_dikeluarkan' => 250, // Bertambah 250
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->jahit->id,
            'peran' => 'jahit',
            'qty_hold' => 250,
            'status_barang' => 'Proses',
        ]);

        $this->assertDatabaseHas('mutasi_produksi', [
            'id_ajuan' => $ajuanId,
            'dari_karyawan_id' => $this->potong->id,
            'ke_karyawan_id' => $this->jahit->id,
            'qty_pindah' => 250,
        ]);
    }

    public function test_tukang_potong_dapat_menolak_ajuan_dari_penjahit_tanpa_mengubah_stok()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250);

        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/reject", [
                'catatan_respon' => 'Barang belum siap diambil semua',
            ])
            ->assertRedirect('/produksi/ajuan-masuk');

        $this->assertDatabaseHas('ajuan_pengambilan_produksi', [
            'id' => $ajuanId,
            'status' => 'ditolak',
            'catatan_respon' => 'Barang belum siap diambil semua',
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'qty_hold' => 0, // Opsi A: WIP input = 0
            'total_selesai' => 495, // Ready stock tidak berubah karena ajuan ditolak
            'total_dikeluarkan' => 0,
        ]);

        $this->assertDatabaseMissing('mutasi_produksi', [
            'id_ajuan' => $ajuanId,
        ]);
    }

    public function test_penjahit_dapat_menyetujui_ajuan_dari_finishing()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->jahit, 'jahit', 200);
        $ajuanId = $this->seedAjuan($detail, $this->jahit, $this->finishing, 'jahit', 'finishing', 120);

        $this->actingAs($this->jahit)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertRedirect('/produksi/ajuan-masuk');

        // Opsi A: saat approve, qty_hold sumber TIDAK berubah (hanya total_dikeluarkan yang bertambah)
        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->jahit->id,
            'peran' => 'jahit',
            'qty_hold' => 0, // Tetap 0 (WIP input, bukan ready stock)
            'total_selesai' => 200,
            'total_dikeluarkan' => 120, // Bertambah 120
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->finishing->id,
            'peran' => 'finishing',
            'qty_hold' => 120,
        ]);
    }

    public function test_user_tidak_boleh_menyetujui_ajuan_yang_bukan_ditujukan_kepadanya()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250);

        $this->actingAs($this->jahitLain)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertStatus(403);
    }

    public function test_halaman_ajuan_menampilkan_barang_ready_dan_riwayat_mutasi_user()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);

        $this->actingAs($this->jahit)
            ->get('/produksi/ajuan-saya')
            ->assertOk()
            ->assertSee($detail->produk->nama_produk)
            ->assertSee('495 pcs')
            ->assertSee('Riwayat Ajuan Saya');
    }

    public function test_akses_halaman_ajuan_sesuai_role()
    {
        // Admin forbidden
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get('/produksi/ajuan-saya')
            ->assertStatus(403);

        // Karyawan allowed
        $this->actingAs($this->jahit)
            ->get('/produksi/ajuan-saya')
            ->assertOk();
    }

    public function test_filter_dan_search_pada_halaman_ajuan()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        // Give product a unique name that does not conflict with static texts like "Produk A-Z"
        $detail->produk->update(['nama_produk' => 'KaosPolosKeren']);
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);

        // Search match
        $this->actingAs($this->jahit)
            ->get('/produksi/ajuan-saya?search=KaosPolosKeren')
            ->assertSee('KaosPolosKeren');

        // Search no match
        $this->actingAs($this->jahit)
            ->get('/produksi/ajuan-saya?search=NONEXISTENT')
            ->assertDontSee('KaosPolosKeren');
    }

    public function test_reject_ajuan_wajib_catatan_respon()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250);

        // Reject without reason
        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/reject", [
                'catatan_respon' => '',
            ])
            ->assertSessionHasErrors(['catatan_respon']);
    }

    public function test_tidak_dapat_approve_atau_reject_ajuan_yang_sudah_direspons()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 495);
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250);

        // Approve first time
        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertRedirect('/produksi/ajuan-masuk');

        // Approve second time
        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertStatus(403);

        // Reject after approved
        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/reject", [
                'catatan_respon' => 'Reject after approve',
            ])
            ->assertStatus(403);
    }

    public function test_stale_virtual_stock_prevent_approval()
    {
        $detail = $this->buatDetailProduksiDisetujui();
        $this->seedStokVirtual($detail, $this->potong, 'potong', 200); // Only 200 ready
        $ajuanId = $this->seedAjuan($detail, $this->potong, $this->jahit, 'potong', 'jahit', 250); // Requesting 250

        // Approve with insufficient virtual stock
        $this->actingAs($this->potong)
            ->post("/produksi/ajuan-pengambilan/{$ajuanId}/approve")
            ->assertStatus(422);
    }

    private function buatDetailProduksiDisetujui(): DetailPerintahProduksi
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $produk = Produk::factory()->create(['nama_produk' => 'Produk A']);

        return DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'estimasi_pcs' => 500,
            'toleransi_minus' => 5,
        ]);
    }

    private function seedStokVirtual(DetailPerintahProduksi $detail, User $karyawan, string $peran, int $qtyHold): void
    {
        // Opsi A: qty_hold = WIP input (barang belum dikerjakan). Ready stock = total_selesai - total_dikeluarkan.
        // Untuk seed ready stock, set qty_hold=0 dan total_selesai=$qtyHold (barang sudah selesai dikerjakan).
        DB::table('stok_virtual')->insert([
            'id' => 1,
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $karyawan->id,
            'id_produk' => $detail->produk_id,
            'peran' => $peran,
            'qty_hold' => 0, // Opsi A: WIP input = 0 (barang sudah selesai dikerjakan)
            'total_selesai' => $qtyHold, // Ready stock = $qtyHold
            'total_dikeluarkan' => 0,
            'total_reject' => 0,
            'status_barang' => 'Ready',
            'is_selesai' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedAjuan(DetailPerintahProduksi $detail, User $dari, User $ke, string $dariTahapan, string $keTahapan, int $qtyAjuan): int
    {
        return DB::table('ajuan_pengambilan_produksi')->insertGetId([
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_produk' => $detail->produk_id,
            'dari_karyawan_id' => $dari->id,
            'ke_karyawan_id' => $ke->id,
            'dari_tahapan' => $dariTahapan,
            'ke_tahapan' => $keTahapan,
            'qty_ajuan' => $qtyAjuan,
            'status' => 'pending',
            'tgl_ajuan' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

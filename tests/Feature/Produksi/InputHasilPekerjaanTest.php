<?php

namespace Tests\Feature\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InputHasilPekerjaanTest extends TestCase
{
    use RefreshDatabase;

    protected User $potong;
    protected User $jahit;
    protected User $finishing;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->potong = User::factory()->create(['role' => 'potong']);
        $this->jahit = User::factory()->create(['role' => 'jahit']);
        $this->finishing = User::factory()->create(['role' => 'finishing']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_karyawan_produksi_dapat_mengakses_halaman_input_hasil_pekerjaan()
    {
        $this->actingAs($this->potong)->get('/produksi/input-hasil')->assertStatus(200);
        $this->actingAs($this->jahit)->get('/produksi/input-hasil')->assertStatus(200);
        $this->actingAs($this->finishing)->get('/produksi/input-hasil')->assertStatus(200);
    }

    public function test_admin_tidak_dapat_mengakses_halaman_input_hasil_pekerjaan()
    {
        $this->actingAs($this->admin)->get('/produksi/input-hasil')->assertStatus(403);
    }

    public function test_validasi_input_hasil_pekerjaan_wajib_memiliki_detail_dan_qty_selesai()
    {
        $this->actingAs($this->potong)
            ->post('/produksi/input-hasil', [])
            ->assertSessionHasErrors(['detail_perintah_produksi_id', 'qty_selesai']);
    }

    public function test_validasi_qty_selesai_harus_angka_positif()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->potong)
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 0,
            ])
            ->assertSessionHasErrors(['qty_selesai']);
    }

    public function test_karyawan_tidak_dapat_input_hasil_untuk_wo_yang_belum_disetujui()
    {
        $wo = PerintahProduksi::factory()->pending()->create();
        $detail = DetailPerintahProduksi::factory()->create(['perintah_produksi_id' => $wo->id]);

        $this->actingAs($this->potong)
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 10,
            ])
            ->assertStatus(403);
    }

    public function test_tukang_potong_dapat_input_hasil_dan_stok_virtual_tercatat()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 120,
            'toleransi_minus' => 10,
        ]);

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 118,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'qty_pcs_potong' => 118,
            'status_validasi_potong' => 'normal',
        ]);

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $detail->perintah_produksi_id,
            'status_produksi' => 'dalam_produksi',
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'id_produk' => $detail->produk_id,
            'peran' => 'potong',
            'qty_hold' => 0, // Opsi A: qty_hold selalu 0 untuk potong (bahan baku ditrack terpisah)
            'total_selesai' => 118,
            'total_dikeluarkan' => 0,
            'status_barang' => 'Ready',
        ]);
    }

    public function test_input_hasil_berulang_oleh_karyawan_yang_sama_menambah_stok_virtual_yang_sama()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->potong)->post('/produksi/input-hasil', [
            'detail_perintah_produksi_id' => $detail->id,
            'qty_selesai' => 115,
        ]);

        $this->actingAs($this->potong)->post('/produksi/input-hasil', [
            'detail_perintah_produksi_id' => $detail->id,
            'qty_selesai' => 5,
            'alasan' => 'Tambahan input bertahap',
        ]);

        $this->assertDatabaseCount('stok_virtual', 1);
        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'qty_hold' => 0, // Opsi A: qty_hold selalu 0 untuk potong
            'total_selesai' => 120,
            'total_dikeluarkan' => 0,
            'status_barang' => 'Ready',
        ]);
    }

    public function test_hasil_potong_di_bawah_toleransi_wajib_menyertakan_alasan()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 150,
            'toleransi_minus' => 10,
        ]);

        $this->actingAs($this->potong)
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 130,
                'tandai_selesai' => 1,
            ])
            ->assertSessionHasErrors(['alasan']);
    }

    public function test_input_bertahap_di_bawah_toleransi_tidak_wajib_alasan_jika_belum_final()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 500,
            'toleransi_minus' => 50,
        ]);

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 300,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'qty_pcs_potong' => 300,
            'status_validasi_potong' => 'normal',
        ]);
    }

    public function test_tandai_produk_selesai_menyimpan_status_final_walau_total_di_bawah_estimasi()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 500,
            'toleransi_minus' => 80,
        ]);

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 430,
                'tandai_selesai' => 1,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'total_selesai' => 430,
            'is_selesai' => true,
        ]);
    }

    public function test_hanya_tukang_potong_yang_dapat_input_hasil_awal_dari_detail_wo()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->jahit)
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 50,
            ])
            ->assertStatus(403);

        $this->actingAs($this->finishing)
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 50,
            ])
            ->assertStatus(403);
    }

    public function test_penjahit_menginput_hasil_dari_stok_virtual_yang_dipegangnya()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->seedStokVirtual([
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->jahit->id,
            'id_produk' => $detail->produk_id,
            'peran' => 'jahit',
            'qty_hold' => 80,
            'total_selesai' => 0,
            'status_barang' => 'Proses',
        ]);

        $this->actingAs($this->jahit)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/input-hasil', [
                'stok_virtual_id' => 1,
                'qty_selesai' => 70,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('stok_virtual', [
            'id' => 1,
            'id_karyawan' => $this->jahit->id,
            'peran' => 'jahit',
            'qty_hold' => 10,
            'total_selesai' => 70,
            'status_barang' => 'Ready',
        ]);
    }

    public function test_karyawan_tidak_dapat_input_hasil_melebihi_qty_hold_yang_dipegang()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->seedStokVirtual([
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->jahit->id,
            'id_produk' => $detail->produk_id,
            'peran' => 'jahit',
            'qty_hold' => 80,
            'total_selesai' => 0,
            'status_barang' => 'Proses',
        ]);

        $this->actingAs($this->jahit)
            ->post('/produksi/input-hasil', [
                'stok_virtual_id' => 1,
                'qty_selesai' => 90,
            ])
            ->assertSessionHasErrors(['qty_selesai']);
    }

    private function buatDetailProduksiDisetujui(array $attributes = []): DetailPerintahProduksi
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $produk = Produk::factory()->create();

        return DetailPerintahProduksi::factory()->create(array_merge([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'estimasi_pcs' => 120,
            'toleransi_minus' => 10,
            'qty_pcs_potong' => null,
            'status_validasi_potong' => 'pending',
        ], $attributes));
    }

    private function seedStokVirtual(array $data): void
    {
        $now = now();

        DB::table('stok_virtual')->insert(array_merge([
            'id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], $data));
    }
}

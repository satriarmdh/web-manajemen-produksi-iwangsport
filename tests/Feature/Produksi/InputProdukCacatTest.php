<?php

namespace Tests\Feature\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InputProdukCacatTest extends TestCase
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

    public function test_karyawan_produksi_dapat_mencatat_produk_cacat_pada_detail_pekerjaan()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 30,
                'keterangan' => 'Kain berlubang saat proses potong',
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('produk_cacat', [
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'id_produk' => $detail->produk_id,
            'tahapan' => 'potong',
            'qty_reject' => 30,
            'keterangan' => 'Kain berlubang saat proses potong',
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'id_produk' => $detail->produk_id,
            'peran' => 'potong',
            'qty_hold' => 0,
            'total_selesai' => 0,
            'total_reject' => 30,
            'status_barang' => 'Proses',
        ]);
    }

    public function test_input_produk_cacat_dapat_menandai_produk_selesai()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 30,
                'keterangan' => 'Cacat final',
                'tandai_selesai' => 1,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'total_reject' => 30,
            'is_selesai' => true,
        ]);
    }

    public function test_validasi_produk_cacat_wajib_memiliki_detail_qty_reject_dan_keterangan()
    {
        $this->actingAs($this->potong)
            ->post('/produksi/produk-cacat', [])
            ->assertSessionHasErrors(['detail_perintah_produksi_id', 'qty_reject', 'keterangan']);
    }

    public function test_qty_reject_harus_angka_positif()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->potong)
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 0,
                'keterangan' => 'Reject kosong',
            ])
            ->assertSessionHasErrors(['qty_reject']);
    }

    public function test_karyawan_tidak_dapat_mencatat_produk_cacat_untuk_perintah_produksi_pending()
    {
        $wo = PerintahProduksi::factory()->pending()->create();
        $detail = DetailPerintahProduksi::factory()->create(['perintah_produksi_id' => $wo->id]);

        $this->actingAs($this->potong)
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 10,
                'keterangan' => 'Belum boleh input',
            ])
            ->assertStatus(403);
    }

    public function test_admin_tidak_dapat_mencatat_produk_cacat()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        $this->actingAs($this->admin)
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 10,
                'keterangan' => 'Admin tidak boleh input',
            ])
            ->assertStatus(403);
    }

    public function test_total_sudah_diinput_menghitung_hasil_baik_dan_produk_cacat()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 500,
            'toleransi_minus' => 20,
        ]);

        $this->actingAs($this->potong)->post('/produksi/input-hasil', [
            'detail_perintah_produksi_id' => $detail->id,
            'qty_selesai' => 450,
        ]);

        $this->actingAs($this->potong)->post('/produksi/produk-cacat', [
            'detail_perintah_produksi_id' => $detail->id,
            'qty_reject' => 30,
            'keterangan' => 'Cacat potong',
        ]);

        $this->assertDatabaseHas('stok_virtual', [
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->potong->id,
            'peran' => 'potong',
            'total_selesai' => 450,
            'total_reject' => 30,
        ]);
    }

    public function test_input_final_di_bawah_toleransi_tidak_wajib_alasan_jika_total_hasil_dan_reject_memenuhi_batas_normal()
    {
        $detail = $this->buatDetailProduksiDisetujui([
            'estimasi_pcs' => 500,
            'toleransi_minus' => 20,
        ]);

        $this->actingAs($this->potong)->post('/produksi/produk-cacat', [
            'detail_perintah_produksi_id' => $detail->id,
            'qty_reject' => 30,
            'keterangan' => 'Cacat potong',
        ]);

        $this->actingAs($this->potong)
            ->from("/produksi/perintah-produksi/{$detail->perintah_produksi_id}")
            ->post('/produksi/input-hasil', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_selesai' => 450,
                'tandai_selesai' => 1,
            ])
            ->assertRedirect("/produksi/perintah-produksi/{$detail->perintah_produksi_id}");

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'qty_pcs_potong' => 450,
            'status_validasi_potong' => 'normal',
        ]);
    }

    public function test_penjahit_input_barang_cacat_mengurangi_qty_hold_pegangan()
    {
        $detail = $this->buatDetailProduksiDisetujui();

        DB::table('stok_virtual')->insert([
            'id' => 1,
            'id_perintah' => $detail->perintah_produksi_id,
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $this->jahit->id,
            'id_produk' => $detail->produk_id,
            'peran' => 'jahit',
            'qty_hold' => 80,
            'total_selesai' => 0,
            'total_reject' => 0,
            'status_barang' => 'Ready',
            'is_selesai' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->jahit)
            ->post('/produksi/produk-cacat', [
                'detail_perintah_produksi_id' => $detail->id,
                'qty_reject' => 15,
                'keterangan' => 'Jahitan rusak',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('stok_virtual', [
            'id' => 1,
            'id_karyawan' => $this->jahit->id,
            'peran' => 'jahit',
            'qty_hold' => 65,
            'total_reject' => 15,
        ]);
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
}

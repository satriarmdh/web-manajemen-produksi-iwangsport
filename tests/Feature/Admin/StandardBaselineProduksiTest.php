<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StandardBaselineProduksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandardBaselineProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Produk $produk;
    protected BahanBaku $bahanBaku;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->produk = Produk::factory()->create(['warna' => 'hitam']);
        $this->bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain', 'warna' => 'hitam']);
    }

    /**
     * Helper: buat pasangan Produk & BahanBaku dengan warna yang sama
     * agar lolos validasi withValidator (warna harus cocok)
     */
    private function createMatchingPair(string $warna = 'hitam', string $namaProduk = null, string $namaBahan = null): array
    {
        $produk = Produk::factory()->create([
            'warna' => $warna,
            'nama_produk' => $namaProduk ?? Produk::factory()->raw()['nama_produk'],
        ]);
        $bahanBaku = BahanBaku::factory()->create([
            'kategori' => 'kain',
            'warna' => $warna,
            'nama_bahan' => $namaBahan ?? BahanBaku::factory()->raw()['nama_bahan'],
        ]);

        return [$produk, $bahanBaku];
    }

    public function test_admin_dapat_mengakses_halaman_standard_baseline_produksi()
    {
        $response = $this->actingAs($this->user)->get(route('admin.standard-baseline-produksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.standard-baseline-produksi.index');
        $response->assertViewHas(['estimasi', 'produks', 'bahanBaku']);
    }

    public function test_admin_dapat_menambahkan_standard_baseline_produksi()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'keterangan' => 'Test keterangan',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil ditambahkan');

        $this->assertDatabaseHas('standard_baseline_produksi', [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'keterangan' => 'Test keterangan',
        ]);
    }

    public function test_validasi_gagal_saat_menambahkan_data_tidak_valid()
    {
        $data = [
            'produk_id' => '',
            'bahan_baku_id' => '',
            'pcs_per_roll' => -1,
            'toleransi_minus' => -1,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertSessionHasErrors(['produk_id', 'bahan_baku_id', 'pcs_per_roll', 'toleransi_minus']);
    }

    public function test_admin_dapat_memperbarui_standard_baseline_produksi()
    {
        // Buat baseline dengan pasangan warna yang cocok
        [$produk, $bahanBaku] = $this->createMatchingPair('navy');

        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $data = [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
            'keterangan' => 'Updated keterangan',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.standard-baseline-produksi.update', $baseline), $data);

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil diperbarui.');

        $this->assertDatabaseHas('standard_baseline_produksi', [
            'id' => $baseline->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
            'keterangan' => 'Updated keterangan',
        ]);
    }

    public function test_admin_dapat_menghapus_standard_baseline_produksi()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.standard-baseline-produksi.destroy', $baseline));

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil dihapus.');

        $this->assertSoftDeleted('standard_baseline_produksi', ['id' => $baseline->id]);
    }

    public function test_admin_dapat_memfilter_berdasarkan_status_aktif()
    {
        // Buat pasangan unik untuk masing-masing status
        [$produkAktif, $bahanAktif] = $this->createMatchingPair('hitam');
        [$produkNonaktif, $bahanNonaktif] = $this->createMatchingPair('abu');

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produkAktif->id,
            'bahan_baku_id' => $bahanAktif->id,
            'is_aktif' => true,
            'pcs_per_roll' => 111,
        ]);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produkNonaktif->id,
            'bahan_baku_id' => $bahanNonaktif->id,
            'is_aktif' => false,
            'pcs_per_roll' => 222,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['status' => 'aktif']));

        $response->assertStatus(200);
        // Gunakan pcs_per_roll sebagai penanda unik karena hanya muncul di baris tabel
        $response->assertSee('111')
            ->assertDontSee('222');
    }

    public function test_admin_dapat_memfilter_berdasarkan_status_nonaktif()
    {
        // Buat pasangan unik untuk masing-masing status
        [$produkAktif, $bahanAktif] = $this->createMatchingPair('hitam');
        [$produkNonaktif, $bahanNonaktif] = $this->createMatchingPair('abu');

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produkAktif->id,
            'bahan_baku_id' => $bahanAktif->id,
            'is_aktif' => true,
            'pcs_per_roll' => 333,
        ]);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produkNonaktif->id,
            'bahan_baku_id' => $bahanNonaktif->id,
            'is_aktif' => false,
            'pcs_per_roll' => 444,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['status' => 'nonaktif']));

        $response->assertStatus(200);
        // Gunakan pcs_per_roll sebagai penanda unik karena hanya muncul di baris tabel
        $response->assertDontSee('333')
            ->assertSee('444');
    }

    public function test_admin_dapat_mengurutkan_berdasarkan_waktu_terbaru()
    {
        // Buat 3 baseline dengan pasangan produk & bahan baku yang unik
        for ($i = 0; $i < 3; $i++) {
            [$produk, $bahan] = $this->createMatchingPair('hitam');
            StandardBaselineProduksi::factory()->create([
                'produk_id' => $produk->id,
                'bahan_baku_id' => $bahan->id,
                'created_at' => now()->subDays(3 - $i),
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['sort' => 'newest']));

        $response->assertStatus(200);

        $estimasi = $response->viewData('estimasi');
        $this->assertGreaterThanOrEqual(
            $estimasi->last()->created_at,
            $estimasi->first()->created_at
        );
    }

    public function test_admin_dapat_mengurutkan_berdasarkan_waktu_terlama()
    {
        // Buat 3 baseline dengan pasangan produk & bahan baku yang unik
        for ($i = 0; $i < 3; $i++) {
            [$produk, $bahan] = $this->createMatchingPair('hitam');
            StandardBaselineProduksi::factory()->create([
                'produk_id' => $produk->id,
                'bahan_baku_id' => $bahan->id,
                'created_at' => now()->subDays(3 - $i),
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['sort' => 'oldest']));

        $response->assertStatus(200);

        $estimasi = $response->viewData('estimasi');
        $this->assertLessThanOrEqual(
            $estimasi->last()->created_at,
            $estimasi->first()->created_at
        );
    }

    public function test_admin_dapat_mencari_berdasarkan_nama_produk()
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Kaos Polos', 'warna' => 'hitam']);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        [$produkLain, $bahanLain] = $this->createMatchingPair('abu');
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produkLain->id,
            'bahan_baku_id' => $bahanLain->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['search' => 'Kaos Polos']));

        $response->assertStatus(200);
        $response->assertSee('Kaos Polos');
    }

    public function test_admin_dapat_mencari_berdasarkan_nama_bahan_baku()
    {
        $bahanBaku = BahanBaku::factory()->create([
            'nama_bahan' => 'Kain Katun',
            'kategori' => 'kain',
            'warna' => 'hitam',
        ]);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['search' => 'Kain Katun']));

        $response->assertStatus(200);
        $response->assertSee('Kain Katun');
    }

    public function test_accessor_range_bawah_menghasilkan_selisih_yang_benar()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
        ]);

        $this->assertEquals(140, $baseline->range_bawah);
    }

    public function test_accessor_range_bawah_mengembalikan_nol_ketika_negatif()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 5,
            'toleransi_minus' => 10,
        ]);

        $this->assertEquals(0, $baseline->range_bawah);
    }

    public function test_relasi_dengan_produk()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(Produk::class, $baseline->produk);
        $this->assertEquals($this->produk->id, $baseline->produk->id);
    }

    public function test_relasi_dengan_bahan_baku()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(BahanBaku::class, $baseline->bahanBaku);
        $this->assertEquals($this->bahanBaku->id, $baseline->bahanBaku->id);
    }

    public function test_user_belum_login_tidak_dapat_mengakses_halaman()
    {
        $response = $this->get(route('admin.standard-baseline-produksi.index'));

        $response->assertRedirect('/login');
    }

    public function test_user_belum_login_tidak_dapat_menambahkan_data()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $response = $this->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertRedirect('/login');
    }

    public function test_user_belum_login_tidak_dapat_memperbarui_data()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 200,
        ];

        $response = $this->put(route('admin.standard-baseline-produksi.update', $baseline), $data);

        $response->assertRedirect('/login');
    }

    public function test_user_belum_login_tidak_dapat_menghapus_data()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->delete(route('admin.standard-baseline-produksi.destroy', $baseline));

        $response->assertRedirect('/login');
    }
}

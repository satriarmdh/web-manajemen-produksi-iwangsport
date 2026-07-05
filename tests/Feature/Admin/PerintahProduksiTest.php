<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\StandardBaselineProduksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerintahProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $karyawanNonAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawanNonAdmin = User::factory()->create(['role' => 'potong']);
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_admin_dapat_mengakses_halaman_perintah_produksi()
    {
        $response = $this->actingAs($this->admin)->get('/admin/perintah-produksi');

        $response->assertStatus(200)
            ->assertViewIs('admin.perintah-produksi.index');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_perintah_produksi()
    {
        $response = $this->actingAs($this->karyawanNonAdmin)->get('/admin/perintah-produksi');

        $response->assertStatus(403);
    }

    // ============================================
    // CREATE - Perintah Produksi
    // ============================================

    public function test_admin_dapat_membuat_perintah_produksi_baru()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 120,
            'toleransi_minus' => 10,
        ]);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/perintah-produksi', $data);

        $response->assertRedirect('/admin/perintah-produksi');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('perintah_produksi', [
            'status_produksi' => 'pending',
            'user_id' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty_roll_pakai' => 1,
            'estimasi_pcs' => 120,
            'toleransi_minus' => 10,
        ]);
    }

    public function test_toleransi_perintah_produksi_dihitung_berdasarkan_jumlah_roll()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 120,
            'toleransi_minus' => 5,
        ]);

        $this->actingAs($this->admin)->post('/admin/perintah-produksi', [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 5,
                ],
            ],
        ]);

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty_roll_pakai' => 5,
            'estimasi_pcs' => 600,
            'toleransi_minus' => 25,
        ]);
    }

    public function test_sistem_auto_generate_nomor_wo_sequential()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 100,
            'toleransi_minus' => 10,
        ]);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);
        $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $wo1 = PerintahProduksi::first();
        $wo2 = PerintahProduksi::latest('id')->first();

        $today = now()->format('Ymd');
        $this->assertEquals("PROD-{$today}-001", $wo1->nomor_wo);
        $this->assertEquals("PROD-{$today}-002", $wo2->nomor_wo);
    }

    public function test_validasi_field_wajib_diisi_saat_membuat_perintah_produksi()
    {
        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', []);

        $response->assertSessionHasErrors(['tgl_mulai', 'details']);
    }

    public function test_validasi_detail_wajib_memiliki_produk_dan_bahan_baku()
    {
        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => null,
                    'bahan_baku_id' => null,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['details.0.produk_id', 'details.0.bahan_baku_id']);
    }

    public function test_validasi_qty_roll_pakai_harus_angka_positif()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['details.0.qty_roll_pakai']);
    }

    public function test_validasi_tanggal_mulai_tidak_boleh_kurang_dari_hari_ini()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $data = [
            'tgl_mulai' => now()->subDay()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['tgl_mulai']);
    }

    public function test_validasi_tanggal_selesai_tidak_boleh_kurang_dari_tanggal_mulai()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $data = [
            'tgl_mulai' => now()->addDay()->format('Y-m-d'),
            'tgl_selesai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['tgl_selesai']);
    }

    public function test_validasi_detail_wajib_memiliki_standard_baseline_aktif()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['details.0.bahan_baku_id']);
    }

    public function test_validasi_kombinasi_produk_dan_bahan_baku_tidak_boleh_duplikat()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 1,
                ],
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $response->assertSessionHasErrors(['details.1.produk_id']);
    }

    // ============================================
    // READ - View & Display
    // ============================================

    public function test_admin_dapat_melihat_detail_perintah_produksi()
    {
        $wo = PerintahProduksi::factory()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/perintah-produksi/{$wo->id}");

        $response->assertStatus(200)
            ->assertSee($wo->nomor_wo)
            ->assertViewHas('perintahProduksi');
    }

    // ============================================
    // PDF PRINTING
    // ============================================

    public function test_admin_dapat_mengakses_halaman_cetak_pdf_perintah_produksi()
    {
        $wo = PerintahProduksi::factory()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/perintah-produksi/{$wo->id}/cetak-pdf");

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_berisi_data_perintah_produksi_dan_detail()
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Kaos Polo']);
        $wo = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260622-001',
            'tgl_mulai' => '2026-06-22',
        ]);
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'qty_roll_pakai' => 5,
            'estimasi_pcs' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/admin/perintah-produksi/{$wo->id}/cetak-pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
    }

    public function test_pdf_berisi_informasi_status_dan_approver()
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Test']);
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Owner Test']);
        
        $wo = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260622-002',
            'status_produksi' => 'disetujui',
            'user_id' => $admin->id,
            'approved_by' => $owner->id,
            'approved_at' => now(),
        ]);
        
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
        ]);

        $response = $this->actingAs($admin)
            ->get("/admin/perintah-produksi/{$wo->id}/cetak-pdf");

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    // public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10()
    // {
    //     PerintahProduksi::factory()->count(15)->create();

    //     $response = $this->actingAs($this->admin)->get('/admin/perintah-produksi');

    //     $response->assertStatus(200)
    //         ->assertSee('Pagination Navigation');
    // }

    // ============================================
    // UPDATE - Edit Perintah Produksi
    // ============================================

    public function test_admin_dapat_memperbarui_perintah_produksi_yang_masih_pending()
    {
        $wo = PerintahProduksi::factory()->pending()->create();
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 15,
        ]);

        $data = [
            'tgl_mulai' => now()->addDay()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->put("/admin/perintah-produksi/{$wo->id}", $data);

        $response->assertRedirect('/admin/perintah-produksi');

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'tgl_mulai' => now()->addDay()->format('Y-m-d'),
        ]);
    }

    public function test_admin_tidak_dapat_memperbarui_perintah_produksi_yang_sudah_disetujui()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();

        $data = [
            'tgl_mulai' => now()->addDay()->format('Y-m-d'),
            'details' => [],
        ];

        $response = $this->actingAs($this->admin)
            ->put("/admin/perintah-produksi/{$wo->id}", $data);

        $response->assertStatus(403);
    }

    // ============================================
    // DELETE - Soft Delete
    // ============================================

    public function test_admin_dapat_menghapus_perintah_produksi_yang_masih_pending()
    {
        $wo = PerintahProduksi::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/perintah-produksi/{$wo->id}");

        $response->assertRedirect('/admin/perintah-produksi');
        $this->assertSoftDeleted('perintah_produksi', ['id' => $wo->id]);
    }

    public function test_admin_tidak_dapat_menghapus_perintah_produksi_yang_sudah_disetujui()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/perintah-produksi/{$wo->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'deleted_at' => null,
        ]);
    }

    // public function test_data_perintah_produksi_yang_dihapus_tidak_muncul_di_halaman_index()
    // {
    //     $woAktif = PerintahProduksi::factory()->create(['nomor_wo' => 'PROD-20260622-001']);
    //     $woDihapus = PerintahProduksi::factory()->create(['nomor_wo' => 'PROD-20260622-002']);
    //     $woDihapus->delete();

    //     $response = $this->actingAs($this->admin)->get('/admin/perintah-produksi');

    //     $response->assertSee('PROD-20260622-001')
    //         ->assertDontSee('PROD-20260622-002');
    // }

    // ============================================
    // WORKFLOW - Tandai Selesai (Admin)
    // ============================================

    public function test_admin_dapat_menandai_perintah_produksi_selesai()
    {
        $wo = PerintahProduksi::factory()->dalamProduksi()->create();

        $response = $this->actingAs($this->admin)
            ->post("/admin/perintah-produksi/{$wo->id}/selesai", [
                'tgl_selesai' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect('/admin/perintah-produksi');

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'status_produksi' => 'selesai',
            'tgl_selesai' => now()->format('Y-m-d'),
        ]);
    }

    public function test_admin_tidak_dapat_menandai_perintah_produksi_selesai_jika_status_bukan_dalam_produksi()
    {
        $woPending = PerintahProduksi::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->post("/admin/perintah-produksi/{$woPending->id}/selesai", [
                'tgl_selesai' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $woPending->id,
            'status_produksi' => 'pending',
        ]);
    }

    // ============================================
    // RIWAYAT PENGGUNAAN KAIN
    // ============================================

    public function test_riwayat_penggunaan_kain_belum_tercatat_saat_perintah_produksi_masih_pending()
    {
        $produk = Produk::factory()->create();
        $bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $data = [
            'tgl_mulai' => now()->format('Y-m-d'),
            'details' => [
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'qty_roll_pakai' => 2,
                ],
            ],
        ];

        $this->actingAs($this->admin)->post('/admin/perintah-produksi', $data);

        $this->assertDatabaseMissing('riwayat_penggunaan_kain', [
            'bahan_baku_id' => $bahanBaku->id,
            'jumlah_pakai' => 2,
        ]);
    }

    // ============================================
    // FILTER & SEARCH
    // ============================================

    public function test_admin_dapat_memfilter_perintah_produksi_berdasarkan_status()
    {
        PerintahProduksi::factory()->pending()->create(['nomor_wo' => 'PROD-20260622-001']);
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-20260622-002']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/perintah-produksi?status=pending');

        $response->assertSee('PROD-20260622-001')
            ->assertDontSee('PROD-20260622-002');
    }

    public function test_admin_dapat_mencari_perintah_produksi_berdasarkan_nomor_wo()
    {
        PerintahProduksi::factory()->create(['nomor_wo' => 'PROD-20260622-001']);
        PerintahProduksi::factory()->create(['nomor_wo' => 'PROD-20260622-002']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/perintah-produksi?search=PROD-20260622-001');

        $response->assertSee('PROD-20260622-001')
            ->assertDontSee('PROD-20260622-002');
    }

    public function test_pencarian_tidak_menemukan_hasil()
    {
        PerintahProduksi::factory()->create(['nomor_wo' => 'PROD-20260622-001']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/perintah-produksi?search=PROD-99999999-999');

        $response->assertDontSee('PROD-20260622-001');
    }

    // ============================================
    // SORT
    // ============================================

    public function test_admin_dapat_mengurutkan_perintah_produksi_berdasarkan_waktu_terbaru()
    {
        $woLama = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260620-001',
            'created_at' => now()->subDays(5),
        ]);
        $woBaru = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260622-001',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/perintah-produksi?sort=terbaru');

        $response->assertSeeInOrder(['PROD-20260622-001', 'PROD-20260620-001']);
    }

    public function test_admin_dapat_mengurutkan_perintah_produksi_berdasarkan_waktu_terlama()
    {
        $woLama = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260620-001',
            'created_at' => now()->subDays(5),
        ]);
        $woBaru = PerintahProduksi::factory()->create([
            'nomor_wo' => 'PROD-20260622-001',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/perintah-produksi?sort=terlama');

        $response->assertSeeInOrder(['PROD-20260620-001', 'PROD-20260622-001']);
    }

    // public function test_default_sort_adalah_terbaru()
    // {
    //     $woLama = PerintahProduksi::factory()->create([
    //         'nomor_wo' => 'PROD-20260620-001',
    //         'created_at' => now()->subDays(5),
    //     ]);
    //     $woBaru = PerintahProduksi::factory()->create([
    //         'nomor_wo' => 'PROD-20260622-001',
    //         'created_at' => now(),
    //     ]);

    //     $response = $this->actingAs($this->admin)->get('/admin/perintah-produksi');

    //     $response->assertSeeInOrder(['PROD-20260622-001', 'PROD-20260620-001']);
    // }
}

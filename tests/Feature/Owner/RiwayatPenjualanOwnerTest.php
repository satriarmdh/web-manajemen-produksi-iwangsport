<?php

namespace Tests\Feature\Owner;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPenjualanOwnerTest extends TestCase
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
    public function owner_dapat_mengakses_halaman_index_riwayat_penjualan()
    {
        $pelanggan = Pelanggan::factory()->create(['nama_pelanggan' => 'Toko Sentosa']);
        $penjualan = Penjualan::create([
            'nomor_invoice' => 'INV-TEST-100',
            'tanggal' => today(),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->admin->id,
            'total_item' => 10,
            'total_harga' => 500000,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.riwayat-penjualan.index'));

        $response->assertStatus(200)
            ->assertViewIs('owner.riwayat-penjualan.index')
            ->assertSee($penjualan->nomor_invoice)
            ->assertSee('Toko Sentosa');
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_detail_transaksi_penjualan()
    {
        $pelanggan = Pelanggan::factory()->create(['nama_pelanggan' => 'Toko Makmur']);
        $produk = Produk::factory()->create(['nama_produk' => 'Jersey Iwangsport']);
        $penjualan = Penjualan::create([
            'nomor_invoice' => 'INV-TEST-200',
            'tanggal' => today(),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->admin->id,
            'total_item' => 5,
            'total_harga' => 250000,
        ]);

        $penjualan->detailPenjualan()->create([
            'produk_id' => $produk->id,
            'qty' => 5,
            'harga_satuan' => 50000,
            'subtotal' => 250000,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.riwayat-penjualan.show', $penjualan));

        $response->assertStatus(200)
            ->assertViewIs('owner.riwayat-penjualan.show')
            ->assertSee($penjualan->nomor_invoice)
            ->assertSee('Toko Makmur')
            ->assertSee('Jersey Iwangsport')
            ->assertSee('Rp 250.000');
    }

    /** @test */
    public function owner_dapat_mencari_dan_memfilter_transaksi_penjualan()
    {
        $pelanggan1 = Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Target']);
        $pelanggan2 = Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Lain']);

        $penjualan1 = Penjualan::create([
            'nomor_invoice' => 'INV-MATCH-1',
            'tanggal' => '2026-07-01',
            'pelanggan_id' => $pelanggan1->id,
            'user_id' => $this->admin->id,
            'total_item' => 1,
            'total_harga' => 10000,
        ]);

        $penjualan2 = Penjualan::create([
            'nomor_invoice' => 'INV-MATCH-2',
            'tanggal' => '2026-07-15',
            'pelanggan_id' => $pelanggan2->id,
            'user_id' => $this->admin->id,
            'total_item' => 2,
            'total_harga' => 20000,
        ]);

        // Uji Search
        $responseSearch = $this->actingAs($this->owner)
            ->get(route('owner.riwayat-penjualan.index', ['search' => 'Target']));

        $responseSearch->assertStatus(200)
            ->assertSee('INV-MATCH-1')
            ->assertDontSee('INV-MATCH-2');

        // Uji Filter Rentang Tanggal
        $responseFilter = $this->actingAs($this->owner)
            ->get(route('owner.riwayat-penjualan.index', [
                'tanggal_mulai' => '2026-07-10',
                'tanggal_akhir' => '2026-07-20',
            ]));

        $responseFilter->assertStatus(200)
            ->assertSee('INV-MATCH-2')
            ->assertDontSee('INV-MATCH-1');
    }

    /** @test */
    public function non_owner_tidak_dapat_mengakses_riwayat_penjualan()
    {
        $responseAdmin = $this->actingAs($this->admin)
            ->get(route('owner.riwayat-penjualan.index'));
        $responseAdmin->assertStatus(403);

        $responseKaryawan = $this->actingAs($this->karyawan)
            ->get(route('owner.riwayat-penjualan.index'));
        $responseKaryawan->assertStatus(403);
    }

    /** @test */
    public function owner_dapat_mencetak_pdf_nota_penjualan()
    {
        $pelanggan = Pelanggan::factory()->create();
        $penjualan = Penjualan::create([
            'nomor_invoice' => 'INV-PDF-OWNER',
            'tanggal' => today(),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $this->admin->id,
            'total_item' => 5,
            'total_harga' => 250000,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.riwayat-penjualan.cetak-pdf', $penjualan));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

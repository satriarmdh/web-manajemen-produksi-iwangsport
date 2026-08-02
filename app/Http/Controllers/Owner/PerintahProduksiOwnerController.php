<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\RejectPerintahProduksiRequest;
use App\Models\PerintahProduksi;
use App\Services\NotificationService;
use App\Services\PerintahProduksiService;
use Illuminate\Http\Request;

class PerintahProduksiOwnerController extends Controller
{
    protected PerintahProduksiService $service;

    public function __construct(PerintahProduksiService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of pending perintah produksi for approval
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sort']);
        $filters['status'] = 'pending'; // Owner hanya melihat yang pending

        $perintahProduksi = $this->service->getAllPaginated($filters, 10);

        return view('owner.perintah-produksi.index', compact('perintahProduksi'));
    }

    /**
     * Approve perintah produksi
     */
    public function approve(PerintahProduksi $perintahProduksi)
    {
        try {
            $this->service->approve($perintahProduksi);
        } catch (\Throwable $e) {
            return redirect()
                ->route('owner.perintah-produksi.index')
                ->with('error', $e->getMessage());
        }

        // Notifikasi -> admin
        app(NotificationService::class)->woApproved($perintahProduksi->nomor_wo);
        // Notifikasi -> semua karyawan produksi (potong, jahit, finishing)
        app(NotificationService::class)->notifyRoles(
            ['potong', 'jahit', 'finishing'],
            'WO Disetujui - Siap Dikerjakan',
            "Perintah produksi {$perintahProduksi->nomor_wo} telah disetujui Owner. Silakan cek pekerjaan Anda.",
            NotificationService::TYPE_WO_ASSIGNED,
            '/produksi/perintah-produksi'
        );

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah disetujui');
    }

    /**
     * Reject perintah produksi
     */
    public function reject(RejectPerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        try {
            $this->service->reject($perintahProduksi, $request->validated('alasan_penolakan'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('owner.perintah-produksi.index')
                ->with('error', $e->getMessage());
        }

        // Notifikasi -> admin
        app(NotificationService::class)->woRejected(
            $perintahProduksi->nomor_wo,
            $request->validated('alasan_penolakan')
        );

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah ditolak');
    }

    /**
     * Memantau progres produksi (Daftar semua WO yang disetujui / dalam produksi / selesai / ditolak)
     */
    public function pantauProgres(Request $request)
    {
        $perintahProduksi = $this->service->getPantauProgresPaginated(
            $request->only(['search', 'status', 'sort', 'tanggal_mulai']),
            10
        );

        return view('owner.pantau-progres.index', compact('perintahProduksi'));
    }

    /**
     * Menampilkan detail tahapan progres pengerjaan perintah produksi secara mendalam
     */
    public function showProgres(PerintahProduksi $perintahProduksi)
    {
        if ($perintahProduksi->status_produksi === 'pending') {
            abort(404, 'Perintah produksi masih pending dan harus disetujui terlebih dahulu.');
        }

        // Load detail relasi stok virtual, log serah terima, dan history menggunakan service global
        $perintahProduksi = $this->service->loadForDetail($perintahProduksi);

        return view('owner.pantau-progres.show', compact('perintahProduksi'));
    }
}

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\RejectPerintahProduksiRequest;
use App\Models\PerintahProduksi;
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
        $this->service->approve($perintahProduksi);

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah disetujui');
    }

    /**
     * Reject perintah produksi
     */
    public function reject(RejectPerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        $this->service->reject($perintahProduksi, $request->validated('alasan_penolakan'));

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah ditolak');
    }

    /**
     * Memantau progres produksi (Daftar semua WO yang disetujui / dalam produksi / selesai / ditolak)
     */
    public function pantauProgres(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort', 'tanggal_mulai']);
        
        $query = PerintahProduksi::with(['user', 'approver', 'details.produk', 'details.bahanBaku'])
            ->where('status_produksi', '!=', 'pending');

        // Filter berdasarkan status
        if (!empty($filters['status']) && $filters['status'] !== 'pending') {
            $query->where('status_produksi', $filters['status']);
        }

        // Search berdasarkan nomor WO
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_wo', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter berdasarkan tanggal mulai
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tgl_mulai', $filters['tanggal_mulai']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        $query->when($sort === 'terbaru', fn($q) => $q->latest())
              ->when($sort === 'terlama', fn($q) => $q->oldest());

        $perintahProduksi = $query->paginate(10)->withQueryString();

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePenerimaanHasilProduksiRequest;
use App\Http\Requests\Admin\ReversalHasilProduksiRequest;
use App\Models\DetailPerintahProduksi;
use App\Models\PenerimaanHasilProduksi;
use App\Services\PenerimaanHasilProduksiService;
use Illuminate\Http\Request;

class PenerimaanHasilProduksiController extends Controller
{
    public function __construct(
        private readonly PenerimaanHasilProduksiService $service
    ) {}

    /**
     * Store a new penerimaan hasil produksi
     */
    public function store(StorePenerimaanHasilProduksiRequest $request)
    {
        try {
            $detail = DetailPerintahProduksi::findOrFail($request->validated('perintah_produksi_detail_id'));
            
            $penerimaan = $this->service->create(
                $detail,
                $request->user(),
                $request->validated()
            );

            $msg = $penerimaan->jenis_penerimaan === 'cacat'
                ? "Penerimaan hasil cacat/reject berhasil dicatat ({$penerimaan->qty_diterima} pcs)."
                : "Penerimaan hasil produksi berhasil dicatat. Stok produk bertambah {$penerimaan->qty_diterima} pcs.";

            return redirect()
                ->back()
                ->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mencatat penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Get history for a detail (AJAX for modal)
     */
    public function history(Request $request, int $detailId)
    {
        $detail = DetailPerintahProduksi::with(['produk', 'penerimaanHasilProduksi.admin', 'penerimaanHasilProduksi.dariKaryawan'])
            ->findOrFail($detailId);

        $history = $this->service->getHistoryForDetail($detailId);
        $summary = $this->service->calculateSummary($detail);

        return response()->json([
            'detail' => [
                'id' => $detail->id,
                'produk_nama' => $detail->produk->nama_produk . ' - ' . ucfirst($detail->produk->warna),
            ],
            'summary' => $summary,
            'history' => $history->map(fn($item) => [
                'id' => $item->id,
                'tanggal_terima' => $item->tanggal_terima->format('d M Y'),
                'qty_diterima' => $item->qty_diterima,
                'jenis_penerimaan' => $item->jenis_penerimaan ?? 'baik',
                'admin_name' => $item->admin->name,
                'dari_karyawan_name' => $item->dariKaryawan->name,
                'catatan' => $item->catatan,
                'bukti_foto_url' => $item->bukti_foto ? asset('storage/' . $item->bukti_foto) : null,
                'created_at' => $item->created_at->format('d M Y H:i'),
            ])
        ]);
    }

    public function reversal(ReversalHasilProduksiRequest $request, PenerimaanHasilProduksi $penerimaan)
    {
        try {
            $reversal = $this->service->createReversal(
                $penerimaan,
                $request->user(),
                $request->validated('catatan')
            );

            return redirect()
                ->back()
                ->with('success', "Koreksi berhasil. Qty {$penerimaan->qty_diterima} pcs dikembalikan ke karyawan.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat koreksi: ' . $e->getMessage());
        }
    }

    /**
     * Get available karyawan for detail (AJAX for dropdown)
     */
    public function availableKaryawan(Request $request, int $detailId)
    {
        $detail = DetailPerintahProduksi::findOrFail($detailId);
        $type = $request->query('type', 'baik');
        $karyawan = $this->service->getAvailableKaryawanForDetail($detail, $type);

        return response()->json([
            'karyawan' => $karyawan
        ]);
    }
}

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Illuminate\Http\Request;

class RiwayatPenjualanOwnerController extends Controller
{
    public function __construct(
        private readonly PenjualanService $service
    ) {}

    /**
     * Display a listing of the sales history.
     */
    public function index(Request $request)
    {
        $penjualan = $this->service->getPenjualanPaginated(
            $request->only(['search', 'tanggal_mulai', 'tanggal_akhir']),
            10
        );

        return view('owner.riwayat-penjualan.index', compact('penjualan'));
    }

    /**
     * Display the specified sales transaction.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'user', 'detailPenjualan.produk', 'pembayaranPenjualan.user']);

        return view('owner.riwayat-penjualan.show', compact('penjualan'));
    }

    /**
     * Cetak PDF Nota Penjualan (Owner View)
     */
    public function cetakPdf(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'user', 'detailPenjualan.produk', 'pembayaranPenjualan.user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.penjualan.pdf', compact('penjualan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('nota-penjualan-' . $penjualan->nomor_invoice . '.pdf');
    }
}

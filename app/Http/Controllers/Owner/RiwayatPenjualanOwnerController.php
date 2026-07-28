<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class RiwayatPenjualanOwnerController extends Controller
{
    /**
     * Display a listing of the sales history.
     */
    public function index(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'user'])
            ->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q) use ($search) {
                      $q->where('nama_pelanggan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->input('tanggal_akhir'));
        }

        $penjualan = $query->paginate(10)->withQueryString();

        return view('owner.riwayat-penjualan.index', compact('penjualan'));
    }

    /**
     * Display the specified sales transaction.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'user', 'detailPenjualan.produk']);

        return view('owner.riwayat-penjualan.show', compact('penjualan'));
    }
}

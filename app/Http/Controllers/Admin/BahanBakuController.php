<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Http\Requests\Admin\StoreBahanBakuRequest;
use App\Http\Requests\Admin\UpdateBahanBakuRequest;
use App\Services\BahanBakuService;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function __construct(
        protected BahanBakuService $bahanBakuService
    ) {}

    public function index(Request $request)
    {
        // Tangkap parameter filter dari URL
        $filters = $request->only(['search', 'kategori', 'stok', 'sort']);

        // Panggil service dengan parameter filters
        $bahanBaku = $this->bahanBakuService->getAllPaginated($filters);
        $nextNumbers = $this->bahanBakuService->getNextNumbers();

        $stats = [
            'total_items' => BahanBaku::count(),
            'stok_menipis' => BahanBaku::where('stok', '<', 10)->count(),
            'stok_habis' => BahanBaku::where('stok', '=', 0)->count(),
            'total_kategori' => BahanBaku::distinct('kategori')->count('kategori'),
        ];

        return view('admin.bahan-baku.index', compact('bahanBaku', 'nextNumbers', 'stats'));
    }

    public function store(StoreBahanBakuRequest $request)
    {
        // Langsung kirim data yang sudah tervalidasi ke Service
        $bahanBaku = $this->bahanBakuService->store($request->validated());

        return redirect()->route('admin.bahan-baku.index')
            ->with('success', 'Bahan baku "'. $bahanBaku->nama_bahan . '" berhasil ditambahkan');
    }

    public function update(UpdateBahanBakuRequest $request, BahanBaku $bahanBaku)
    {
        $this->bahanBakuService->update($bahanBaku, $request->validated());

        return redirect()->route('admin.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $this->bahanBakuService->delete($bahanBaku);
        
        return redirect()->route('admin.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }

    /**
     * Generate kode bahan baku berdasarkan kategori (AJAX endpoint)
     */
    public function generateKode(string $kategori)
    {
        $kode = $this->bahanBakuService->generateKodeBahan($kategori);
        
        return response()->json([
            'kode' => $kode
        ]);
    }
}

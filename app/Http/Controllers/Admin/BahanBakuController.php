<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Http\Requests\Admin\StoreBahanBakuRequest;
use App\Http\Requests\Admin\UpdateBahanBakuRequest;
use App\Services\BahanBakuService;

class BahanBakuController extends Controller
{
    public function __construct(
        protected BahanBakuService $bahanBakuService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        // Tangkap parameter filter dari URL
        $filters = $request->only(['search', 'kategori', 'stok', 'sort']);

        // Panggil service dengan parameter filters
        $bahanBaku = $this->bahanBakuService->getAllPaginated($filters);
        $nextNumbers = $this->bahanBakuService->getNextNumbers();

        return view('admin.bahan-baku.index', compact('bahanBaku', 'nextNumbers'));
    }

    public function store(StoreBahanBakuRequest $request)
    {
        // Langsung kirim data yang sudah tervalidasi ke Service
        $bahanBaku = $this->bahanBakuService->store($request->validated());

        return redirect()->route('admin.bahan-baku.index')
            ->with('success', 'Bahan baku berhasil ditambahkan dengan kode ' . $bahanBaku->kode_bahan);
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
}

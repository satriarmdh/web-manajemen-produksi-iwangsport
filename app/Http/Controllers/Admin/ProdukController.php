<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Http\Requests\Admin\StoreProdukRequest;
use App\Http\Requests\Admin\UpdateProdukRequest;
use App\Services\ProdukService;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function __construct(
        protected ProdukService $produkService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'ukuran', 'stok', 'sort']);
        $produk = $this->produkService->getAllPaginated($filters);
        $nextNumber = $this->produkService->generateKodeProduk();

        $stats = [
            'total_items' => Produk::count(),
            'stok_menipis' => Produk::where('stok', '<', 100)->count(),
            'produk_aktif' => Produk::where('is_aktif', true)->count(),
        ];

        return view('admin.produk.index', compact('produk', 'nextNumber', 'stats'));
    }

    public function store(StoreProdukRequest $request)
    {
        $produk = $this->produkService->store($request->validated());

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk "' . $produk->nama_produk . '" berhasil ditambahkan');
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $this->produkService->update($produk, $request->validated());

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $this->produkService->delete($produk);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}

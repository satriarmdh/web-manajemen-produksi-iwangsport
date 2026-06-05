<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use Illuminate\Validation\Rule;
// use SebastianBergmann\CodeUnit\FunctionUnit;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBaku = BahanBaku::latest()->get();
        return view('admin.bahan-baku.index', compact('bahanBaku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_bahan' => 'required|string|unique:bahan_baku,kode_bahan',
            'nama_bahan' => 'required|string|max:255',
            'warna' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok' => 'nullable|integer|min:0',
        ]);

        BahanBaku::create($validated);

        return redirect()->route('admin.bahan-baku.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $validated = $request->validate([
            'kode_bahan' => [
                'required',
                'string',
                Rule::unique('bahan_baku')->ignore($bahanBaku->id),
            ],
            'nama_bahan' => 'required|string|max:255',
            'warna' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
        ]);

        $bahanBaku->update($validated);

        return redirect()->route('admin.bahan-baku.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete(); // Soft delete bekerja otomatis

        return redirect()->route('admin.bahan-baku.index')->with('success', 'Bahan baku berhasil dihapus.');
    }
}

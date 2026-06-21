<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePelangganRequest;
use App\Http\Requests\Admin\UpdatePelangganRequest;
use App\Models\Pelanggan;
use App\Services\PelangganService;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function __construct(
        protected PelangganService $pelangganService
    ) {}

    /**
     * Display a listing of pelanggan
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort']);
        $pelanggan = $this->pelangganService->getPelanggan($filters);
        $nextNumber = $this->pelangganService->generateKode();

        return view('admin.pelanggan.index', compact('pelanggan', 'nextNumber'));
    }

    /**
     * Display pelanggan detail (JSON)
     */
    public function show(Pelanggan $pelanggan)
    {
        return response()->json($pelanggan);
    }

    /**
     * Store a newly created pelanggan
     */
    public function store(StorePelangganRequest $request)
    {
        $pelanggan = $this->pelangganService->create($request->validated());

        return redirect()
            ->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan "' . $pelanggan->nama_pelanggan . '" berhasil ditambahkan');
    }

    /**
     * Update the specified pelanggan
     */
    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan)
    {
        $this->pelangganService->update($pelanggan, $request->validated());

        return redirect()
            ->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan "' . $pelanggan->nama_pelanggan . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified pelanggan (soft delete)
     */
    public function destroy(Pelanggan $pelanggan)
    {
        $this->pelangganService->delete($pelanggan);

        return redirect()
            ->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan "' . $pelanggan->nama_pelanggan . '" berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'kategori', 'status', 'sort']);
        $suppliers = $this->supplierService->getSuppliers($filters);
        $nextNumber = $this->supplierService->generateKode();

        return view('admin.supplier.index', compact('suppliers', 'nextNumber'));
    }

    /**
     * Store a newly created supplier
     */
    public function store(StoreSupplierRequest $request)
    {
        $supplier = $this->supplierService->create($request->validated());

        return redirect()
            ->route('admin.supplier.index')
            ->with('success', 'Supplier "' . $supplier->nama_supplier . '" berhasil ditambahkan');
    }

    /**
     * Display supplier detail (JSON)
     */
    public function show(Supplier $supplier)
    {
        return response()->json($this->supplierService->loadForDetail($supplier));
    }

    /**
     * Update the specified supplier
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $this->supplierService->update($supplier, $request->validated());

        return redirect()
            ->route('admin.supplier.index')
            ->with('success', 'Supplier "' . $supplier->nama_supplier . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified supplier (soft delete)
     */
    public function destroy(Supplier $supplier)
    {
        $this->supplierService->delete($supplier);

        return redirect()
            ->route('admin.supplier.index')
            ->with('success', 'Supplier "' . $supplier->nama_supplier . '" berhasil dihapus.');
    }
}

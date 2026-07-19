<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\InputHasilPotongRequest;
use App\Models\DetailPerintahProduksi;
use App\Services\PerintahProduksiService;

class PotongController extends Controller
{
    public function __construct(
        private readonly PerintahProduksiService $service
    ) {}

    /**
     * Input hasil potong untuk detail perintah produksi
     */
    public function inputHasil(InputHasilPotongRequest $request, DetailPerintahProduksi $detail)
    {
        $this->service->inputHasilPotong(
            $detail,
            $request->qty_pcs_potong,
            $request->alasan
        );

        return redirect()
            ->back()
            ->with('success', 'Hasil potong berhasil diinput');
    }
}

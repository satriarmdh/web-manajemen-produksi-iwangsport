<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\StoreProdukCacatRequest;
use App\Services\ProdukCacatService;

class ProdukCacatController extends Controller
{
    public function store(StoreProdukCacatRequest $request, ProdukCacatService $service)
    {
        $service->store($request->validated(), $request->user());

        return redirect()
            ->back()
            ->with('success', 'Data barang cacat berhasil dicatat');
    }
}

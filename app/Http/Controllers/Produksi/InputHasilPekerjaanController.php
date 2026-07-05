<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\StoreInputHasilPekerjaanRequest;
use App\Services\InputHasilPekerjaanService;

class InputHasilPekerjaanController extends Controller
{
    public function store(StoreInputHasilPekerjaanRequest $request, InputHasilPekerjaanService $service)
    {
        $service->store($request->validated(), $request->user());

        return redirect()
            ->back()
            ->with('success', 'Hasil pekerjaan berhasil diinput');
    }
}

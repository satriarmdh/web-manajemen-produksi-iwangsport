<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Services\DashboardProduksiService;
use Illuminate\Http\Request;

class DashboardProduksiController extends Controller
{
    public function __construct(
        private readonly DashboardProduksiService $service
    ) {}

    public function index(Request $request)
    {
        return view('produksi.dashboard', $this->service->getStats($request->user()));
    }
}


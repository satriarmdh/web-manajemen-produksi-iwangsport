<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardProduksiController extends Controller
{
    public function index(Request $request)
    {
        return view('produksi.dashboard');
    }
}

<?php

namespace App\Http\Requests\Produksi;

use App\Models\AjuanPengambilanProduksi;
use Illuminate\Foundation\Http\FormRequest;

class ApproveAjuanPengambilanProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ajuan = $this->route('ajuan');

        return $ajuan instanceof AjuanPengambilanProduksi
            && in_array($this->user()?->role, ['potong', 'jahit'], true)
            && (int) $ajuan->dari_karyawan_id === (int) $this->user()?->id
            && $ajuan->dari_tahapan === $this->user()?->role
            && $ajuan->status === 'pending';
    }

    public function rules(): array
    {
        return [];
    }
}
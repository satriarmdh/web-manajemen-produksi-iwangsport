<?php

namespace App\Http\Requests\Admin;

// use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|max:255',
            'warna'      => 'required|string|max:100',
            'kategori'   => 'required|string|max:100',
            'satuan'     => 'required|string|max:50',
            'stok'       => 'nullable|integer|min:0',
            'is_aktif'   => 'nullable|boolean',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

// use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBahanBakuRequest extends FormRequest
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
            'stok'       => 'required|integer|min:0',
            'is_aktif'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan baku wajib diisi.',
            'nama_bahan.string'   => 'Nama bahan baku harus berupa teks.',
            'nama_bahan.max'      => 'Nama bahan baku maksimal 255 karakter.',
            'warna.required'      => 'Warna wajib dipilih.',
            'warna.string'        => 'Warna harus berupa teks.',
            'warna.max'           => 'Warna maksimal 100 karakter.',
            'kategori.required'   => 'Kategori wajib dipilih.',
            'kategori.string'     => 'Kategori harus berupa teks.',
            'kategori.max'        => 'Kategori maksimal 100 karakter.',
            'satuan.required'     => 'Satuan wajib dipilih.',
            'satuan.string'       => 'Satuan harus berupa teks.',
            'satuan.max'          => 'Satuan maksimal 50 karakter.',
            'stok.required'       => 'Stok wajib diisi.',
            'stok.integer'        => 'Stok harus berupa angka.',
            'stok.min'            => 'Stok tidak boleh kurang dari 0.',
            'is_aktif.boolean'    => 'Status aktif tidak valid.',
        ];
    }
}

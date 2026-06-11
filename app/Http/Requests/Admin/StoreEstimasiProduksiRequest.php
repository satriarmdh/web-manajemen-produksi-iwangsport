<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimasiProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produk_id'      => 'required|exists:produk,id',
            'bahan_baku_id'  => 'required|exists:bahan_baku,id|unique:estimasi_produksi,bahan_baku_id,NULL,id,produk_id,' . $this->produk_id,
            'pcs_per_roll'   => 'required|integer|min:1',
            'toleransi_minus' => 'nullable|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
            'is_aktif'       => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'produk_id.required' => 'Produk harus dipilih.',
            'produk_id.exists' => 'Produk yang dipilih tidak valid.',
            'bahan_baku_id.required' => 'Bahan baku harus dipilih.',
            'bahan_baku_id.exists' => 'Bahan baku yang dipilih tidak valid.',
            'bahan_baku_id.unique' => 'Kombinasi produk dan bahan baku ini sudah ada.',
            'pcs_per_roll.required' => 'Jumlah pcs per roll harus diisi.',
            'pcs_per_roll.integer' => 'Jumlah pcs per roll harus berupa angka.',
            'pcs_per_roll.min' => 'Jumlah pcs per roll minimal 1.',
            'toleransi_minus.integer' => 'Toleransi minus harus berupa angka.',
            'toleransi_minus.min' => 'Toleransi minus tidak boleh negatif.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produkId = $this->route('produk')?->id ?? $this->route('produk');

        return [
            'kode_produk'  => 'nullable|string|unique:produk,kode_produk,' . $produkId,
            'nama_produk'  => 'required|string|max:255',
            'ukuran'       => 'required|in:normal,jumbo',
            'warna'        => 'required|string|max:100',
            'harga_satuan' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:50',
            'stok'         => 'required|integer|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'kode_produk.unique'    => 'Kode produk ini sudah digunakan oleh produk lain.',
            'nama_produk.required'  => 'Nama produk wajib diisi.',
            'nama_produk.max'       => 'Nama produk maksimal 255 karakter.',
            'ukuran.required'       => 'Ukuran wajib dipilih.',
            'ukuran.in'             => 'Ukuran hanya boleh dipilih: normal atau jumbo.',
            'warna.required'        => 'Warna wajib diisi.',
            'warna.max'             => 'Warna maksimal 100 karakter.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.integer'  => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'      => 'Harga satuan tidak boleh kurang dari 0.',
            'satuan.required'       => 'Satuan wajib diisi.',
            'satuan.max'            => 'Satuan maksimal 50 karakter.',
            'stok.required'         => 'Stok wajib diisi.',
            'stok.integer'          => 'Stok harus berupa angka.',
            'stok.min'              => 'Stok tidak boleh kurang dari 0.',
        ];
    }
}

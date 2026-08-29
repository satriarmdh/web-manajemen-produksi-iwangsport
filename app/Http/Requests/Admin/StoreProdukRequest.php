<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_produk'  => 'nullable|string|unique:produk,kode_produk',
            'nama_produk'  => 'required|string|max:255',
            'ukuran'       => 'required|in:normal,jumbo',
            'warna'        => 'required|string|max:100',
            'harga_satuan' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:50',
            'stok'         => 'nullable|integer|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'is_aktif'     => 'nullable|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'kode_produk.string'    => 'Kode produk harus berupa teks.',
            'kode_produk.unique'    => 'Kode produk ini sudah digunakan, silakan coba lagi.',
            'nama_produk.required'  => 'Nama produk wajib diisi.',
            'nama_produk.string'    => 'Nama produk harus berupa teks.',
            'nama_produk.max'       => 'Nama produk maksimal 255 karakter.',
            'ukuran.required'       => 'Ukuran wajib dipilih.',
            'ukuran.in'             => 'Ukuran hanya boleh dipilih: normal atau jumbo.',
            'warna.required'        => 'Warna wajib diisi.',
            'warna.string'          => 'Warna harus berupa teks.',
            'warna.max'             => 'Warna maksimal 100 karakter.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.integer'  => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'      => 'Harga satuan tidak boleh kurang dari 0.',
            'satuan.required'       => 'Satuan wajib diisi.',
            'satuan.string'          => 'Satuan harus berupa teks.',
            'satuan.max'            => 'Satuan maksimal 50 karakter.',
            'stok.integer'          => 'Stok harus berupa angka.',
            'stok.min'              => 'Stok tidak boleh kurang dari 0.',
            'is_aktif.boolean'      => 'Status aktif tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'stok' => $this->filled('stok') ? (int) $this->input('stok') : 0,
            'stok_minimal' => $this->filled('stok_minimal') ? (int) $this->input('stok_minimal') : 0,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $nama = trim($this->input('nama_produk', ''));
            $ukuran = trim($this->input('ukuran', ''));
            $warna = trim($this->input('warna', ''));

            if ($nama !== '' && $ukuran !== '' && $warna !== '') {
                $exists = \App\Models\Produk::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($nama)])
                    ->whereRaw('LOWER(TRIM(ukuran)) = ?', [strtolower($ukuran)])
                    ->whereRaw('LOWER(TRIM(warna)) = ?', [strtolower($warna)])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'nama_produk',
                        "Produk dengan nama '{$nama}', ukuran '" . ucfirst($ukuran) . "', dan warna '" . ucfirst($warna) . "' sudah ada di dalam sistem."
                    );
                }
            }
        });
    }
}

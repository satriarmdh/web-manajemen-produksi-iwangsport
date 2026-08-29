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
            'kategori'   => 'required|string|in:kain,bahan_pendukung|max:100',
            'satuan'     => 'required|string|max:50',
            'stok'       => 'required|integer|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
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

    protected function prepareForValidation(): void
    {
        if ($this->filled('kategori')) {
            $kategori = strtolower(trim($this->input('kategori', '')));
            $satuan = ($kategori === 'kain') ? 'roll' : 'pcs';
            $this->merge(['satuan' => $satuan]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $nama = trim($this->input('nama_bahan', ''));
            $warna = trim($this->input('warna', ''));
            $kategori = trim($this->input('kategori', ''));
            $satuan = trim($this->input('satuan', ''));

            if ($nama !== '' && $warna !== '' && $kategori !== '' && $satuan !== '') {
                $bahanBakuId = $this->route('bahan_baku')?->id ?? $this->route('bahan_baku');

                $exists = \App\Models\BahanBaku::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [strtolower($nama)])
                    ->whereRaw('LOWER(TRIM(warna)) = ?', [strtolower($warna)])
                    ->whereRaw('LOWER(TRIM(kategori)) = ?', [strtolower($kategori)])
                    ->whereRaw('LOWER(TRIM(satuan)) = ?', [strtolower($satuan)])
                    ->when($bahanBakuId, fn($q) => $q->where('id', '!=', $bahanBakuId))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'nama_bahan',
                        "Bahan baku '{$nama}' dengan warna '" . ucfirst($warna) . "', kategori '" . ucfirst($kategori) . "', dan satuan '" . ucfirst($satuan) . "' sudah ada di dalam sistem."
                    );
                }
            }
        });
    }
}

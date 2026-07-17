<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_supplier' => 'nullable|string|unique:suppliers,kode_supplier|max:20',
            'nama_supplier' => 'required|string|max:255',
            'kategori'      => 'required|array|min:1',
            'kategori.*'    => 'string|in:kain,benang,kancing,resleting,aksesoris',
            'kontak'        => 'required|string|max:20',
            'email'         => 'required|email|unique:suppliers,email|max:255',
            'alamat'        => 'required|string',
            'catatan'       => 'nullable|string',
            'is_aktif'      => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_supplier.string' => 'Kode supplier harus berupa teks.',
            'kode_supplier.unique' => 'Kode supplier ini sudah terdaftar.',
            'kode_supplier.max' => 'Kode supplier maksimal 20 karakter.',
            'nama_supplier.required' => 'Nama supplier harus diisi.',
            'nama_supplier.string' => 'Nama supplier harus berupa teks.',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter.',
            'kategori.required' => 'Pilih minimal satu kategori.',
            'kategori.array' => 'Format kategori tidak valid.',
            'kategori.min' => 'Pilih minimal satu kategori.',
            'kategori.*.string' => 'Format kategori tidak valid.',
            'kategori.*.in' => 'Kategori yang dipilih tidak valid.',
            'kontak.required' => 'Nomor kontak harus diisi.',
            'kontak.string' => 'Nomor kontak harus berupa teks.',
            'kontak.max' => 'Nomor kontak maksimal 20 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.max' => 'Email maksimal 255 karakter.',
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'catatan.string' => 'Catatan harus berupa teks.',
            'is_aktif.boolean' => 'Status tidak valid.',
        ];
    }
}

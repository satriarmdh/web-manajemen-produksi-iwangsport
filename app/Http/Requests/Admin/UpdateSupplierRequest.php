<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')->id;

        return [
            'kode_supplier' => 'nullable|string|unique:suppliers,kode_supplier,' . $supplierId . '|max:20',
            'nama_supplier' => 'required|string|max:255',
            'kategori'      => 'required|array|min:1',
            'kategori.*'    => 'string|in:kain,benang,kancing,resleting,aksesoris',
            'kontak'        => 'required|string|max:20',
            'email'         => 'required|email|unique:suppliers,email,' . $supplierId . '|max:255',
            'alamat'        => 'required|string',
            'catatan'       => 'nullable|string',
            'status'        => 'nullable|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_supplier.unique' => 'Kode supplier ini sudah terdaftar.',
            'nama_supplier.required' => 'Nama supplier harus diisi.',
            'kategori.required' => 'Pilih minimal satu kategori.',
            'kategori.min' => 'Pilih minimal satu kategori.',
            'kategori.*.in' => 'Kategori yang dipilih tidak valid.',
            'kontak.required' => 'Nomor kontak harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'alamat.required' => 'Alamat harus diisi.',
            'status.in' => 'Status harus aktif atau nonaktif.',
        ];
    }
}

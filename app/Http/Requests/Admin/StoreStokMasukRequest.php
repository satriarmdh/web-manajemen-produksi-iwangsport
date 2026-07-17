<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokMasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Auth sudah dihandle middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'bahan_baku_id'   => 'required|exists:bahan_baku,id',
            'quantity'        => 'required|integer|min:1',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'keterangan'      => 'nullable|string|max:500',
            'bukti_pembelian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'bahan_baku_id.required'  => 'Bahan baku wajib dipilih.',
            'bahan_baku_id.exists'    => 'Bahan baku tidak ditemukan.',
            'quantity.required'       => 'Jumlah wajib diisi.',
            'quantity.integer'        => 'Jumlah harus berupa angka.',
            'quantity.min'            => 'Jumlah minimal 1.',
            'supplier_id.exists'      => 'Supplier tidak ditemukan.',
            'keterangan.string'       => 'Keterangan harus berupa teks.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'bukti_pembelian.file'    => 'Bukti pembelian harus berupa file.',
            'bukti_pembelian.mimes'   => 'Format bukti pembelian: JPG, PNG, atau PDF.',
            'bukti_pembelian.max'     => 'Ukuran bukti pembelian maksimal 5 MB.',
        ];
    }
}

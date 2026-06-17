<?php

namespace App\Http\Requests\Admin;

use App\Models\BahanBaku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStokKeluarRequest extends FormRequest
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
            'bahan_baku_id' => [
                'required',
                'exists:bahan_baku,id',
                Rule::exists('bahan_baku', 'id')->where(function ($query) {
                    $query->where('kategori', '!=', 'kain');
                }),
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                // Validasi stok tersedia (custom rule)
                function ($attribute, $value, $fail) {
                    $bahanBaku = BahanBaku::find($this->input('bahan_baku_id'));
                    if ($bahanBaku && $value > $bahanBaku->stok) {
                        $fail('Jumlah yang diminta melebihi stok tersedia (stok saat ini: ' . $bahanBaku->stok . ').');
                    }
                },
            ],
            'penerima'            => 'required|string|max:255',
            'keterangan'          => 'nullable|string|max:500',
            'bukti_pengeluaran'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'bahan_baku_id.required'  => 'Bahan baku wajib dipilih.',
            'bahan_baku_id.exists'    => 'Bahan baku tidak ditemukan atau kategori kain tidak dapat dikeluarkan melalui menu ini.',
            'quantity.required'       => 'Jumlah wajib diisi.',
            'quantity.integer'        => 'Jumlah harus berupa angka.',
            'quantity.min'            => 'Jumlah minimal 1.',
            'penerima.required'       => 'Penerima wajib diisi.',
            'penerima.max'            => 'Nama penerima maksimal 255 karakter.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'bukti_pengeluaran.file'  => 'Bukti pengeluaran harus berupa file.',
            'bukti_pengeluaran.mimes' => 'Format bukti pengeluaran: JPG, PNG, atau PDF.',
            'bukti_pengeluaran.max'   => 'Ukuran bukti pengeluaran maksimal 5 MB.',
        ];
    }
}

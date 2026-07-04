<?php

namespace App\Http\Requests\Produksi;

use Illuminate\Foundation\Http\FormRequest;

class InputHasilPotongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'potong';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'qty_pcs_potong' => 'required|integer|min:0',
            'alasan' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'qty_pcs_potong.required' => 'Jumlah PCS potong wajib diisi',
            'qty_pcs_potong.integer' => 'Jumlah PCS potong harus berupa angka',
            'qty_pcs_potong.min' => 'Jumlah PCS potong minimal 0',
            'alasan.string' => 'Alasan harus berupa teks',
            'alasan.max' => 'Alasan maksimal 500 karakter',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $detail = $this->route('detail');
            $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;
            $qty = $this->input('qty_pcs_potong');
            $alasan = $this->input('alasan');

            // Jika di bawah toleransi, alasan wajib diisi
            if ($qty < $batasBawah && empty($alasan)) {
                $validator->errors()->add('alasan', 'Alasan wajib diisi jika hasil potong di bawah batas toleransi (' . $batasBawah . ' PCS)');
            }
        });
    }
}

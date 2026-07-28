<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReversalHasilProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'catatan' => 'required|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'catatan.required' => 'Alasan koreksi harus diisi.',
            'catatan.max' => 'Alasan koreksi maksimal 500 karakter.'
        ];
    }
}

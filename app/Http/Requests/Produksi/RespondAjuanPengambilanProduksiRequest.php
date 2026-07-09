<?php

namespace App\Http\Requests\Produksi;

use Illuminate\Foundation\Http\FormRequest;

class RespondAjuanPengambilanProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['potong', 'jahit'], true);
    }

    public function rules(): array
    {
        return [
            'catatan_respon' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_respon.required' => 'Alasan penolakan wajib diisi.',
            'catatan_respon.string' => 'Alasan penolakan harus berupa teks.',
            'catatan_respon.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}

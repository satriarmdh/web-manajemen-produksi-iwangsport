<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class RejectPerintahProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $perintahProduksi = $this->route('perintahProduksi');

        return $this->user()?->role === 'owner'
            && $perintahProduksi?->status_produksi === 'pending';
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.string' => 'Alasan penolakan harus berupa teks.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}

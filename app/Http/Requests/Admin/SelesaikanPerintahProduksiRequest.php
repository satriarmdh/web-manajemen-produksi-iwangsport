<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SelesaikanPerintahProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'tgl_selesai' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tgl_selesai.date' => 'Format tanggal selesai tidak valid.',
        ];
    }
}

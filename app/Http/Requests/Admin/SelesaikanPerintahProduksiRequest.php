<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SelesaikanPerintahProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $perintahProduksi = $this->route('perintahProduksi');

        return $this->user()?->role === 'admin'
            && $perintahProduksi?->status_produksi === 'dalam_produksi';
    }

    public function rules(): array
    {
        return [
            'tgl_selesai' => ['required', 'date'],
        ];
    }
}

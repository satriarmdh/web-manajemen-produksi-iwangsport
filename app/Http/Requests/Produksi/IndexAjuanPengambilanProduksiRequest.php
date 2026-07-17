<?php

namespace App\Http\Requests\Produksi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAjuanPengambilanProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['potong', 'jahit', 'finishing'], true);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'sumber' => ['nullable', 'integer', 'exists:users,id'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'sort' => ['nullable', Rule::in(['fifo', 'wo_az', 'produk_az', 'qty_terbesar', 'qty_terkecil'])],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => trim((string) $this->input('search', '')),
            'sumber' => (string) $this->input('sumber', ''),
            'tanggal' => (string) $this->input('tanggal', ''),
            'sort' => (string) $this->input('sort', 'fifo'),
        ];
    }
}
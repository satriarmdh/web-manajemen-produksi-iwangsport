<?php

namespace App\Http\Requests\Produksi;

use App\Models\StokVirtual;
use Illuminate\Foundation\Http\FormRequest;

class StoreAjuanPengambilanProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['jahit', 'finishing'], true);
    }

    public function rules(): array
    {
        return [
            'stok_virtual_id' => ['required_without:items', 'nullable', 'exists:stok_virtual,id'],
            'qty_ajuan' => ['required_with:stok_virtual_id', 'nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array'],
            'items.*.stok_virtual_id' => ['required_with:items', 'exists:stok_virtual,id'],
            'items.*.qty_ajuan' => ['nullable', 'integer', 'min:1'],
            'catatan_pengaju' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'stok_virtual_id.required_without' => 'Barang ready yang ingin diambil wajib dipilih.',
            'stok_virtual_id.exists' => 'Barang ready tidak ditemukan.',
            'qty_ajuan.required_with' => 'Jumlah pengambilan wajib diisi.',
            'qty_ajuan.integer' => 'Jumlah pengambilan harus berupa angka.',
            'qty_ajuan.min' => 'Jumlah pengambilan minimal 1 pcs.',
            'items.array' => 'Daftar pengajuan tidak valid.',
            'items.*.stok_virtual_id.required_with' => 'Barang ready yang ingin diambil wajib dipilih.',
            'items.*.stok_virtual_id.exists' => 'Barang ready tidak ditemukan.',
            'items.*.qty_ajuan.integer' => 'Jumlah pengambilan harus berupa angka.',
            'items.*.qty_ajuan.min' => 'Jumlah pengambilan minimal 1 pcs.',
            'catatan_pengaju.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->ajuanItems();

            if (empty($items)) {
                $validator->errors()->add('items', 'Isi minimal satu jumlah pengajuan.');
                return;
            }

            $expectedSource = $this->user()->role === 'jahit' ? 'potong' : 'jahit';

            foreach ($items as $index => $item) {
                $stok = StokVirtual::find($item['stok_virtual_id']);
                if (! $stok) {
                    continue;
                }

                if ($stok->peran !== $expectedSource) {
                    abort(403);
                }

                $readyStock = $stok->peran === 'potong'
                    ? (int) $stok->qty_hold
                    : max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan);
                if ($readyStock <= 0) {
                    abort(403);
                }

                if ((int) $item['qty_ajuan'] > $readyStock) {
                    $errorKey = $this->filled('stok_virtual_id') ? 'qty_ajuan' : "items.{$index}.qty_ajuan";
                    $validator->errors()->add($errorKey, 'Jumlah pengambilan tidak boleh melebihi stok ready sumber.');
                }
            }
        });
    }

    public function ajuanItems(): array
    {
        if ($this->filled('stok_virtual_id')) {
            return [[
                'stok_virtual_id' => (int) $this->input('stok_virtual_id'),
                'qty_ajuan' => (int) $this->input('qty_ajuan'),
            ]];
        }

        return collect($this->input('items', []))
            ->filter(fn ($item) => isset($item['stok_virtual_id'], $item['qty_ajuan']) && (int) $item['qty_ajuan'] > 0)
            ->map(fn ($item) => [
                'stok_virtual_id' => (int) $item['stok_virtual_id'],
                'qty_ajuan' => (int) $item['qty_ajuan'],
            ])
            ->values()
            ->all();
    }
}

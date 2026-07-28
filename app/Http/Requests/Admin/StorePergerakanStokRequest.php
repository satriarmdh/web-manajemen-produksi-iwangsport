<?php

namespace App\Http\Requests\Admin;

use App\Models\BahanBaku;
use Illuminate\Foundation\Http\FormRequest;

class StorePergerakanStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $rules = [
            'jenis_pergerakan' => 'required|in:masuk,keluar',
            'tanggal' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string',
            'bukti' => 'nullable|file|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];

        if ($this->input('jenis_pergerakan') === 'masuk') {
            $rules['supplier_id'] = 'nullable|exists:suppliers,id';
        } else {
            $rules['penerima'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $jenis = $this->input('jenis_pergerakan');
            $items = $this->input('items', []);

            if (!is_array($items)) {
                return;
            }

            foreach ($items as $index => $item) {
                if (empty($item['bahan_baku_id'])) {
                    continue;
                }

                $bahan = BahanBaku::find($item['bahan_baku_id']);
                if (!$bahan) {
                    continue;
                }

                if (!$bahan->is_aktif) {
                    $validator->errors()->add("items.{$index}.bahan_baku_id", "Bahan baku {$bahan->nama_bahan} sedang tidak aktif.");
                }

                if ($jenis === 'keluar') {
                    $qty = (int) ($item['quantity'] ?? 0);
                    if ($qty > $bahan->stok) {
                        $validator->errors()->add("items.{$index}.quantity", "Stok {$bahan->nama_bahan} tidak mencukupi (Tersedia: {$bahan->stok} {$bahan->satuan}).");
                    }
                    
                    if ($bahan->kategori === 'kain') {
                        $validator->errors()->add("items.{$index}.bahan_baku_id", "Pengeluaran langsung tidak diperbolehkan untuk kategori kain.");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal harus menambahkan 1 item bahan baku.',
            'items.min' => 'Minimal harus menambahkan 1 item bahan baku.',
            'penerima.required' => 'Kolom penerima wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ];
    }
}

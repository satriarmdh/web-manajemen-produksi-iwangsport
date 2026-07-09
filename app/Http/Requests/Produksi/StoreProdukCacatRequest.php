<?php

namespace App\Http\Requests\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\StokVirtual;
use Illuminate\Foundation\Http\FormRequest;

class StoreProdukCacatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['potong', 'jahit', 'finishing'], true);
    }

    public function rules(): array
    {
        return [
            'detail_perintah_produksi_id' => ['required', 'exists:detail_perintah_produksi,id'],
            'qty_reject' => ['required', 'integer', 'min:1'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'tandai_selesai' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'detail_perintah_produksi_id.required' => 'Produk yang dilaporkan cacat wajib dipilih.',
            'detail_perintah_produksi_id.exists' => 'Produk pada perintah produksi tidak ditemukan.',
            'qty_reject.required' => 'Jumlah barang cacat wajib diisi.',
            'qty_reject.integer' => 'Jumlah barang cacat harus berupa angka.',
            'qty_reject.min' => 'Jumlah barang cacat minimal 1 pcs.',
            'keterangan.required' => 'Keterangan cacat wajib diisi agar admin dapat memvalidasi laporan.',
            'keterangan.string' => 'Keterangan cacat harus berupa teks.',
            'keterangan.max' => 'Keterangan cacat maksimal 1000 karakter.',
            'tandai_selesai.boolean' => 'Pilihan tandai selesai tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'detail_perintah_produksi_id' => 'produk',
            'qty_reject' => 'jumlah barang cacat',
            'keterangan' => 'keterangan cacat',
            'tandai_selesai' => 'tandai produk selesai',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('detail_perintah_produksi_id')) {
                return;
            }

            $detail = DetailPerintahProduksi::with('perintahProduksi')->find($this->input('detail_perintah_produksi_id'));

            if (! $detail) {
                return;
            }

            if (! in_array($detail->perintahProduksi?->status_produksi, ['disetujui', 'dalam_produksi'], true)) {
                abort(403);
            }

            if (in_array($this->user()->role, ['jahit', 'finishing'], true)) {
                $stokVirtual = StokVirtual::where('id_detail_perintah', $detail->id)
                    ->where('id_karyawan', $this->user()->id)
                    ->where('peran', $this->user()->role)
                    ->first();

                if (! $stokVirtual || (int) $stokVirtual->qty_hold <= 0) {
                    abort(403);
                }

                if ((int) $this->input('qty_reject') > (int) $stokVirtual->qty_hold) {
                    $validator->errors()->add('qty_reject', 'Jumlah barang cacat tidak boleh melebihi stok yang dipegang.');
                }
            }
        });
    }
}

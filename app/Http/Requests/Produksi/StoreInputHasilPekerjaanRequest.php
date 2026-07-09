<?php

namespace App\Http\Requests\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\StokVirtual;
use Illuminate\Foundation\Http\FormRequest;

class StoreInputHasilPekerjaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['potong', 'jahit', 'finishing'], true);
    }

    public function rules(): array
    {
        $rules = [
            'qty_selesai' => ['required', 'integer', 'min:1'],
            'tandai_selesai' => ['nullable', 'boolean'],
            'alasan' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->user()?->role === 'potong') {
            $rules['detail_perintah_produksi_id'] = ['required_without:stok_virtual_id', 'exists:detail_perintah_produksi,id'];
        }

        if (in_array($this->user()?->role, ['jahit', 'finishing'], true)) {
            if ($this->filled('detail_perintah_produksi_id') && ! $this->filled('stok_virtual_id')) {
                abort(403);
            }

            $rules['stok_virtual_id'] = ['required', 'exists:stok_virtual,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'qty_selesai.required' => 'Jumlah hasil selesai wajib diisi.',
            'qty_selesai.integer' => 'Jumlah hasil selesai harus berupa angka.',
            'qty_selesai.min' => 'Jumlah hasil selesai minimal 1 pcs.',
            'tandai_selesai.boolean' => 'Pilihan tandai selesai tidak valid.',
            'alasan.string' => 'Alasan harus berupa teks.',
            'alasan.max' => 'Alasan maksimal 1000 karakter.',
            'detail_perintah_produksi_id.required_without' => 'Produk yang akan diinput hasilnya wajib dipilih.',
            'detail_perintah_produksi_id.exists' => 'Produk pada perintah produksi tidak ditemukan.',
            'stok_virtual_id.required' => 'Stok pegangan wajib dipilih untuk input hasil tahap ini.',
            'stok_virtual_id.exists' => 'Stok pegangan tidak ditemukan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'qty_selesai' => 'jumlah hasil selesai',
            'tandai_selesai' => 'tandai produk selesai',
            'alasan' => 'alasan',
            'detail_perintah_produksi_id' => 'produk',
            'stok_virtual_id' => 'stok pegangan',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $qtySelesai = (int) $this->input('qty_selesai');

            if ($this->user()?->role === 'potong' && $this->filled('detail_perintah_produksi_id')) {
                $detail = DetailPerintahProduksi::with('perintahProduksi')->find($this->input('detail_perintah_produksi_id'));

                if (! $detail) {
                    return;
                }

                $status = $detail->perintahProduksi?->status_produksi;
                if (! in_array($status, ['disetujui', 'dalam_produksi'], true)) {
                    abort(403);
                }

                $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;
                $totalSebelumnya = (int) ($detail->qty_pcs_potong ?? 0);
                $totalReject = (int) (StokVirtual::where('id_detail_perintah', $detail->id)
                    ->where('id_karyawan', $this->user()->id)
                    ->where('peran', $this->user()->role)
                    ->value('total_reject') ?? 0);
                $totalSetelahInput = $totalSebelumnya + $qtySelesai + $totalReject;
                $ditandaiSelesai = $this->boolean('tandai_selesai');

                if ($ditandaiSelesai && $totalSetelahInput < $batasBawah && ! $this->filled('alasan')) {
                    $validator->errors()->add('alasan', 'Alasan wajib diisi jika produk ditandai selesai tetapi total hasil masih di bawah batas toleransi.');
                }
            }

            if (in_array($this->user()?->role, ['jahit', 'finishing'], true) && $this->filled('stok_virtual_id')) {
                $stokVirtual = StokVirtual::find($this->input('stok_virtual_id'));

                if (! $stokVirtual) {
                    return;
                }

                if ($stokVirtual->id_karyawan !== $this->user()->id || $stokVirtual->peran !== $this->user()->role) {
                    abort(403);
                }

                if ($qtySelesai > $stokVirtual->qty_hold) {
                    $validator->errors()->add('qty_selesai', 'Qty selesai tidak boleh melebihi qty yang dipegang.');
                }
            }
        });
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => [
                'required',
                'integer',
                Rule::exists('pelanggan', 'id')->where('is_aktif', true),
            ],
            'tanggal' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.produk_id' => [
                'required',
                'integer',
                Rule::exists('produk', 'id')->where('is_aktif', true),
            ],
            'items.*.qty' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $produkId = $this->input("items.{$index}.produk_id");

                    if ($produkId) {
                        // Exclude qty from this penjualan's old items (stok akan dikembalikan)
                        $penjualanId = $this->route('penjualan')->id ?? null;
                        $produk = Produk::find($produkId);

                        if ($produk) {
                            // Stok tersedia = stok saat ini + qty dari detail lama untuk produk ini
                            $oldQty = \App\Models\DetailPenjualan::where('penjualan_id', $penjualanId)
                                ->where('produk_id', $produkId)
                                ->sum('qty');

                            $availableStok = $produk->stok + $oldQty;

                            if ($value > $availableStok) {
                                $fail("Qty ({$value}) melebihi stok tersedia \"{$produk->nama_produk} {$produk->warna}\" ({$availableStok}).");
                            }
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan harus dipilih.',
            'pelanggan_id.exists' => 'Pelanggan tidak valid atau non-aktif.',
            'tanggal.required' => 'Tanggal penjualan harus diisi.',
            'tanggal.date' => 'Tanggal penjualan tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal penjualan tidak boleh di masa depan.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
            'items.required' => 'Minimal 1 item produk harus diisi.',
            'items.array' => 'Format items tidak valid.',
            'items.min' => 'Minimal 1 item produk harus diisi.',
            'items.*.produk_id.required' => 'Produk harus dipilih.',
            'items.*.produk_id.exists' => 'Produk tidak valid atau non-aktif.',
            'items.*.qty.required' => 'Qty harus diisi.',
            'items.*.qty.integer' => 'Qty harus berupa angka.',
            'items.*.qty.min' => 'Qty minimal 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'pelanggan_id' => 'pelanggan',
            'tanggal' => 'tanggal penjualan',
            'catatan' => 'catatan',
            'items' => 'items',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Produk;
use App\Models\BahanBaku;

class StoreStandardBaselineProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produk_id'      => 'required|exists:produk,id',
            'bahan_baku_id'  => 'required|exists:bahan_baku,id|unique:standard_baseline_produksi,bahan_baku_id,NULL,id,produk_id,' . $this->produk_id,
            'pcs_per_roll'   => 'required|integer|min:1',
            'toleransi_minus' => 'nullable|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
            'is_aktif'       => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'toleransi_minus' => $this->filled('toleransi_minus') ? (int) $this->input('toleransi_minus') : 0,
        ]);
    }

    public function messages(): array
    {
        return [
            'produk_id.required' => 'Produk harus dipilih.',
            'produk_id.exists' => 'Produk yang dipilih tidak valid.',
            'bahan_baku_id.required' => 'Bahan baku harus dipilih.',
            'bahan_baku_id.exists' => 'Bahan baku yang dipilih tidak valid.',
            'bahan_baku_id.unique' => 'Kombinasi produk dan bahan baku ini sudah ada.',
            'pcs_per_roll.required' => 'Jumlah pcs per roll harus diisi.',
            'pcs_per_roll.integer' => 'Jumlah pcs per roll harus berupa angka.',
            'pcs_per_roll.min' => 'Jumlah pcs per roll minimal 1.',
            'toleransi_minus.integer' => 'Toleransi minus harus berupa angka.',
            'toleransi_minus.min' => 'Toleransi minus tidak boleh negatif.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'is_aktif.boolean' => 'Status aktif tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->produk_id && $this->bahan_baku_id) {
                $produk = Produk::find($this->produk_id);
                $bahanBaku = BahanBaku::find($this->bahan_baku_id);

                if ($produk && $bahanBaku) {
                    $warnaProduk = strtolower(trim($produk->warna));
                    $warnaBahan = strtolower(trim($bahanBaku->warna));

                    if ($warnaProduk === 'abu') {
                        $warnaProduk = 'abu-abu';
                    }
                    if ($warnaBahan === 'abu') {
                        $warnaBahan = 'abu-abu';
                    }

                    if ($warnaProduk !== $warnaBahan) {
                        $validator->errors()->add('bahan_baku_id', 'Warna bahan baku (' . $bahanBaku->warna . ') tidak cocok dengan warna produk (' . $produk->warna . ').');
                    }
                }
            }
        });
    }
}

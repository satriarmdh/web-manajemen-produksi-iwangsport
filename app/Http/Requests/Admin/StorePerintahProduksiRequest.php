<?php

namespace App\Http\Requests\Admin;

use App\Models\StandardBaselineProduksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePerintahProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'tgl_mulai' => 'required|date|after_or_equal:today',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'details.*.qty_roll_pakai' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_mulai.required' => 'Tanggal mulai wajib diisi',
            'tgl_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid',
            'tgl_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
            'tgl_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid',
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai',
            'details.required' => 'Detail produk wajib diisi',
            'details.array' => 'Detail produk harus berupa array',
            'details.min' => 'Minimal harus ada 1 detail produk',
            'details.*.produk_id.required' => 'Produk wajib dipilih',
            'details.*.produk_id.exists' => 'Produk tidak valid',
            'details.*.bahan_baku_id.required' => 'Bahan baku wajib dipilih',
            'details.*.bahan_baku_id.exists' => 'Bahan baku tidak valid',
            'details.*.qty_roll_pakai.required' => 'Jumlah roll wajib diisi',
            'details.*.qty_roll_pakai.integer' => 'Jumlah roll harus berupa angka',
            'details.*.qty_roll_pakai.min' => 'Jumlah roll minimal 1',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateDetailCombinations($validator);
        });
    }

    private function validateDetailCombinations(Validator $validator): void
    {
        $details = $this->input('details', []);

        if (!is_array($details)) {
            return;
        }

        $usedCombinations = [];

        foreach ($details as $index => $detail) {
            $produkId = $detail['produk_id'] ?? null;
            $bahanBakuId = $detail['bahan_baku_id'] ?? null;

            if (!$produkId || !$bahanBakuId) {
                continue;
            }

            $combinationKey = $produkId . '-' . $bahanBakuId;
            if (in_array($combinationKey, $usedCombinations, true)) {
                $validator->errors()->add("details.{$index}.produk_id", 'Kombinasi produk dan bahan baku tidak boleh duplikat dalam satu perintah produksi');
                continue;
            }

            $usedCombinations[] = $combinationKey;

            $baselineExists = StandardBaselineProduksi::where('produk_id', $produkId)
                ->where('bahan_baku_id', $bahanBakuId)
                ->where('is_aktif', true)
                ->exists();

            if (!$baselineExists) {
                $validator->errors()->add("details.{$index}.bahan_baku_id", 'Standard baseline untuk kombinasi produk dan bahan baku ini belum tersedia atau tidak aktif');
            }
        }
    }
}

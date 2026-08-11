<?php

namespace App\Http\Requests\Admin;

use App\Models\DetailPerintahProduksi;
use App\Models\StokVirtual;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePenerimaanHasilProduksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'perintah_produksi_detail_id' => [
                'required',
                'integer',
                Rule::exists('detail_perintah_produksi', 'id')
            ],
            'jenis_penerimaan' => [
                'nullable',
                'string',
                'in:baik,cacat'
            ],
            'dari_karyawan_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                function ($attribute, $value, $fail) {
                    $detailId = $this->input('perintah_produksi_detail_id');
                    $jenisPenerimaan = $this->input('jenis_penerimaan', 'baik');
                    
                    $stokVirtual = StokVirtual::where([
                        'id_detail_perintah' => $detailId,
                        'id_karyawan' => $value,
                    ])->first();

                    if (!$stokVirtual) {
                        $fail('Karyawan tidak memiliki catatan stok untuk produk ini.');
                        return;
                    }

                    $qtyDiterima = (int) $this->input('qty_diterima');

                    if ($jenisPenerimaan === 'cacat') {
                        // Hitung sisa reject yang belum diserahkan
                        $deliveredReject = (int) \App\Models\PenerimaanHasilProduksi::where([
                            'perintah_produksi_detail_id' => $detailId,
                            'dari_karyawan_id' => $value,
                            'jenis_penerimaan' => 'cacat',
                        ])->sum('qty_diterima');

                        $qtySisa = (int) $stokVirtual->total_reject - $deliveredReject;

                        if ($qtySisa <= 0) {
                            $fail('Karyawan tidak memiliki barang cacat/reject yang belum diserahkan.');
                            return;
                        }

                        if ($qtyDiterima && $qtyDiterima > $qtySisa) {
                            $fail("Qty cacat diterima ({$qtyDiterima}) melebihi barang cacat yang belum diserahkan ({$qtySisa}).");
                        }
                    } else {
                        // Hitung sisa yang belum diserahkan: total_selesai - total_dikeluarkan
                        $qtySisa = (int) $stokVirtual->total_selesai - (int) $stokVirtual->total_dikeluarkan;

                        if ($qtySisa <= 0) {
                            $fail('Karyawan tidak memiliki stok yang tersedia untuk diserahkan. Semua stok sudah diserahkan.');
                            return;
                        }

                        // Validate: qty_diterima tidak melebihi sisa yang belum diserahkan
                        if ($qtyDiterima && $qtyDiterima > $qtySisa) {
                            $fail("Qty diterima ({$qtyDiterima}) melebihi stok yang belum diserahkan ({$qtySisa}). Total selesai: {$stokVirtual->total_selesai}, sudah diserahkan: {$stokVirtual->total_dikeluarkan}.");
                        }
                    }
                }
            ],
            'qty_diterima' => [
                'required',
                'integer',
                'min:1',
            ],
            'tanggal_terima' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:500'
            ],
            'bukti_foto' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048' // 2MB max
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'perintah_produksi_detail_id.required' => 'Detail perintah produksi harus dipilih.',
            'perintah_produksi_detail_id.exists' => 'Detail perintah produksi tidak valid.',
            'dari_karyawan_id.required' => 'Karyawan harus dipilih.',
            'dari_karyawan_id.exists' => 'Karyawan tidak valid.',
            'qty_diterima.required' => 'Qty diterima harus diisi.',
            'qty_diterima.integer' => 'Qty diterima harus berupa angka.',
            'qty_diterima.min' => 'Qty diterima minimal 1.',
            'tanggal_terima.required' => 'Tanggal terima harus diisi.',
            'tanggal_terima.date' => 'Tanggal terima tidak valid.',
            'tanggal_terima.before_or_equal' => 'Tanggal terima tidak boleh di masa depan.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
            'bukti_foto.required' => 'Bukti foto harus diupload.',
            'bukti_foto.image' => 'File harus berupa gambar.',
            'bukti_foto.mimes' => 'Format foto harus jpeg, jpg, atau png.',
            'bukti_foto.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'perintah_produksi_detail_id' => 'detail perintah produksi',
            'dari_karyawan_id' => 'karyawan',
            'qty_diterima' => 'qty diterima',
            'tanggal_terima' => 'tanggal terima',
            'catatan' => 'catatan',
            'bukti_foto' => 'bukti foto',
        ];
    }
}

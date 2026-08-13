<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranPenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'tanggal_bayar' => ['required', 'date', 'before_or_equal:now'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'metode_pembayaran' => ['required', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi.',
            'tanggal_bayar.date' => 'Format tanggal pembayaran tidak valid.',
            'tanggal_bayar.before_or_equal' => 'Tanggal & waktu pembayaran tidak boleh melebihi waktu saat ini.',
            'jumlah_bayar.required' => 'Nominal pembayaran wajib diisi.',
            'jumlah_bayar.numeric' => 'Nominal pembayaran harus berupa angka.',
            'jumlah_bayar.min' => 'Nominal pembayaran minimal Rp 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran harus berupa tunai atau transfer.',
            'catatan.max' => 'Catatan maksimal 255 karakter.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa berkas gambar.',
            'bukti_pembayaran.mimes' => 'Format bukti pembayaran harus jpeg, jpg, png, atau webp.',
            'bukti_pembayaran.max' => 'Ukuran berkas bukti pembayaran maksimal 5MB.',
        ];
    }
}

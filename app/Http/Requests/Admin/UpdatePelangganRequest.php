<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pelangganId = $this->route('pelanggan')?->id ?? $this->route('pelanggan');

        return [
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:pelanggan,email,' . $pelangganId],
            'alamat' => ['required', 'string', 'max:500'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'is_aktif' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'nama_pelanggan.string'   => 'Nama pelanggan harus berupa teks.',
            'nama_pelanggan.max'      => 'Nama pelanggan maksimal 255 karakter.',
            'no_telp.required'        => 'Nomor telepon wajib diisi.',
            'no_telp.string'          => 'Nomor telepon harus berupa teks.',
            'no_telp.regex'           => 'Format nomor telepon tidak valid.',
            'no_telp.max'             => 'Nomor telepon maksimal 20 karakter.',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'email.max'               => 'Email maksimal 255 karakter.',
            'email.unique'            => 'Email sudah terdaftar.',
            'alamat.required'         => 'Alamat wajib diisi.',
            'alamat.string'           => 'Alamat harus berupa teks.',
            'alamat.max'              => 'Alamat maksimal 500 karakter.',
            'keterangan.string'       => 'Keterangan harus berupa teks.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'is_aktif.boolean'        => 'Status aktif tidak valid.',
        ];
    }
}

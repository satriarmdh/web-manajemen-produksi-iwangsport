<?php

namespace App\Http\Requests\Owner;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->has('password')) {
            $this->merge([
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->withoutTrashed()
            ],
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,potong,jahit,finishing',
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ];
    }

    /**
     * Kustomisasi pesan error validasi bahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            
            'role.required' => 'Peran atau hak akses wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid.',
            
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',
            
            'alamat.max' => 'Alamat terlalu panjang, maksimal 500 karakter.',
            'no_hp.max' => 'Nomor HP terlalu panjang, maksimal 20 karakter.',
        ];
    }
}

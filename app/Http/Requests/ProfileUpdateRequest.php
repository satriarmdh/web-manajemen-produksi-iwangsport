<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $emailChanged = $this->filled('email') && $this->input('email') !== $user->email;
        $passwordChanged = $this->filled('password');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan,Laki-laki,Perempuan'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];

        if ($emailChanged || $passwordChanged) {
            $rules['current_password'] = ['required', 'current_password'];
        } else {
            $rules['current_password'] = ['nullable'];
        }

        if ($passwordChanged) {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string'   => 'Nama lengkap harus berupa teks.',
            'name.max'      => 'Nama lengkap maksimal 255 karakter.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.max'      => 'Email maksimal 255 karakter.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
            
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin yang dipilih tidak valid.',
            
            'no_hp.max' => 'Nomor HP terlalu panjang, maksimal 20 karakter.',
            'alamat.max' => 'Alamat terlalu panjang, maksimal 500 karakter.',
            
            'current_password.required' => 'Password saat ini wajib diisi untuk mengubah email atau password.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min'       => 'Password baru minimal harus 8 karakter.',
        ];
    }
}

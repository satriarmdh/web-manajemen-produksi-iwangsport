<?php

namespace App\Http\Requests\Owner;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Menangkap objek user dari route untuk mengecualikan emailnya sendiri dari validasi unique
        $user = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->ignore($user->id)->withoutTrashed()
            ],
            'role' => 'required|in:admin,potong,jahit,finishing,owner',
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            
            'role.required' => 'Peran atau hak akses wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid.',
            
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',
            
            'alamat.max' => 'Alamat terlalu panjang, maksimal 500 karakter.',
            'no_hp.max' => 'Nomor HP terlalu panjang, maksimal 20 karakter.',
        ];
    }
}

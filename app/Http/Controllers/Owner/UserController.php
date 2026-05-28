<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('owner.manajemen-pengguna.index', compact('users')); 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,potong,jahit,finishing',
        ]);

        // Enkripsi password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect('/owner/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,potong,jahit,finishing,owner',
        ]);

        $user->update($validated);

        return redirect('/owner/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect('/owner/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
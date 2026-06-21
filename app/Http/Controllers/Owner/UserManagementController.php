<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use App\Http\Requests\Owner\StoreUserRequest;
use App\Http\Requests\Owner\UpdateUserRequest;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // protected UserManagementService $userManagementService;

    // // Inject UserService ke dalam controller
    // public function __construct(UserManagementService $userManagementService)
    // {
    //     $this->userManagementService = $userManagementService;
    // }

    public function __construct(
        protected UserManagementService $userManagementService
    ) {}

    public function index(Request $request)
    {
        $users = $this->userManagementService->getUsersPaginated([
            'search' => $request->search,
            'role' => $request->role,
            'sort' => $request->sort,
        ]);
        
        return view('owner.manajemen-pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('owner.manajemen-pengguna.create');
    }

    // Menampilkan halaman form edit pengguna
    public function edit(User $user)
    {
        return view('owner.manajemen-pengguna.edit', compact('user'));
    }

    public function store(StoreUserRequest $request)
    {
        // Validasi otomatis berjalan di StoreUserRequest
        $this->userManagementService->createUser($request->validated());

        return redirect('/owner/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userManagementService->updateUser($user, $request->validated());

        return redirect('/owner/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->userManagementService->deleteUser($user);

        return redirect('/owner/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
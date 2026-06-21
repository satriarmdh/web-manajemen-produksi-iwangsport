<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Pengaturan Sistem</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Manajemen Pengguna
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Manajemen Pengguna
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Karyawan & Akses</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data pengguna, peran, dan hak akses ke dalam sistem.</p>
            </div>
            <a href="{{ route('owner.users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
                Tambah Pengguna
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Informasi Pengguna</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Peran / Hak Akses</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Terakhir Dilihat</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#0F034D] flex items-center justify-center text-white font-bold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $badges = [
                                        'owner' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'admin' => 'bg-rose-100 text-rose-700 border-rose-200',
                                        'potong' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'jahit' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'finishing' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    ];
                                    $badgeClass = $badges[$user->role] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full {{ $user->online_status ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></div>
                                    <span class="text-xs {{ $user->online_status ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                        {{ $user->online_status ? 'Sedang Aktif' : 'Offline' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($user->online_status)
                                        <span class="text-xs font-medium text-gray-400">-</span>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            {{ $user->last_seen ? \Carbon\Carbon::parse($user->last_seen)->diffForHumans() : '-' }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    
                                    <a href="{{ route('owner.users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('owner.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Perhatian!\n\nApakah Anda yakin ingin menghapus akun {{ $user->name }} secara permanen?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-gray-500 font-medium">Belum ada pengguna terdaftar.</p>
                                    <p class="text-sm text-gray-400 mt-1">Silakan klik Tambah Pengguna untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                <x-pagination.custom-global-pagination :paginator="$users" />
            </div>
        @endif
    </div>
</x-layouts.owner>
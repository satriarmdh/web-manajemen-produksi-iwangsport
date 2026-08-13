@php
    $role = auth()->user()->role;
    $layoutComponent = match($role) {
        'admin' => 'layouts.admin',
        'owner' => 'layouts.owner',
        default => 'layouts.produksi',
    };
    $dashboardRoute = match($role) {
        'admin' => route('admin.dashboard'),
        'owner' => route('owner.dashboard'),
        default => route('produksi.dashboard'),
    };
@endphp

<x-dynamic-component :component="$layoutComponent">
    @if(in_array($role, ['admin', 'owner']))
        <x-slot:breadcrumb>
            <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
                <svg class="w-4 h-4 shrink-0 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Daftar Notifikasi
            </li>
        </x-slot:breadcrumb>

        <x-slot:header>
            Pusat Notifikasi
        </x-slot:header>
    @else
        <x-slot:header>
            Pusat Notifikasi
        </x-slot:header>
    @endif

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-900 font-bold ml-4">&times;</button>
            </div>
        @endif

        {{-- Header & Action Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#0F034D]">Semua Notifikasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola dan lihat seluruh pemberitahuan aktivitas sistem Anda</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>

                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-[#0F034D] text-xs font-semibold rounded-xl border border-blue-100 transition-colors cursor-pointer shadow-sm">
                                <svg class="w-4 h-4 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Semua Sudah Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tabs Filter --}}
            <div class="flex items-center gap-2 pt-3 border-t border-gray-100 overflow-x-auto">
                <a href="{{ route('notifications.page', ['filter' => 'all']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filter === 'all' ? 'bg-[#0F034D] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $totalCount }}</span>
                </a>
                <a href="{{ route('notifications.page', ['filter' => 'unread']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filter === 'unread' ? 'bg-[#0F034D] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Belum Dibaca
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'unread' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $unreadCount }}</span>
                </a>
                <a href="{{ route('notifications.page', ['filter' => 'read']) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filter === 'read' ? 'bg-[#0F034D] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Sudah Dibaca
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'read' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $readCount }}</span>
                </a>
            </div>
        </div>

        {{-- Notification List --}}
        <div class="space-y-3">
            @forelse($notifications as $notification)
                @php 
                    $d = $notification->data; 
                    $isUnread = !$notification->read_at;
                    $targetUrl = $d['url'] ?? '#';
                @endphp
                <div class="bg-white rounded-2xl border transition-all duration-200 overflow-hidden shadow-sm hover:shadow-md {{ $isUnread ? 'border-blue-200 bg-blue-50/20' : 'border-gray-100' }}">
                    <div class="p-4 sm:p-5 flex items-start gap-4">
                        {{-- Status Dot Indicator --}}
                        <div class="mt-1 shrink-0 flex items-center justify-center">
                            <span class="w-3 h-3 rounded-full {{ $isUnread ? 'bg-[#0F034D] ring-4 ring-blue-100' : 'bg-gray-300' }}"></span>
                        </div>

                        {{-- Main Content --}}
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h4 class="text-sm font-bold {{ $isUnread ? 'text-[#0F034D]' : 'text-gray-800' }} truncate">
                                    {{ $d['title'] ?? 'Pemberitahuan Sistem' }}
                                </h4>
                                <span class="text-[11px] font-medium text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('d M Y, H:i') }})
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 leading-relaxed">
                                {{ $d['message'] ?? '' }}
                            </p>

                            @if($targetUrl && $targetUrl !== '#')
                                <div class="pt-2 flex items-center gap-3">
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline-block" onsubmit="window.location.href='{{ $targetUrl }}';">
                                        @csrf
                                        <a href="{{ $targetUrl }}" onclick="event.preventDefault(); markAndRedirect('{{ $notification->id }}', '{{ $targetUrl }}')" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0F034D] hover:underline">
                                            Buka Tautan Tinjauan
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Mark single read button if unread --}}
                        @if($isUnread)
                            <button type="button" onclick="markSingleAsRead('{{ $notification->id }}', this)" title="Tandai Sudah Dibaca" class="shrink-0 p-1.5 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0F034D] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Tidak ada notifikasi</h3>
                    <p class="text-xs text-gray-400 mt-1">Belum ada pemberitahuan pada kategori filter ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';

        async function markSingleAsRead(id, btnElement) {
            try {
                const res = await fetch('/api/notifications/' + id + '/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function markAndRedirect(id, targetUrl) {
            try {
                await fetch('/api/notifications/' + id + '/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
            } catch (e) {
                console.error(e);
            } finally {
                window.location.href = targetUrl;
            }
        }
    </script>
</x-dynamic-component>

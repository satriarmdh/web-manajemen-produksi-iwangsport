{{-- Notification Dropdown Partial - rendered via fetch --}}
<div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
    <h3 class="text-sm font-bold text-[#0F034D]">Notifikasi</h3>
    @if($unreadCount > 0)
        <button id="notification-mark-all" class="text-xs font-medium text-[#0F034D] hover:underline cursor-pointer">Tandai sudah dibaca</button>
    @endif
</div>
<div class="overflow-y-auto flex-1 divide-y divide-gray-50" style="max-height: 380px;">
    @forelse($notifications as $notification)
        @php $d = $notification->data; @endphp
        <a href="#"
           data-id="{{ $notification->id }}"
           data-url="{{ $d['url'] ?? '#' }}"
           class="notif-item flex items-center gap-3 px-4 py-3 transition-colors {{ !$notification->read_at ? 'bg-blue-50/40 hover:bg-blue-50' : 'hover:bg-gray-50' }}">
            <div class="shrink-0 w-2.5 h-2.5 rounded-full {{ !$notification->read_at ? 'bg-[#0F034D]' : 'bg-transparent' }}"></div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold {{ !$notification->read_at ? 'text-gray-900' : 'text-gray-500' }} truncate">{{ $d['title'] ?? '-' }}</p>
                <p class="text-xs text-gray-400 mt-0.5" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $d['message'] ?? '' }}</p>
                <p class="text-[10px] text-gray-300 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
        </a>
    @empty
        <div class="p-6 text-center text-sm text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Tidak ada notifikasi
        </div>
    @endforelse
</div>
<div class="p-2.5 border-t border-gray-100 bg-gray-50/70 text-center shrink-0">
    <a href="{{ route('notifications.page') }}" id="notification-view-all" class="block w-full py-1.5 text-xs font-bold text-[#0F034D] hover:text-[#0a0235] transition-colors">
        Lihat Selengkapnya →
    </a>
</div>

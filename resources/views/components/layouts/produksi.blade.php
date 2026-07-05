<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Karyawan Produksi | Iwangsport' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { margin: 0; padding: 0; min-height: 100%; }
        body { overflow-x: hidden; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-[#0F034D] min-h-screen">
    @php
        $role = auth()->user()->role;
        $roleLabels = [
            'potong' => 'Tukang Potong',
            'jahit' => 'Penjahit',
            'finishing' => 'Finishing',
        ];
        $roleLabel = $roleLabels[$role] ?? 'Karyawan Produksi';
        $isDashboard = request()->routeIs('produksi.dashboard');
        $isPekerjaan = request()->routeIs('produksi.perintah-produksi.*');
        $isAjuan = request()->routeIs('produksi.ajuan-pengambilan.*');
        $navItems = [
            [
                'label' => 'Dashboard',
                'route' => route('produksi.dashboard'),
                'active' => $isDashboard,
                'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            ],
            [
                'label' => 'Pekerjaan',
                'route' => route('produksi.perintah-produksi.index'),
                'active' => $isPekerjaan,
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],

            [
                'label' => 'Ajuan',
                'route' => route('produksi.ajuan-pengambilan.index'),
                'active' => $isAjuan,
                'icon' => 'M8 7h8m-8 4h8m-8 4h5M5 5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5z',
            ],
        ];
    @endphp

    <div class="min-h-screen lg:flex">
        <aside class="hidden lg:flex lg:w-72 lg:shrink-0 bg-white border-r border-gray-100 min-h-screen flex-col fixed inset-y-0 left-0 z-30">
            <div class="h-20 px-6 flex items-center gap-3 border-b border-gray-100">
                <img src="{{ asset('images/logo-primary.png') }}" alt="Logo" class="w-9 h-9 rounded-full object-cover">
                <div>
                    <p class="text-lg font-bold tracking-tight">Iwangsport</p>
                    <p class="text-xs text-gray-500">Panel Produksi</p>
                </div>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                @foreach($navItems as $item)
                    <a href="{{ $item['route'] }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ $item['active'] ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-[#0F034D] hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 shrink-0 {{ $item['active'] ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}"></path></svg>
                        <span class="font-medium text-sm">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 lg:ml-72 min-h-screen pb-24 lg:pb-0">
            <header class="sticky top-0 inset-x-0 z-40 bg-white border-b border-gray-100 shadow-sm shadow-gray-200/40">
                <div class="px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-[#0F034D] truncate">{{ $header ?? 'Panel Produksi' }}</h1>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-semibold text-[#0F034D]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ $roleLabel }}</p>
                        </div>
                        <div class="relative">
                            <button id="profile-btn" type="button" onclick="toggleProfileDropdown()" class="flex items-center gap-2 hover:bg-gray-50 p-1 rounded-full transition-colors cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-[#0F034D] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block transition-transform duration-200" id="profile-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div id="profile-dropdown" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-right">
                                <div class="p-4 border-b border-gray-50">
                                    <p class="text-sm font-bold text-[#0F034D] truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                    <span class="mt-2 inline-flex items-center px-2 py-1 rounded-lg bg-[#0F034D]/5 text-[#0F034D] text-[11px] font-semibold">{{ $roleLabel }}</span>
                                </div>
                                <div class="p-2">
                                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Profile Settings
                                    </a>
                                </div>
                                <div class="p-2 border-t border-gray-50">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t border-gray-100 shadow-[0_-10px_30px_rgba(15,3,77,0.08)]">
        <div class="grid grid-cols-3 px-2 pt-2 pb-[calc(0.5rem+env(safe-area-inset-bottom))]">
            @foreach($navItems as $item)
                <a href="{{ $item['route'] }}" class="flex flex-col items-center justify-center gap-1 py-2 rounded-2xl transition-all {{ $item['active'] ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}"></path></svg>
                    <span class="text-[11px] font-semibold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            const arrow = document.getElementById('profile-arrow');
            const isOpen = dropdown.classList.contains('opacity-100');

            if (isOpen) {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                arrow?.classList.remove('rotate-180');
            } else {
                dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                dropdown.classList.add('opacity-100', 'scale-100');
                arrow?.classList.add('rotate-180');
            }
        }

        document.addEventListener('click', function (event) {
            const button = document.getElementById('profile-btn');
            const dropdown = document.getElementById('profile-dropdown');
            const arrow = document.getElementById('profile-arrow');

            if (!button || !dropdown) return;

            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                arrow?.classList.remove('rotate-180');
            }
        });
    </script>
</body>
</html>

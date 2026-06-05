{{-- ====== LAYOUT ====== --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Owner Panel | Iwangsport' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans antialiased text-[#0F034D] bg-gray-50 flex h-screen overflow-hidden">

    <div id="mobile-overlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-gray-900/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden cursor-pointer"></div>

    <aside id="sidebar" class="fixed md:relative z-50 md:z-0 -translate-x-full md:translate-x-0 w-72 shrink-0 bg-white border-r border-gray-100 flex flex-col h-full shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-all duration-300 overflow-hidden">
        
        <div id="sidebar-header" class="flex flex-row items-center justify-between h-20 px-6 border-b border-gray-50 transition-all duration-300">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-primary.png') }}" alt="Logo" class="w-8 h-8 rounded-full object-cover shrink-0">
                <span class="sidebar-text text-xl font-bold tracking-tight whitespace-nowrap">Iwangsport</span>
            </div>
            
            <button onclick="toggleSidebar()" class="hidden md:flex shrink-0 w-8 h-8 items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:text-[#0F034D] hover:border-[#0F034D] transition-colors cursor-pointer">
                <svg id="sidebar-toggle-icon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <button onclick="toggleMobileSidebar()" class="md:hidden shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:text-red-500 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto no-scrollbar px-4 py-6 space-y-2">
            
            @php $isDashboard = request()->routeIs('owner.dashboard'); @endphp
            <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ $isDashboard ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-[#0F034D] hover:bg-gray-100' }}" title="Dashboard Inventori">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="sidebar-text font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>

            @php $isProduksiGroup = request()->routeIs('owner.persetujuan-workorder', 'owner.pantau-progres'); @endphp
            <div>
                <button onclick="toggleMenu('menu-produksi', 'icon-produksi')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isProduksiGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-[#0F034D] hover:bg-gray-100' }}" title="Produksi & Persetujuan">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isProduksiGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isProduksiGroup ? 'font-bold' : '' }}">Produksi & Persetujuan</span>
                    </div>
                    <svg id="icon-produksi" class="sidebar-text shrink-0 w-4 h-4 transition-transform duration-300 transform {{ $isProduksiGroup ? 'rotate-180 text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div id="menu-produksi" class="overflow-hidden transition-all duration-300 {{ $isProduksiGroup ? 'max-h-96' : 'max-h-0' }}">
                    <ul class="relative ml-6 pl-4 mt-1 mb-2 space-y-1">
                        
                        <li>
                            @php 
                                $isWorkOrder = request()->routeIs('owner.persetujuan-workorder'); 
                                $isPantau = request()->routeIs('owner.pantau-progress'); 
                            @endphp
                            <a href="#" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isWorkOrder ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 border-l-2 transition-colors {{ $isPantau ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isWorkOrder ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isWorkOrder ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Persetujuan Work Order
                            </a>
                        </li>
                        
                        <li>
                            <a href="#" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isPantau ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isPantau ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isPantau ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                Pantau Progres Produksi
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            @php $isLaporanGroup = request()->routeIs('owner.log-mutasi', 'owner.riwayat-penjualan'); @endphp
            <div>
                <button onclick="toggleMenu('menu-laporan', 'icon-laporan')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isLaporanGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-[#0F034D] hover:bg-gray-100' }}" title="Laporan & Riwayat">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isLaporanGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isLaporanGroup ? 'font-bold' : '' }}">Laporan & Riwayat</span>
                    </div>
                    <svg id="icon-laporan" class="sidebar-text shrink-0 w-4 h-4 transition-transform duration-300 transform {{ $isLaporanGroup ? 'rotate-180 text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div id="menu-laporan" class="overflow-hidden transition-all duration-300 {{ $isLaporanGroup ? 'max-h-96' : 'max-h-0' }}">
                    <ul class="relative ml-6 pl-4 mt-1 mb-2 space-y-1">
                        <li>
                            @php 
                                $isMutasi = request()->routeIs('owner.log-mutasi'); 
                                $isRiwayatJual = request()->routeIs('owner.riwayat-penjualan');
                            @endphp
                            <a href="#" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isMutasi ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                
                                <span class="absolute -left-4 -top-2 -bottom-2 border-l-2 transition-colors {{ $isRiwayatJual ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isMutasi ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isMutasi ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                
                                Log Mutasi Bahan Baku
                            </a>
                        </li>
                        <li>
                            <a href="#" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isRiwayatJual ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isRiwayatJual ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isRiwayatJual ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                Riwayat Penjualan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            @php $isPengaturanGroup = request()->routeIs('owner.users.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-pengaturan', 'icon-pengaturan')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isPengaturanGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-[#0F034D] hover:bg-gray-100' }}" title="Pengaturan Sistem">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isPengaturanGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isPengaturanGroup ? 'font-bold' : '' }}">Pengaturan Sistem</span>
                    </div>
                    <svg id="icon-pengaturan" class="sidebar-text shrink-0 w-4 h-4 transition-transform duration-300 transform {{ $isPengaturanGroup ? 'rotate-180 text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="menu-pengaturan" class="overflow-hidden transition-all duration-300 {{ $isPengaturanGroup ? 'max-h-96' : 'max-h-0' }}">
                    <ul class="relative ml-6 pl-4 mt-1 mb-2 space-y-1">
                        <li>
                            @php $isManajemenUser = request()->routeIs('owner.users.*'); @endphp
                            <a href="{{ route('owner.users.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isManajemenUser ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isManajemenUser ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isManajemenUser ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                Manajemen Pengguna
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-full bg-gray-100 relative z-10 w-full overflow-hidden">
        
    <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30">
        <div class="flex items-center">
            <button onclick="toggleMobileSidebar()" class="md:hidden mr-3 shrink-0 w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-50 transition-colors cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="relative w-64 sm:w-80 md:w-96">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 pointer-events-none">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" class="bg-gray-100/50 border border-gray-200 text-gray-900 text-xs sm:text-sm rounded-full focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 block w-full pl-9 sm:pl-11 p-2 sm:p-2.5 outline-none transition-all placeholder-gray-400" placeholder="Search...">
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-5">
            <button class="relative w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-1.5 right-1.5 sm:top-2.5 sm:right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <div class="hidden sm:block w-px h-6 bg-gray-200 mx-1"></div>

            <div class="relative">
                <button id="profile-btn" onclick="toggleProfileDropdown()" class="flex items-center gap-2 hover:bg-gray-50 p-1 rounded-full transition-colors cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-[#0F034D] flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div id="profile-dropdown" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-right">
                    <div class="p-4 border-b border-gray-50">
                        <p class="text-sm font-bold text-[#0F034D] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profile Settings
                        </a>
                    </div>
                    <div class="p-2 border-t border-gray-50">
                        <form method="POST" action="{{ url('/logout') }}">
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
    </header>

    <div class="flex-1 overflow-y-auto p-8 w-full">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                {{-- Slot untuk Breadcrumb --}}
                @if (isset($breadcrumb))
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 text-xs sm:text-sm font-medium">
                            {{ $breadcrumb }}
                        </ol>
                    </nav>
                @endif

                {{-- Slot untuk Judul Halaman Utama --}}
                @if (isset($header))
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#0F034D] tracking-tight">
                        {{ $header }}
                    </h1>
                @endif
            </div>
            {{ $slot }}
        </div>
    </div>
</main>

    <script>
        window.flashMessages = {
            success: "{{ session('success') }}",
            error: "{{ session('error') }}",
            warning: "{{ $errors->any() ? 'Ada data yang tidak valid, silakan periksa form Anda.' : '' }}"
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite([
        'resources/js/layout/toggle-navbar-menu.js',
        'resources/js/layout/toast.js'
    ])
</body>
</html>
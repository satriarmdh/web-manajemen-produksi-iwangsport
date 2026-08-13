<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel | Iwangsport' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/global-modal.css', 'resources/js/app.js'])
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
            
            @php $isDashboard = request()->routeIs('admin.dashboard'); @endphp
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ $isDashboard ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="sidebar-text font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>

            @php 
                $isBahanBaku = request()->routeIs('admin.bahan-baku.*'); 
                $isProduk = request()->routeIs('admin.produk.*'); 
                $isSupplier = request()->routeIs('admin.supplier.*');
                $isPelanggan = request()->routeIs('admin.pelanggan.*');
                $isStandardBaseline = request()->routeIs('admin.standard-baseline-produksi.*');
                $isManajemenGroup = $isBahanBaku || $isProduk || $isSupplier || $isPelanggan || $isStandardBaseline;
            @endphp
            <div>
                <button onclick="toggleMenu('menu-manajemen', 'icon-manajemen')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isManajemenGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isManajemenGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isManajemenGroup ? 'font-bold' : '' }}">Manajemen Data</span>
                    </div>
                    <svg id="icon-manajemen" class="sidebar-text shrink-0 w-4 h-4 transition-transform duration-300 transform {{ $isManajemenGroup ? 'rotate-180 text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="menu-manajemen" class="overflow-hidden transition-all duration-300 {{ $isManajemenGroup ? 'max-h-96' : 'max-h-0' }}">
                    <ul class="relative ml-6 pl-4 mt-1 mb-2 space-y-1">
                        
                        <li>
                            <a href="{{ route('admin.bahan-baku.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isBahanBaku ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 z-20 border-l-2 transition-colors {{ ($isProduk || $isSupplier || $isPelanggan || $isStandardBaseline) ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 z-10 w-4 border-t-2 transition-colors {{ $isBahanBaku ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 z-20 bottom-1/2 border-l-2 transition-colors {{ $isBahanBaku ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Bahan Baku
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.produk.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isProduk ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 z-20 border-l-2 transition-colors {{ ($isStandardBaseline || $isSupplier || $isPelanggan) ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 z-10 w-4 border-t-2 transition-colors {{ $isProduk ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 z-20 bottom-1/2 border-l-2 transition-colors {{ $isProduk ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Produk
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.standard-baseline-produksi.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isStandardBaseline ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 z-20 border-l-2 transition-colors {{ ($isSupplier || $isPelanggan) ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 z-10 w-4 border-t-2 transition-colors {{ $isStandardBaseline ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 z-20 bottom-1/2 border-l-2 transition-colors {{ $isStandardBaseline ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Standard Baseline Produksi
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.supplier.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isSupplier ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 z-20 border-l-2 transition-colors {{ $isPelanggan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 z-10 w-4 border-t-2 transition-colors {{ $isSupplier ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 z-20 bottom-1/2 border-l-2 transition-colors {{ $isSupplier ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Supplier
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.pelanggan.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isPelanggan ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 z-20 bottom-1/2 border-l-2 transition-colors {{ $isPelanggan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 z-10 w-4 border-t-2 transition-colors {{ $isPelanggan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                Pelanggan
                            </a>
                        </li>

                    </ul>
                </div>
            </div>

            @php 
                $isPerintah = request()->routeIs('admin.perintah-produksi.*'); 
                $isProduksiGroup = $isPerintah;
            @endphp
            <div>
                <a href="{{ route('admin.perintah-produksi.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isProduksiGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isProduksiGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isProduksiGroup ? 'font-bold' : '' }}">Perintah Produksi</span>
                    </div>
                </a>
            </div>

            @php
                $isPergerakanBahan = request()->routeIs('admin.pergerakan-stok.*');
                $isPenjualan = request()->routeIs('admin.penjualan.*');
                $isTransaksiStokGroup = $isPergerakanBahan || $isPenjualan;
            @endphp
            <div>
                <button onclick="toggleMenu('menu-transaksi-stok', 'icon-transaksi-stok')" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ $isTransaksiStokGroup ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ $isTransaksiStokGroup ? 'text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span class="sidebar-text font-medium text-sm whitespace-nowrap {{ $isTransaksiStokGroup ? 'font-bold' : '' }}">Transaksi Stok</span>
                    </div>
                    <svg id="icon-transaksi-stok" class="sidebar-text shrink-0 w-4 h-4 transition-transform duration-300 transform {{ $isTransaksiStokGroup ? 'rotate-180 text-white' : 'text-gray-400 group-hover:text-[#0F034D]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="menu-transaksi-stok" class="overflow-hidden transition-all duration-300 {{ $isTransaksiStokGroup ? 'max-h-96' : 'max-h-0' }}">
                    <ul class="relative ml-6 pl-4 mt-1 mb-2 space-y-1">
                        <li>
                            <a href="{{ route('admin.pergerakan-stok.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isPergerakanBahan ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 -bottom-2 border-l-2 transition-colors {{ $isPenjualan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isPergerakanBahan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isPergerakanBahan ? 'border-[#0F034D]' : 'border-transparent' }}"></span>
                                Pergerakan Stok Bahan Baku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.penjualan.index') }}" class="relative flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors group whitespace-nowrap {{ $isPenjualan ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'text-gray-400 hover:text-[#0F034D] hover:bg-gray-100' }}">
                                <span class="absolute -left-4 -top-2 bottom-1/2 border-l-2 transition-colors {{ $isPenjualan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                <span class="absolute -left-4 top-1/2 w-4 border-t-2 transition-colors {{ $isPenjualan ? 'border-[#0F034D]' : 'border-gray-100' }}"></span>
                                Penjualan Produk
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
            </div>

            <div class="flex items-center gap-2 sm:gap-5">
                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notification-bell" class="relative w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="notification-badge" class="hidden absolute top-1 right-1 sm:top-1.5 sm:right-1.5 min-w-[16px] h-4 flex items-center justify-center px-1 bg-red-500 rounded-full border-2 border-white text-[10px] font-bold text-white leading-none">0</span>
                    </button>
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-gray-100 z-50 origin-top-right max-h-[480px] flex flex-col">
                        <div class="p-4 text-center text-sm text-gray-400">Memuat...</div>
                    </div>
                </div>

                <div class="hidden sm:block w-px h-6 bg-gray-200 mx-1"></div>

                <div class="relative">
                    <button id="profile-btn" onclick="toggleProfileDropdown()" class="flex items-center gap-2 hover:bg-gray-50 p-1 rounded-full transition-colors cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-[#0F034D] flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                        </div>
                        <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="profile-dropdown" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-right">
                    <div class="p-4 border-b border-gray-50">
                        <p class="text-sm font-bold text-[#0F034D] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Profile Settings
                        </a>
                    </div>
                    <div class="p-2 border-t border-gray-50">
                        <form method="POST" action="{{ url('/logout') }}" data-swal-confirm data-confirm-title="Keluar?" data-confirm-message="Anda akan keluar dari sistem. Lanjutkan?" data-confirm-button="Ya, Keluar">
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
                    @if (isset($breadcrumb))
                        <nav class="flex mb-2" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 text-xs sm:text-sm font-medium">
                                {{ $breadcrumb }}
                            </ol>
                        </nav>
                    @endif

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
            warning: {!! $errors->any() ? json_encode(implode('<br>', $errors->all())) : '""' !!}
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite([
        'resources/js/global-modal.js',
        'resources/js/layout/toggle-navbar-menu.js',
        'resources/js/layout/toast.js',
        'resources/js/utils/swal-confirm.js',
        'resources/js/notifications/bell.js'
    ])
</body>
</html>
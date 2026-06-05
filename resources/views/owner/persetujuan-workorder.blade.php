<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="inline-flex items-center">
            <a href="{{ route('owner.dashboard') }}" class="text-[#0F034D] flex items-center gap-1.5">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Test
            </a>
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Tes
    </x-slot:header>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 min-h-100 flex items-center justify-center">
        <div class="text-center">
            <h3 class="text-lg font-bold text-[#0F034D] mb-2">Selamat Datang di Panel Owner!</h3>
            <p class="text-gray-500">Coba cek sidebar di sebelah kiri, struktur dropdown dan animasinya seharusnya sudah berjalan sempurna.</p>
        </div>
    </div>

</x-layouts.owner>
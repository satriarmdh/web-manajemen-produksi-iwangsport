<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Produksi</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.perintah-produksi.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Perintah Produksi</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Buat Perintah Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Buat Perintah Produksi
    </x-slot:header>

    <form action="{{ route('admin.perintah-produksi.store') }}" method="POST">
        @csrf

        {{-- Informasi Umum --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-[#0F034D] mb-6">Informasi Umum</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Nomor WO Preview --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F034D] mb-2">Nomor Perintah Produksi</label>
                    <input type="text" value="{{ $previewNomorWO }}" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Otomatis di-generate oleh sistem</p>
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label for="tgl_mulai" class="block text-sm font-medium text-[#0F034D] mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 outline-none transition-all"
                        required>
                    @error('tgl_mulai')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label for="tgl_selesai" class="block text-sm font-medium text-[#0F034D] mb-2">
                        Tanggal Selesai <span class="text-xs text-gray-500">(Opsional)</span>
                    </label>
                    <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai') }}"
                        min="{{ old('tgl_mulai', now()->format('Y-m-d')) }}"
                        class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 outline-none transition-all">
                    <p class="text-xs text-gray-500 mt-1">Dapat diisi nanti saat perintah selesai</p>
                </div>
            </div>
        </div>

        {{-- Detail Produk --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-[#0F034D] mb-6">Detail Produk</h2>

            {{-- Form Input Detail --}}
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Produk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" id="input-produk">
                            <input type="text" id="input-produk-search" placeholder="Ketik untuk mencari produk..." autocomplete="off"
                                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 outline-none transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div id="input-produk-dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($produks as $produk)
                                        <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm"
                                            data-value="{{ $produk->id }}"
                                            data-text="{{ $produk->nama_produk }} ({{ $produk->kode_produk }}) - {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $produk->nama_produk }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $produk->kode_produk }}  -  {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}</div>
                                            </div>
                                            <svg class="check-icon w-4 h-4 text-[#0F034D] hidden shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="input-produk-no-results" class="hidden p-4 text-center text-sm text-gray-500">Produk tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" id="input-bahan">
                            <input type="text" id="input-bahan-search" placeholder="Ketik untuk mencari bahan baku..." autocomplete="off"
                                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 outline-none transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div id="input-bahan-dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($bahanBakus as $bahanBaku)
                                        <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm"
                                            data-value="{{ $bahanBaku->id }}"
                                            data-text="{{ $bahanBaku->nama_bahan }} ({{ $bahanBaku->kode_bahan }}) - {{ ucfirst($bahanBaku->warna) }}, {{ ucfirst($bahanBaku->kategori) }}">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $bahanBaku->nama_bahan }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $bahanBaku->kode_bahan }}  -  {{ ucfirst($bahanBaku->warna) }}, {{ ucfirst($bahanBaku->kategori) }}</div>
                                            </div>
                                            <svg class="check-icon w-4 h-4 text-[#0F034D] hidden shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="input-bahan-no-results" class="hidden p-4 text-center text-sm text-gray-500">Bahan baku tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Qty Roll --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah Roll <span class="text-red-500">*</span></label>
                        <input type="number" id="input-qty" min="1"
                            class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 outline-none"
                            placeholder="Contoh: 5">
                    </div>

                    {{-- Estimasi & Tombol Tambah --}}
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Estimasi PCS</label>
                            <input type="text" id="input-estimasi" readonly
                                class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 cursor-not-allowed"
                                value="-">
                        </div>
                        <button type="button" id="btn-tambah-detail" data-add-detail disabled
                            class="px-4 py-2 text-sm font-medium text-white bg-[#0F034D] rounded-lg hover:bg-[#1a0a6e] transition-colors disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed disabled:hover:bg-gray-300">
                            Tambah
                        </button>
                    </div>
                </div>

                <div id="baseline-alert" class="hidden mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Standard produksi belum tersedia</p>
                            <p class="mt-1 text-xs leading-relaxed text-amber-700">
                                Kombinasi produk dan bahan baku yang dipilih belum memiliki Standard Baseline Produksi aktif. Silakan buat standard produksi terlebih dahulu agar estimasi PCS dapat dihitung.
                            </p>
                            <a href="{{ route('admin.standard-baseline-produksi.index') }}" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-[#0F034D] hover:underline">
                                Buka Standard Baseline Produksi
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6m0 0v6m0-6L10 16"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">No</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Produk</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Bahan Baku</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600">Qty Roll</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600">Estimasi PCS</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detail-table-body">
                        {{-- Rows akan ditambahkan oleh JavaScript --}}
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 bg-gray-50">
                            <td colspan="4" class="py-3 px-4 text-right font-semibold text-[#0F034D]">Total Estimasi:</td>
                            <td class="py-3 px-4 text-center font-bold text-[#0F034D]" id="total-estimasi">0 pcs</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @error('details')
                <p class="text-red-500 text-sm mt-4">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-4 italic">* Estimasi PCS dihitung otomatis berdasarkan standar baseline (Qty Roll Ã- PCS per Roll)</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.perintah-produksi.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white bg-[#0F034D] rounded-xl hover:bg-[#1a0a6e] transition-colors shadow-md shadow-[#0F034D]/20">
                Simpan Perintah Produksi
            </button>
        </div>
    </form>
    <script type="application/json" id="perintah-produksi-baselines">@json($baselines)</script>
    <script type="application/json" id="perintah-produksi-produks">@json($produks)</script>
    <script type="application/json" id="perintah-produksi-bahan-bakus">@json($bahanBakus)</script>
    @vite('resources/js/admin/perintah-produksi/form-create.js')
</x-layouts.admin>


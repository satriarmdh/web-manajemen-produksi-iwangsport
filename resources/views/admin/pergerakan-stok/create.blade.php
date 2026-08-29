<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path></svg>
            <span class="select-none">Transaksi Stok</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab]) }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Pergerakan Stok</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Tambah Stok {{ ucfirst($tab) }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Tambah Stok {{ ucfirst($tab) }}
    </x-slot:header>

    <div class="w-full">
        <!-- Alert error global jika ada -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-sm">
                <p class="font-bold mb-1">Periksa kembali inputan Anda:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 rounded-t-xl">
                <h3 class="text-lg font-bold text-[#0F034D]">Form Stok {{ ucfirst($tab) }}</h3>
            </div>

            <form action="{{ route('admin.pergerakan-stok.store') }}" method="POST" enctype="multipart/form-data" id="pergerakan-form" class="px-6 py-6 space-y-6"
                data-swal-confirm
                data-confirm-title="Simpan Transaksi Stok?"
                data-confirm-message="Transaksi yang sudah disimpan akan mempengaruhi stok bahan baku. Pastikan data sudah benar."
                data-confirm-button="Ya, Simpan">
                @csrf
                <input type="hidden" name="jenis_pergerakan" value="{{ $tab }}">

                <!-- Baris 1: Informasi Transaksi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors bg-white outline-none text-gray-700">
                    </div>

                    @if($tab === 'masuk')
                        <!-- Supplier -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Supplier</label>
                            <div class="relative">
                                <input type="hidden" name="supplier_id" id="supplier_value" value="{{ old('supplier_id') }}">
                                <input type="text" id="supplier_input" placeholder="Cari / pilih supplier..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500 bg-white">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="supplier_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto hidden">
                                    <div class="p-2">
                                        @foreach($suppliers as $supplier)
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $supplier->id }}" data-text="{{ $supplier->nama_supplier }}">
                                            <span class="text-sm font-medium text-gray-700">{{ $supplier->nama_supplier }}</span>
                                            <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div id="supplier_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Penerima -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Penerima <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="penerima" id="penerima_value" value="{{ old('penerima') }}" required>
                                <input type="text" id="penerima_input" placeholder="Cari / pilih penerima..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500 bg-white">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="penerima_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto hidden">
                                    <div class="p-2">
                                        @foreach($karyawan as $k)
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $k->name }}" data-text="{{ $k->name }} ({{ ucfirst($k->role) }})">
                                            <span class="text-sm font-medium text-gray-700">{{ $k->name }} <span class="text-gray-400 text-xs">({{ ucfirst($k->role) }})</span></span>
                                            <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div id="penerima_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Bukti -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bukti Lampiran (Foto/Gambar)</label>
                        <input type="file" name="bukti" accept="image/*"
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0F034D]/5 file:text-[#0F034D] hover:file:bg-[#0F034D]/10 transition-all border border-gray-300 rounded-xl p-1.5 cursor-pointer">
                    </div>
                </div>

                <!-- Baris 2: Catatan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan / Keterangan</label>
                    <textarea name="catatan" rows="2" placeholder="Keterangan tambahan..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors bg-white outline-none resize-none text-gray-700">{{ old('catatan') }}</textarea>
                </div>

                <!-- Baris 3: Daftar Bahan Baku Section -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Daftar Bahan Baku</h3>

                    <!-- Form Tambah Item Ke List -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Bahan Baku <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" id="input_bahan_baku_value">
                                <input type="text" id="input_bahan_baku_input" placeholder="Cari / pilih bahan baku..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors bg-white outline-none text-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="input_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                    <div class="p-2">
                                        @php
                                            $bahanList = $tab === 'masuk' ? $bahanBakuAll : $bahanBakuNonKain;
                                        @endphp
                                        @foreach($bahanList as $b)
                                            <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" 
                                                 data-value="{{ $b->id }}" 
                                                 data-text="{{ $b->nama_bahan }} - {{ ucfirst($b->warna) }} ({{ $b->kode_bahan }})"
                                                 data-nama="{{ $b->nama_bahan }}"
                                                 data-warna="{{ ucfirst($b->warna) }}"
                                                 data-kode="{{ $b->kode_bahan }}"
                                                 data-satuan="{{ $b->satuan }}"
                                                 data-stok="{{ $b->stok }}">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium text-gray-700">{{ $b->nama_bahan }} - {{ ucfirst($b->warna) }} <span class="text-gray-400">({{ $b->kode_bahan }})</span></div>
                                                </div>
                                                <div class="shrink-0 text-right">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $b->stok > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                                        Stok: {{ number_format($b->stok, 0, ',', '.') }} {{ ucfirst($b->satuan) }}
                                                    </span>
                                                </div>
                                                <svg class="check-icon w-4 h-4 text-[#0F034D] hidden shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="input_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Bahan baku tidak ditemukan</div>
                                </div>
                            </div>
                            <p id="info_stok_baku" class="text-xs mt-1.5 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah PCS / Satuan <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="number" id="input-quantity" min="1" placeholder="Qty" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-all bg-white outline-none text-gray-700">
                                <button type="button" id="btn-tambah-item" class="px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#0F034D]/10 cursor-pointer">
                                    Tambah
                                </button>
                            </div>
                            <p id="info_qty_baku_warning" class="text-xs text-red-500 font-medium mt-1.5 hidden"></p>
                        </div>
                    </div>

                    <!-- Tabel Item Terpilih -->
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Kode</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Nama Bahan Baku</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-500">Jumlah</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-items-body" class="divide-y divide-gray-50">
                                <tr id="tr-empty-state">
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                        Belum ada bahan baku ditambahkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab]) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-colors cursor-pointer">Batal</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.pergerakanStokConfig = {
            oldSupplier: "{{ old('supplier_id') }}",
            oldPenerima: "{{ old('penerima') }}",
            jenisPergerakan: "{{ $tab }}"
        };
    </script>
    @vite([
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/pergerakan-stok/create.js'
    ])
</x-layouts.admin>

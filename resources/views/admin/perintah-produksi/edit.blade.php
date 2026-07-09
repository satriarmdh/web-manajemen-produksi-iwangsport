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
            Edit Perintah Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Edit Perintah Produksi
    </x-slot:header>

    <form action="{{ route('admin.perintah-produksi.update', $perintahProduksi) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Informasi Umum --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-[#0F034D] mb-6">Informasi Umum</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Nomor WO Preview --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F034D] mb-2">Nomor Perintah Produksi</label>
                    <input type="text" value="{{ $perintahProduksi->nomor_wo }}" readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Nomor Perintah Produksi tidak dapat diubah</p>
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label for="tgl_mulai" class="block text-sm font-medium text-[#0F034D] mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ old('tgl_mulai', \Carbon\Carbon::parse($perintahProduksi->tgl_mulai)->format('Y-m-d')) }}"
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
                    <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ old('tgl_selesai', $perintahProduksi->tgl_selesai ? \Carbon\Carbon::parse($perintahProduksi->tgl_selesai)->format('Y-m-d') : '') }}"
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
                                            data-text="{{ $produk->nama_produk }} ({{ $produk->kode_produk }}) — {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $produk->nama_produk }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $produk->kode_produk }} • {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}</div>
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
                                            data-text="{{ $bahanBaku->nama_bahan }} ({{ $bahanBaku->kode_bahan }}) — {{ ucfirst($bahanBaku->warna) }}, {{ ucfirst($bahanBaku->kategori) }}">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $bahanBaku->nama_bahan }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $bahanBaku->kode_bahan }} • {{ ucfirst($bahanBaku->warna) }}, {{ ucfirst($bahanBaku->kategori) }}</div>
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
                        <button type="button" id="btn-tambah-detail" onclick="addDetailRow()" disabled
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

            <p class="text-xs text-gray-500 mt-4 italic">* Estimasi PCS dihitung otomatis berdasarkan standar baseline (Qty Roll × PCS per Roll)</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.perintah-produksi.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white bg-[#0F034D] rounded-xl hover:bg-[#1a0a6e] transition-colors shadow-md shadow-[#0F034D]/20">
                Update Perintah Produksi
            </button>
        </div>
    </form>

    @php
        $initialDetails = $perintahProduksi->details->map(function ($detail) {
            return [
                'produk_id' => (string) $detail->produk_id,
                'bahan_baku_id' => (string) $detail->bahan_baku_id,
                'qty_roll_pakai' => (int) $detail->qty_roll_pakai,
                'estimasi_pcs' => (int) $detail->estimasi_pcs,
                'produk_nama' => $detail->produk->nama_produk ?? '-',
                'bahan_nama' => $detail->bahanBaku->nama_bahan ?? '-',
            ];
        })->values();
    @endphp

    <script>
        // Data baseline dari server
        const baselines = @json($baselines);

        // Data produk dan bahan baku untuk lookup nama
        const produksData = @json($produks);
        const bahanBakusData = @json($bahanBakus);

        // Data detail lama dari server
        const initialDetails = @json($initialDetails);

        // Array untuk menyimpan detail yang ditambahkan
        let details = [...initialDetails];

        const tglMulaiInput = document.getElementById('tgl_mulai');
        const tglSelesaiInput = document.getElementById('tgl_selesai');

        function syncTanggalSelesaiMin() {
            tglSelesaiInput.min = tglMulaiInput.value;

            if (tglSelesaiInput.value && tglSelesaiInput.value < tglMulaiInput.value) {
                tglSelesaiInput.value = '';
            }
        }

        tglMulaiInput.addEventListener('change', syncTanggalSelesaiMin);
        syncTanggalSelesaiMin();

        // Hitung estimasi saat input berubah
        function calculateEstimasi() {
            const produkId = document.getElementById('input-produk').value;
            const bahanId = document.getElementById('input-bahan').value;
            const qty = parseInt(document.getElementById('input-qty').value) || 0;
            const estimasiInput = document.getElementById('input-estimasi');
            const baselineAlert = document.getElementById('baseline-alert');
            const tambahButton = document.getElementById('btn-tambah-detail');

            baselineAlert.classList.add('hidden');
            tambahButton.disabled = true;

            if (!produkId || !bahanId) {
                estimasiInput.value = '-';
                return;
            }

            // Cari baseline yang sesuai
            const baseline = baselines.find(b => b.produk_id == produkId && b.bahan_baku_id == bahanId);

            if (!baseline) {
                estimasiInput.value = 'Baseline belum tersedia';
                baselineAlert.classList.remove('hidden');
                return;
            }

            if (qty < 1) {
                estimasiInput.value = '-';
                return;
            }

            const estimasi = qty * baseline.pcs_per_roll;
            estimasiInput.value = estimasi + ' pcs';
            tambahButton.disabled = false;
        }

        // Event listeners untuk kalkulasi otomatis
        document.getElementById('input-produk').addEventListener('change', calculateEstimasi);
        document.getElementById('input-bahan').addEventListener('change', calculateEstimasi);
        document.getElementById('input-qty').addEventListener('input', calculateEstimasi);

        function initSearchableDropdown(prefix) {
            const hiddenInput = document.getElementById(prefix);
            const searchInput = document.getElementById(`${prefix}-search`);
            const dropdown = document.getElementById(`${prefix}-dropdown`);
            const noResults = document.getElementById(`${prefix}-no-results`);
            const arrow = searchInput.parentElement.querySelector('.dropdown-arrow');
            const options = dropdown.querySelectorAll('.dropdown-option');

            function filterOptions() {
                const term = searchInput.value.toLowerCase();
                let visibleCount = 0;

                options.forEach((option) => {
                    const text = option.dataset.text.toLowerCase();
                    const isVisible = text.includes(term);
                    option.style.display = isVisible ? 'flex' : 'none';
                    if (isVisible) visibleCount++;
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount > 0);
                }
            }

            function openDropdown() {
                dropdown.classList.remove('hidden');
                arrow?.classList.add('rotate-180');
                searchInput.value = '';
                filterOptions();
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                arrow?.classList.remove('rotate-180');

                if (!hiddenInput.value) {
                    searchInput.value = '';
                } else {
                    const selected = dropdown.querySelector(`.dropdown-option[data-value="${hiddenInput.value}"]`);
                    searchInput.value = selected ? selected.dataset.text : '';
                }
            }

            function selectOption(option) {
                options.forEach((item) => {
                    item.classList.remove('bg-gray-100');
                    item.querySelector('.check-icon')?.classList.add('hidden');
                });

                option.classList.add('bg-gray-100');
                option.querySelector('.check-icon')?.classList.remove('hidden');

                hiddenInput.value = option.dataset.value;
                searchInput.value = option.dataset.text;
                searchInput.classList.add('font-medium', 'text-gray-900');
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                closeDropdown();
            }

            searchInput.addEventListener('focus', openDropdown);
            searchInput.addEventListener('input', filterOptions);
            options.forEach((option) => option.addEventListener('click', () => selectOption(option)));

            document.addEventListener('click', (event) => {
                if (!searchInput.parentElement.contains(event.target)) {
                    closeDropdown();
                }
            });
        }

        function resetSearchableDropdown(prefix) {
            const hiddenInput = document.getElementById(prefix);
            const searchInput = document.getElementById(`${prefix}-search`);
            const dropdown = document.getElementById(`${prefix}-dropdown`);

            hiddenInput.value = '';
            searchInput.value = '';
            searchInput.classList.remove('font-medium', 'text-gray-900');
            dropdown.querySelectorAll('.dropdown-option').forEach((item) => {
                item.classList.remove('bg-gray-100');
                item.querySelector('.check-icon')?.classList.add('hidden');
                item.style.display = 'flex';
            });
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        initSearchableDropdown('input-produk');
        initSearchableDropdown('input-bahan');

        // Tambah detail ke tabel
        function addDetailRow() {
            const produkId = document.getElementById('input-produk').value;
            const bahanId = document.getElementById('input-bahan').value;
            const qty = parseInt(document.getElementById('input-qty').value) || 0;

            // Validasi
            if (!produkId || !bahanId || qty < 1) {
                alert('Mohon lengkapi semua field dan pastikan jumlah roll minimal 1');
                return;
            }

            // Cari baseline
            const baseline = baselines.find(b => b.produk_id == produkId && b.bahan_baku_id == bahanId);
            if (!baseline) {
                alert('Kombinasi produk dan bahan baku tidak memiliki standar baseline. Silakan hubungi administrator.');
                return;
            }

            // Cek duplikat
            const exists = details.some(d => d.produk_id == produkId && d.bahan_baku_id == bahanId);
            if (exists) {
                alert('Kombinasi produk dan bahan baku ini sudah ditambahkan. Silakan edit atau hapus yang sudah ada.');
                return;
            }

            // Cari nama produk dan bahan baku
            const produk = produksData.find(p => p.id == produkId);
            const bahan = bahanBakusData.find(b => b.id == bahanId);

            // Hitung estimasi
            const estimasi = qty * baseline.pcs_per_roll;

            // Tambah ke array
            details.push({
                produk_id: produkId,
                bahan_baku_id: bahanId,
                qty_roll_pakai: qty,
                estimasi_pcs: estimasi,
                produk_nama: produk.nama_produk,
                bahan_nama: bahan.nama_bahan
            });

            // Render ulang tabel
            renderTable();

            // Reset form input
            resetSearchableDropdown('input-produk');
            resetSearchableDropdown('input-bahan');
            document.getElementById('input-qty').value = '';
            document.getElementById('input-estimasi').value = '-';
            document.getElementById('baseline-alert').classList.add('hidden');
            document.getElementById('btn-tambah-detail').disabled = true;
        }

        // Hapus detail dari tabel
        function removeDetailRow(index) {
            if (confirm('Hapus detail ini?')) {
                details.splice(index, 1);
                renderTable();
            }
        }

        // Render tabel detail
        function renderTable() {
            const tbody = document.getElementById('detail-table-body');
            tbody.innerHTML = '';

            if (details.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            Belum ada detail produk. Silakan tambahkan di form atas.
                        </td>
                    </tr>
                `;
                document.getElementById('total-estimasi').textContent = '0 pcs';
                return;
            }

            let totalEstimasi = 0;

            details.forEach((detail, index) => {
                totalEstimasi += detail.estimasi_pcs;

                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="py-3 px-4 text-gray-600">${index + 1}</td>
                    <td class="py-3 px-4 font-medium text-[#0F034D]">${detail.produk_nama}</td>
                    <td class="py-3 px-4 text-gray-600">${detail.bahan_nama}</td>
                    <td class="py-3 px-4 text-center">${detail.qty_roll_pakai}</td>
                    <td class="py-3 px-4 text-center font-semibold text-green-600">${detail.estimasi_pcs} pcs</td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" onclick="removeDetailRow(${index})"
                            class="text-red-600 hover:text-red-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                    <input type="hidden" name="details[${index}][produk_id]" value="${detail.produk_id}">
                    <input type="hidden" name="details[${index}][bahan_baku_id]" value="${detail.bahan_baku_id}">
                    <input type="hidden" name="details[${index}][qty_roll_pakai]" value="${detail.qty_roll_pakai}">
                `;
                tbody.appendChild(row);
            });

            document.getElementById('total-estimasi').textContent = totalEstimasi + ' pcs';
        }

        // Render awal saat halaman dimuat
        renderTable();
    </script>
</x-layouts.admin>

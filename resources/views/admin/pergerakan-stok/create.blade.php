<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Transaksi Stok</span>
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

            <form action="{{ route('admin.pergerakan-stok.store') }}" method="POST" enctype="multipart/form-data" id="pergerakan-form" class="px-6 py-6 space-y-6">
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
                            <input type="text" name="penerima" value="{{ old('penerima') }}" placeholder="Nama karyawan penerima..." required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors bg-white outline-none text-gray-700">
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
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" 
                                                 data-value="{{ $b->id }}" 
                                                 data-text="{{ $b->nama_bahan }} - {{ ucfirst($b->warna) }} ({{ $b->kode_bahan }})"
                                                 data-nama="{{ $b->nama_bahan }}"
                                                 data-warna="{{ ucfirst($b->warna) }}"
                                                 data-kode="{{ $b->kode_bahan }}"
                                                 data-satuan="{{ $b->satuan }}"
                                                 data-stok="{{ $b->stok }}">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-gray-700">{{ $b->nama_bahan }} - {{ ucfirst($b->warna) }} <span class="text-gray-400">({{ $b->kode_bahan }})</span></div>
                                                    @if($tab === 'keluar')
                                                        <div class="text-[11px] text-gray-500">Tersedia: {{ $b->stok }} {{ $b->satuan }}</div>
                                                    @endif
                                                </div>
                                                <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="input_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Bahan baku tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah PCS / Satuan <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="number" id="input-quantity" min="1" placeholder="Qty" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-all bg-white outline-none text-gray-700">
                                <button type="button" id="btn-tambah-item" class="px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#0F034D]/10 cursor-pointer">
                                    Tambah
                                </button>
                            </div>
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @vite(['resources/js/admin/custom-forms.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi dropdown kustom
            initCustomDropdown('input_bahan_baku');
            if (document.getElementById('supplier_input')) {
                initCustomDropdown('supplier');
            }

            const hiddenBahanInput = document.getElementById('input_bahan_baku_value');
            const dropdownBahan = document.getElementById('input_bahan_baku_dropdown');
            const inputQty = document.getElementById('input-quantity');
            const btnTambah = document.getElementById('btn-tambah-item');
            const tableBody = document.getElementById('table-items-body');
            const trEmpty = document.getElementById('tr-empty-state');
            const jenisPergerakan = "{{ $tab }}";

            let items = [];

            function renderTable() {
                const rows = tableBody.querySelectorAll('tr:not(#tr-empty-state)');
                rows.forEach(r => r.remove());

                if (items.length === 0) {
                    trEmpty.classList.remove('hidden');
                    return;
                }

                trEmpty.classList.add('hidden');

                items.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50/50 transition-colors';
                    tr.innerHTML = `
                        <td class="px-4 py-3 text-gray-500 font-mono">${item.kode}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">${item.nama} - ${item.warna}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                            ${item.quantity} ${item.satuan}
                            <input type="hidden" name="items[${index}][bahan_baku_id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="btn-hapus-item text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors cursor-pointer" data-id="${item.id}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });

                document.querySelectorAll('.btn-hapus-item').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        items = items.filter(it => it.id !== id);
                        renderTable();
                    });
                });
            }

            btnTambah.addEventListener('click', () => {
                const id = hiddenBahanInput.value;
                const qty = parseInt(inputQty.value) || 0;

                if (!id) {
                    alert('Pilih bahan baku terlebih dahulu!');
                    return;
                }

                if (qty <= 0) {
                    alert('Jumlah kuantiti harus minimal 1!');
                    return;
                }

                // Ambil option terpilih dari custom dropdown
                const selectedOpt = dropdownBahan.querySelector(`.dropdown-option[data-value="${id}"]`);
                if (!selectedOpt) return;

                const nama = selectedOpt.getAttribute('data-nama');
                const warna = selectedOpt.getAttribute('data-warna') || '-';
                const kode = selectedOpt.getAttribute('data-kode');
                const satuan = selectedOpt.getAttribute('data-satuan');
                const stokMax = parseInt(selectedOpt.getAttribute('data-stok')) || 0;

                if (items.some(it => it.id === id)) {
                    alert('Bahan baku ini sudah ditambahkan ke dalam daftar!');
                    return;
                }

                if (jenisPergerakan === 'keluar') {
                    if (qty > stokMax) {
                        alert(`Stok tidak mencukupi untuk ${nama}! Maksimal pengeluaran: ${stokMax} ${satuan}`);
                        return;
                    }
                }

                items.push({ id, nama, warna, kode, quantity: qty, satuan });

                // Reset custom dropdown
                resetCustomDropdown('input_bahan_baku');
                inputQty.value = '';

                renderTable();
            });
        });
    </script>
</x-layouts.admin>

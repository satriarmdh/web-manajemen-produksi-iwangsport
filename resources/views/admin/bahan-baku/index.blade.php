<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Bahan Baku
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Bahan Baku
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Inventori Dasar</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data master bahan baku yang digunakan untuk proses produksi.</p>
            </div>
            <button onclick="toggleModal('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Bahan Baku
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Kode & Nama Bahan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Warna</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Kategori</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Satuan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Total Stok</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bahanBaku as $bahan)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div> --}}
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $bahan->nama_bahan }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">{{ $bahan->kode_bahan }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $bahan->warna }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">
                                    {{ ucfirst($bahan->kategori) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $bahan->satuan }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($bahan->stok == 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        Stockout
                                    </span>
                                @else
                                    <span class="text-sm font-bold {{ $bahan->stok < 10 ? 'text-amber-600' : 'text-gray-900' }}">
                                        {{ number_format($bahan->stok, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" 
                                            onclick="openEditModal(this)"
                                            data-id="{{ $bahan->id }}"
                                            data-kode="{{ $bahan->kode_bahan }}"
                                            data-nama="{{ $bahan->nama_bahan }}"
                                            data-kategori="{{ $bahan->kategori }}"
                                            data-satuan="{{ $bahan->satuan }}"
                                            class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Edit Bahan Baku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>

                                    <form action="{{ route('admin.bahan-baku.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku {{ $bahan->nama_bahan }}? Data terkait produksi mungkin akan terpengaruh.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Bahan Baku">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <p class="text-gray-500 font-medium">Belum ada bahan baku terdaftar.</p>
                                    <p class="text-sm text-gray-400 mt-1">Silakan klik Tambah Bahan Baku untuk mengisi katalog.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TAMBAH BAHAN BAKU --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('add-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">
                
                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Tambah Bahan Baku</h3>
                    <button onclick="toggleModal('add-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form action="{{ route('admin.bahan-baku.store') }}" method="POST" id="addForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="kode_bahan" required placeholder="Contoh: KAIN-001" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm uppercase">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_bahan" required placeholder="Contoh: Kain Cotton Combed 30s Hitam" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <select name="kategori" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-white cursor-pointer">
                                        <option value="" disabled selected>Pilih...</option>
                                        <option value="kain">Kain</option>
                                        <option value="benang">Benang</option>
                                        <option value="kancing">Kancing</option>
                                        <option value="resleting">Resleting</option>
                                        <option value="aksesoris">Aksesoris Lainnya</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <select name="satuan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-white cursor-pointer">
                                        <option value="" disabled selected>Pilih...</option>
                                        <option value="roll">Roll</option>
                                        <option value="kg">Kilogram (Kg)</option>
                                        <option value="pcs">Pieces (Pcs)</option>
                                        <option value="meter">Meter</option>
                                        <option value="yard">Yard</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Awal</label>
                                <input type="number" name="stok" value="0" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-gray-50" readonly title="Stok awal default adalah 0. Tambah stok melalui menu Pemasukan Bahan.">
                                <p class="text-xs text-gray-500 mt-1">Sistem menerapkan riwayat mutasi. Penambahan stok dilakukan di menu Operasional.</p>
                            </div> --}}
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="toggleModal('add-modal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="addForm" class="px-5 py-2.5 text-sm font-medium text-white bg-[#0F034D] hover:bg-[#0a0235] shadow-md rounded-xl transition-all cursor-pointer">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT BAHAN BAKU --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('edit-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">
                
                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Edit Bahan Baku</h3>
                    <button onclick="toggleModal('edit-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form action="" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="kode_bahan" id="edit_kode" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm uppercase">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_bahan" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <select name="kategori" id="edit_kategori" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-white cursor-pointer">
                                        <option value="kain">Kain</option>
                                        <option value="benang">Benang</option>
                                        <option value="kancing">Kancing</option>
                                        <option value="resleting">Resleting</option>
                                        <option value="aksesoris">Aksesoris Lainnya</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <select name="satuan" id="edit_satuan" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-white cursor-pointer">
                                        <option value="roll">Roll</option>
                                        <option value="kg">Kilogram (Kg)</option>
                                        <option value="pcs">Pieces (Pcs)</option>
                                        <option value="meter">Meter</option>
                                        <option value="yard">Yard</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="toggleModal('edit-modal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="editForm" class="px-5 py-2.5 text-sm font-medium text-white bg-[#0F034D] hover:bg-[#0a0235] shadow-md rounded-xl transition-all cursor-pointer">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            const isHidden = modal.classList.contains('hidden');
            const innerContent = modal.querySelector('div.transform');
            
            if (isHidden) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    innerContent.classList.remove('scale-95');
                }, 10);
            } else {
                modal.classList.add('opacity-0');
                innerContent.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        }

        // Logic Auto-fill Data untuk Modal Edit
        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const kode = button.getAttribute('data-kode');
            const nama = button.getAttribute('data-nama');
            const kategori = button.getAttribute('data-kategori');
            const satuan = button.getAttribute('data-satuan');

            // Set Form Action URL (Ganti sesuai nama rute Anda)
            const form = document.getElementById('editForm');
            form.action = `/admin/bahan-baku/${id}`; 

            // Isi input fields
            document.getElementById('edit_kode').value = kode;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_satuan').value = satuan;

            toggleModal('edit-modal');
        }
    </script>
</x-layouts.admin>
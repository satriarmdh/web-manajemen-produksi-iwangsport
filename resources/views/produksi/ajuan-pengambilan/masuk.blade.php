<x-layouts.produksi>
    <x-slot:header>Ajuan Masuk</x-slot:header>

    @if(session('success'))
        <div class="mb-4 rounded-2xl bg-green-50 border border-green-100 p-4 text-sm font-semibold text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-[#0F034D]">Ajuan Masuk</h3>
                <p class="text-sm text-gray-500 mt-1">Konfirmasi ajuan pengambilan barang dari karyawan produksi lain.</p>
            </div>
            <div class="sm:w-auto">
                <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-amber-700/70">Perlu konfirmasi</p>
                    <p class="text-sm font-bold text-amber-700">{{ $ajuanMasuk->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <h3 class="font-bold text-[#0F034D] mb-3">Daftar Ajuan Masuk</h3>
        <div class="space-y-4">
            @forelse($ajuanMasuk->groupBy('id_perintah') as $idPerintah => $ajuanDalamPerintah)
                @php
                    $perintah = $ajuanDalamPerintah->first()->perintahProduksi;
                    $totalQty = $ajuanDalamPerintah->sum('qty_ajuan');
                @endphp
                <details class="group rounded-2xl border border-gray-100 overflow-hidden" open>
                    <summary class="list-none cursor-pointer bg-gray-50/80 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-[#0F034D]">{{ $perintah->nomor_wo ?? 'Perintah Produksi' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $ajuanDalamPerintah->count() }} ajuan • {{ number_format($totalQty, 0, ',', '.') }} pcs diajukan</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path></svg>
                        </div>
                    </summary>

                    <div class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuanDalamPerintah as $ajuan)
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-[#0F034D]">{{ $ajuan->produk->nama_produk ?? '-' }} - {{ ucfirst($ajuan->produk->warna ?? '-') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Dari {{ $ajuan->keKaryawan->name ?? '-' }} • {{ number_format($ajuan->qty_ajuan, 0, ',', '.') }} pcs</p>
                                        @if($ajuan->catatan_pengaju)
                                            <p class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">Catatan pengaju: {{ $ajuan->catatan_pengaju }}</p>
                                        @endif
                                        <span class="inline-flex mt-2 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Menunggu konfirmasi</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 sm:w-56">
                                        <form action="{{ route('produksi.ajuan-pengambilan.approve', $ajuan) }}" method="POST">
                                            @csrf
                                            <button class="w-full rounded-xl bg-green-600 py-2.5 text-xs font-bold text-white hover:bg-green-700 transition-colors">Setujui</button>
                                        </form>
                                        <button type="button" data-open-reject-modal="{{ $ajuan->id }}" class="w-full rounded-xl bg-red-50 py-2.5 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors">Tolak</button>
                                    </div>
                                </div>
                            </div>

                            <div data-reject-modal="{{ $ajuan->id }}" class="fixed inset-0 z-[70] hidden items-center justify-center bg-[#0F034D]/40 px-4 backdrop-blur-sm">
                                <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl shadow-[#0F034D]/20">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-bold text-[#0F034D]">Tolak Ajuan?</h3>
                                            <p class="mt-1 text-sm text-gray-500">Isi alasan penolakan agar pengaju tahu penyebabnya.</p>
                                        </div>
                                        <button type="button" data-close-reject-modal class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('produksi.ajuan-pengambilan.reject', $ajuan) }}" method="POST" class="mt-4 space-y-4">
                                        @csrf
                                        <textarea name="catatan_respon" rows="4" required maxlength="1000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Contoh: Barang belum siap diambil hari ini"></textarea>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" data-close-reject-modal class="rounded-xl border border-gray-200 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                                            <button type="submit" class="rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors">Tolak Ajuan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @empty
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-6 text-center">
                    <p class="text-sm font-semibold text-gray-600">Belum ada ajuan masuk.</p>
                    <p class="text-xs text-gray-400 mt-1">Ajuan dari karyawan lain akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </section>
    <script>
        const closeRejectModals = () => {
            document.querySelectorAll('[data-reject-modal]').forEach((modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        };

        document.querySelectorAll('[data-open-reject-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                closeRejectModals();
                const modal = document.querySelector(`[data-reject-modal="${button.dataset.openRejectModal}"]`);
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            });
        });

        document.querySelectorAll('[data-close-reject-modal]').forEach((button) => {
            button.addEventListener('click', closeRejectModals);
        });

        document.querySelectorAll('[data-reject-modal]').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeRejectModals();
                }
            });
        });
    </script>
</x-layouts.produksi>

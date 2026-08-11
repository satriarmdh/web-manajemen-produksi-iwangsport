<x-layouts.produksi>
    <x-slot:header>
        Detail Pekerjaan
    </x-slot:header>

    <div class="mb-4">
        <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0F034D] hover:text-[#24116f]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path></svg>
            Kembali ke daftar pekerjaan
        </a>
    </div>

    <div class="rounded-3xl bg-gradient-to-br from-[#0F034D] to-[#24116f] p-5 text-white shadow-xl shadow-[#0F034D]/20 mb-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs text-white/60">Perintah Produksi</p>
                <h2 class="text-xl font-bold mt-1">Perintah Produksi: {{ $perintahProduksi->nomor_wo }}</h2>
                <p class="text-sm text-white/75 mt-2">Periode: {{ \Carbon\Carbon::parse($perintahProduksi->tgl_mulai)->format('d M Y') }} - {{ $perintahProduksi->tgl_selesai ? \Carbon\Carbon::parse($perintahProduksi->tgl_selesai)->format('d M Y') : '-' }}</p>
                <p class="mt-3 rounded-2xl bg-white/10 border border-white/15 px-3 py-2 text-xs font-semibold text-white/85">Pastikan hasil yang diinput sesuai Perintah Produksi ini agar produk yang sama dari PP berbeda tidak tertukar.</p>
            </div>
            <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold">{{ ucfirst(str_replace('_', ' ', $perintahProduksi->status_produksi)) }}</span>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($perintahProduksi->details as $detail)
            @php
                $warnaProduk = strtolower($detail->produk->warna ?? '-');
                $warnaDotMap = [
                    'hitam' => '#111827',
                    'navy' => '#061952',
                    'abu-abu' => '#9CA3AF',
                    'abu' => '#9CA3AF',
                    'putih' => '#FFFFFF',
                ];
                $warnaDot = $warnaDotMap[$warnaProduk] ?? '#CBD5E1';
                $needsStroke = in_array($warnaProduk, ['abu-abu', 'abu', 'putih'], true);
                $stokVirtualSaya = \App\Models\StokVirtual::where('id_detail_perintah', $detail->id)
                    ->where('id_karyawan', auth()->id())
                    ->where('peran', $role)
                    ->first();
                if ($role === 'potong') {
                    $targetInput = (int) $detail->estimasi_pcs;
                    $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;
                } else {
                    $targetInput = $stokVirtualSaya ? ((int) $stokVirtualSaya->qty_hold + (int) $stokVirtualSaya->total_selesai + (int) $stokVirtualSaya->total_reject) : 0;
                    $batasBawah = $targetInput;
                }
                $hasilBaik = (int) ($stokVirtualSaya?->total_selesai ?? 0);
                $totalReject = (int) ($stokVirtualSaya?->total_reject ?? 0);
                $totalDikeluarkan = (int) ($stokVirtualSaya?->total_dikeluarkan ?? 0);
                $sudahDiinput = $hasilBaik + $totalReject;
                $sisaEstimasi = max(0, $targetInput - $sudahDiinput);
                $progressPersen = $targetInput > 0 ? min(100, round(($sudahDiinput / $targetInput) * 100)) : 0;
                $inputSelesai = (bool) ($stokVirtualSaya?->is_selesai ?? false) || ($targetInput > 0 && $sudahDiinput >= $targetInput);
                $perluPengajuan = $role !== 'potong' && (! $stokVirtualSaya || $targetInput <= 0);
                $stokReady = $stokVirtualSaya
                    ? max(0, (int) $stokVirtualSaya->total_selesai - (int) $stokVirtualSaya->total_dikeluarkan)
                    : 0;
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <h3 class="text-md font-bold text-[#0F034D] truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</h3>
                            <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $detail->bahanBaku->nama_bahan ?? '-' }} - {{ ucfirst($detail->bahanBaku->warna ?? '-') }} - {{ number_format($detail->qty_roll_pakai ?? 0) }} Roll</p>
                    </div>
                </div>

                @if(! $perluPengajuan)
                    <div class="rounded-2xl border border-[#0F034D]/10 bg-gradient-to-br from-[#0F034D]/5 via-white to-indigo-50/70 p-4 mb-4">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-[#0F034D]/60 font-semibold">Progress input</p>
                            <p class="text-2xl font-bold text-[#0F034D] mt-0.5">
                                {{ number_format($sudahDiinput, 0, ',', '.') }}
                                <span class="text-sm font-semibold text-gray-400">/ {{ number_format($targetInput, 0, ',', '.') }} pcs</span>
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $inputSelesai ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $inputSelesai ? 'Selesai' : 'Sisa ' . number_format($sisaEstimasi, 0, ',', '.') . ' pcs' }}
                            </span>
                        </div>
                    </div>

                    <div class="h-3 rounded-full bg-white border border-[#0F034D]/10 overflow-hidden">
                        <div class="h-full rounded-full {{ $inputSelesai ? 'bg-green-500' : 'bg-[#0F034D]' }} transition-all" style="width: {{ $progressPersen }}%"></div>
                    </div>

                    @php
                        $selisihKaryawan = (int) $sudahDiinput - (int) $targetInput;
                        $selisihColorClass = $selisihKaryawan < 0 
                            ? ($sudahDiinput < $batasBawah ? 'text-red-600' : 'text-amber-600') 
                            : ($selisihKaryawan > 0 ? 'text-blue-600' : 'text-green-700');
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                        <div class="rounded-xl bg-white/80 border border-white p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Batas normal</p>
                            <p class="text-sm font-bold text-green-700">&ge; {{ number_format($batasBawah, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="rounded-xl bg-white/80 border border-white p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Hasil baik</p>
                            <p class="text-sm font-bold text-blue-700">{{ number_format($hasilBaik, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="rounded-xl bg-white/80 border border-white p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Barang cacat / reject</p>
                            <p class="text-sm font-bold text-red-600">{{ number_format($totalReject, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="rounded-xl bg-white/80 border border-white p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">Selisih</p>
                            <p class="text-sm font-bold {{ $selisihColorClass }}">
                                {{ $selisihKaryawan > 0 ? '+' : '' }}{{ number_format($selisihKaryawan, 0, ',', '.') }} pcs
                            </p>
                        </div>
                    </div>
                    @if($stokReady > 0 || $totalDikeluarkan > 0)
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div class="rounded-xl bg-green-50/80 border border-green-100 p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-green-600/70 font-semibold">Stok ready</p>
                            <p class="text-sm font-bold text-green-700">{{ number_format($stokReady, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="rounded-xl bg-blue-50/80 border border-blue-100 p-2.5">
                            <p class="text-[10px] uppercase tracking-wide text-blue-600/70 font-semibold">Sudah diserahkan</p>
                            <p class="text-sm font-bold text-blue-700">{{ number_format($totalDikeluarkan, 0, ',', '.') }} pcs</p>
                        </div>
                    </div>
                    @endif
                    </div>

                @endif

                @if($role === 'potong')
                    @if($inputSelesai)
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-green-800">Hasil produksi {{ $detail->produk->nama_produk ?? 'produk ini' }} - {{ ucfirst($detail->produk->warna ?? '-') }} selesai diinputkan</p>
                                <p class="text-xs text-green-700 mt-0.5">Jika ada perubahan jumlah, silakan hubungi admin.</p>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('produksi.input-hasil.store') }}" method="POST" class="space-y-3"
                            data-swal-confirm
                            data-confirm-title="Simpan Hasil Potong?"
                            data-confirm-message="Pastikan jumlah hasil yang diinput sudah benar. Data ini akan mempengaruhi stok virtual."
                            data-confirm-button="Ya, Simpan">
                            @csrf
                            <input type="hidden" name="detail_perintah_produksi_id" value="{{ $detail->id }}">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Hasil Selesai <span class="text-xs font-normal text-gray-400">(Barang Baik / Tidak Cacat)</span></label>
                                <p class="text-[11px] text-gray-400 -mt-0.5 mb-1.5">Inputkan jumlah barang yang selesai dengan kondisi <strong>baik (tidak cacat)</strong>. Barang cacat diinputkan terpisah di kolom bawah.</p>
                                <input type="number" name="qty_selesai" min="0" max="{{ $sisaEstimasi }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Contoh: {{ max(1, $sisaEstimasi) }}">
                            </div> 
                            <div class="pt-2 border-t border-gray-100">
                                <label class="block text-sm font-semibold text-amber-700 mb-1">Jumlah Hasil Cacat / Reject <span class="text-xs font-normal text-amber-500/70">(Opsional)</span></label>
                                <input type="number" name="qty_reject" min="1" class="w-full px-4 py-3 rounded-xl border border-amber-100 bg-amber-50/30 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-200" placeholder="Isi jika ada produk reject">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 mb-1">Keterangan Cacat</label>
                                <textarea name="keterangan_cacat" rows="2" class="w-full px-4 py-3 rounded-xl border border-amber-100 bg-amber-50/30 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-200" placeholder="Contoh: Kain berlubang, jahitan rusak, noda, dll"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan Jika Hasil Final Dibawah Batas Normal</label>
                                <p class="text-[10px] text-gray-400 mt-0.5 mb-1.5">*Hasil Final = Hasil Baik + Hasil Cacat/Reject</p>
                                <textarea name="alasan" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Isi jika produk ditandai selesai tetapi total hasil masih dibawah batas normal"></textarea>
                            </div>
                            <label class="group mt-2 mb-4 flex items-start gap-4 rounded-xl bg-[#0F034D]/5 border-2 border-[#0F034D]/20 p-4 cursor-pointer transition-all hover:border-[#0F034D]/40 has-[:checked]:border-[#0F034D] has-[:checked]:bg-[#0F034D]/5">
                                <input type="checkbox" name="tandai_selesai" value="1" class="sr-only peer">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border-2 border-[#0F034D] bg-white text-transparent transition-all peer-checked:bg-white peer-checked:text-[#0F034D]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-[#0F034D]">Tandai produk ini selesai</span>
                                    <span class="block text-xs text-gray-500 mt-1 leading-relaxed">Centang sekali jika seluruh pekerjaan untuk produk ini sudah diselesaikan.</span>
                                </span>
                            </label>

                            <button type="submit" class="w-full py-3 rounded-xl bg-[#0F034D] text-white text-sm font-semibold shadow-md shadow-[#0F034D]/20 hover:bg-[#24116f] transition-colors">Simpan Hasil Pekerjaan</button>
                        </form>
                    @endif
                @else
                    @if(! $stokVirtualSaya || $targetInput <= 0)
                        <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4 flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-amber-800">Ajukan pengambilan dulu</p>
                                <p class="mt-1 text-xs leading-relaxed text-amber-700">Produk ini belum memiliki barang yang disetujui untuk tahap {{ ucfirst($role) }}. Ajukan pengambilan barang terlebih dahulu agar bisa input hasil.</p>
                                <a href="{{ route('produksi.ajuan-pengambilan.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#0F034D] hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    Klik disini untuk membuat ajuan
                                </a>
                            </div>
                        </div>
                    @elseif($inputSelesai)
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-green-800">Hasil {{ ucfirst($role) }} {{ $detail->produk->nama_produk ?? 'produk ini' }} - {{ ucfirst($detail->produk->warna ?? '-') }} selesai diinputkan</p>
                                <p class="text-xs text-green-700 mt-0.5">Ready untuk tahap berikutnya: {{ number_format($stokReady, 0, ',', '.') }} pcs.</p>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('produksi.input-hasil.store') }}" method="POST" class="space-y-3"
                            data-swal-confirm
                            data-confirm-title="Simpan Hasil Pekerjaan?"
                            data-confirm-message="Pastikan jumlah hasil yang diinput sudah benar. Data ini akan mempengaruhi stok virtual Anda."
                            data-confirm-button="Ya, Simpan">
                            @csrf
                            <input type="hidden" name="stok_virtual_id" value="{{ $stokVirtualSaya->id }}">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Hasil Selesai <span class="text-xs font-normal text-gray-400">(Barang Baik / Tidak Cacat)</span></label>
                                <p class="text-[11px] text-gray-400 -mt-0.5 mb-1.5">Inputkan jumlah barang yang selesai dengan kondisi <strong>baik (tidak cacat)</strong>. Barang cacat diinputkan terpisah di kolom bawah.</p>
                                <input type="number" name="qty_selesai" min="0" max="{{ $stokVirtualSaya->qty_hold }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Contoh: {{ max(1, (int) $stokVirtualSaya->qty_hold) }}">
                            </div>
                            <div class="pt-2 border-t border-gray-100">
                                <label class="block text-sm font-semibold text-amber-700 mb-1">Jumlah Hasil Cacat / Reject <span class="text-xs font-normal text-amber-500/70">(Opsional)</span></label>
                                <input type="number" name="qty_reject" min="1" max="{{ $stokVirtualSaya->qty_hold }}" class="w-full px-4 py-3 rounded-xl border border-amber-100 bg-amber-50/30 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-200" placeholder="Isi jika ada produk reject">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 mb-1">Keterangan Cacat</label>
                                <textarea name="keterangan_cacat" rows="2" class="w-full px-4 py-3 rounded-xl border border-amber-100 bg-amber-50/30 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-200" placeholder="Contoh: Kain berlubang, jahitan rusak, noda, dll"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan Jika Hasil Final Kurang Dari Target</label>
                                <p class="text-[10px] text-gray-400 mt-0.5 mb-1.5">*Hasil Final = Hasil Baik + Hasil Cacat/Reject</p>
                                <textarea name="alasan" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Isi jika produk ditandai selesai tetapi total hasil masih dibawah batas normal"></textarea>
                            </div>
                            <label class="group mt-2 mb-4 flex items-start gap-4 rounded-xl bg-[#0F034D]/5 border-2 border-[#0F034D]/20 p-4 cursor-pointer transition-all hover:border-[#0F034D]/40 has-[:checked]:border-[#0F034D] has-[:checked]:bg-[#0F034D]/5">
                                <input type="checkbox" name="tandai_selesai" value="1" class="sr-only peer">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border-2 border-[#0F034D] bg-white text-transparent transition-all peer-checked:bg-white peer-checked:text-[#0F034D]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-[#0F034D]">Tandai produk ini selesai</span>
                                    <span class="block text-xs text-gray-500 mt-1 leading-relaxed">Centang sekali jika seluruh pekerjaan untuk produk ini sudah diselesaikan.</span>
                                </span>
                            </label>

                            <button type="submit" class="w-full py-3 rounded-xl bg-[#0F034D] text-white text-sm font-semibold shadow-md shadow-[#0F034D]/20 hover:bg-[#24116f] transition-colors">Simpan Hasil Pekerjaan</button>
                        </form>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</x-layouts.produksi>


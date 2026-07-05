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

    @if(session('success'))
        <div class="mb-4 rounded-2xl bg-green-50 border border-green-100 p-4 flex items-start gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-green-800">Berhasil</p>
                <p class="text-xs text-green-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 border border-red-100 p-4 shadow-sm">
            <p class="text-sm font-bold text-red-700">Input belum bisa disimpan</p>
            <ul class="mt-2 space-y-1 text-xs text-red-600 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-3xl bg-gradient-to-br from-[#0F034D] to-[#24116f] p-5 text-white shadow-xl shadow-[#0F034D]/20 mb-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs text-white/60">Nomor WO</p>
                <h2 class="text-xl font-bold mt-1">{{ $perintahProduksi->nomor_wo }}</h2>
                <p class="text-sm text-white/75 mt-2">Mulai {{ \Carbon\Carbon::parse($perintahProduksi->tgl_mulai)->format('d M Y') }}</p>
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
                $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;
                $stokVirtualSaya = \App\Models\StokVirtual::where('id_detail_perintah', $detail->id)
                    ->where('id_karyawan', auth()->id())
                    ->where('peran', $role)
                    ->first();
                $sudahDiinput = (int) ($stokVirtualSaya?->total_selesai ?? 0);
                $sisaEstimasi = max(0, $detail->estimasi_pcs - $sudahDiinput);
                $inputSelesai = $sudahDiinput >= $detail->estimasi_pcs;
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <h3 class="text-sm font-bold text-[#0F034D] truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</h3>
                            <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Bahan: {{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-[#0F034D]/60">Estimasi</p>
                        <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }} pcs</p>
                    </div>
                    <div class="rounded-xl bg-green-50 border border-green-100 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-green-700/70">Batas normal</p>
                        <p class="text-sm font-bold text-green-800">≥ {{ number_format($batasBawah, 0, ',', '.') }} pcs</p>
                    </div>
                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-3">
                        <p class="text-[10px] uppercase tracking-wide text-blue-700/70">Sudah diinput</p>
                        <p class="text-sm font-bold text-blue-800">{{ number_format($sudahDiinput, 0, ',', '.') }} pcs</p>
                    </div>
                    <div class="rounded-xl {{ $inputSelesai ? 'bg-gray-50 border-gray-100' : 'bg-amber-50 border-amber-100' }} border p-3">
                        <p class="text-[10px] uppercase tracking-wide {{ $inputSelesai ? 'text-gray-500' : 'text-amber-700/70' }}">Sisa estimasi</p>
                        <p class="text-sm font-bold {{ $inputSelesai ? 'text-gray-600' : 'text-amber-800' }}">{{ number_format($sisaEstimasi, 0, ',', '.') }} pcs</p>
                    </div>
                </div>

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
                        <form action="{{ route('produksi.input-hasil.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="detail_perintah_produksi_id" value="{{ $detail->id }}">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Jumlah hasil selesai</label>
                                <input type="number" name="qty_selesai" min="1" max="{{ $sisaEstimasi }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Contoh: {{ max(1, $sisaEstimasi) }}">
                            </div>
                            <label class="flex items-start gap-3 rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3">
                                <input type="checkbox" name="tandai_selesai" value="1" class="mt-0.5 rounded border-gray-300 text-[#0F034D] focus:ring-[#0F034D]">
                                <span>
                                    <span class="block text-xs font-bold text-[#0F034D]">Tandai produk ini selesai</span>
                                    <span class="block text-[11px] text-gray-500 mt-0.5">Centang hanya jika hasil untuk produk ini sudah final. Jika total final masih di bawah batas normal, alasan wajib diisi.</span>
                                </span>
                            </label>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Alasan jika hasil final di bawah toleransi</label>
                                <textarea name="alasan" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Isi jika produk ditandai selesai tetapi totalnya kurang dari batas normal"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3 rounded-xl bg-[#0F034D] text-white text-sm font-bold shadow-md shadow-[#0F034D]/20 hover:bg-[#24116f] transition-colors">Simpan hasil pekerjaan</button>
                        </form>
                    @endif
                @else
                    <div class="rounded-xl bg-amber-50 border border-amber-100 p-3">
                        <p class="text-xs text-amber-700 leading-relaxed">Input untuk tahap {{ ucfirst($role) }} dilakukan dari stok virtual/barang yang sudah diajukan dan diterima. Daftar stok pegangan akan ditampilkan pada pengembangan berikutnya.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-layouts.produksi>

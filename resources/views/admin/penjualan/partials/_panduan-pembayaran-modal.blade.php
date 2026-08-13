{{-- Slide-Over Right Panel Panduan Pembayaran --}}
<div id="modal-panduan-pembayaran" class="slide-panel">
    <div class="slide-panel-backdrop" onclick="closePanduanPembayaranModal()"></div>
    <div class="slide-panel-body">
        <div class="slide-panel-header">
            <div class="slide-panel-header-icon">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="slide-panel-header-title text-base font-bold text-[#0F034D]">Info & Panduan Edit</h2>
                <p class="text-xs text-gray-500">Ringkasan transaksi dan petunjuk perubahan data</p>
            </div>
            <button type="button" class="slide-panel-close" onclick="closePanduanPembayaranModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="slide-panel-content space-y-4">
            <!-- Ringkasan Keuangan Saat Ini -->
            <div class="space-y-2.5">
                <h4 class="text-xs font-bold text-[#0F034D] uppercase tracking-wider">Status Keuangan Terdaftar</h4>
                <div class="space-y-2 text-xs">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Total Uang Diterima:</span>
                        <span class="font-bold text-green-700 text-sm">Rp {{ number_format($penjualan->total_dibayar, 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Status Tagihan Nota:</span>
                        @php $st = $penjualan->status_pembayaran; @endphp
                        @if($st === 'lunas')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">LUNAS</span>
                        @elseif($st === 'sebagian')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">SEBAGIAN (DP)</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">BELUM BAYAR</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Petunjuk Skenario Perubahan -->
            <div class="space-y-2.5 pt-2 border-t border-gray-100">
                <h4 class="text-xs font-bold text-[#0F034D] uppercase tracking-wider">💡 Petunjuk Perubahan Data:</h4>

                <div class="space-y-3 text-xs">
                    <!-- Skenario A: Tambah Qty / Tambah Produk -->
                    <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/80 space-y-1">
                        <div class="font-bold text-amber-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>1. Jika Menambah Produk / Qty (Total Naik)</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed pl-5">
                            • Sisa tagihan akan otomatis bertambah.<br>
                            • Status pembayaran berubah menjadi <strong>SEBAGIAN (DP)</strong>.<br>
                            • Kekurangan pembayaran dapat dicatat lewat tombol <em>"+ Tambah Pembayaran"</em> di halaman detail nota.
                        </p>
                    </div>

                    <!-- Skenario B: Kurang Qty / Hapus Produk -->
                    <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200/80 space-y-1">
                        <div class="font-bold text-blue-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                            <span>2. Jika Mengurangi Qty / Hapus Produk (Total Turun)</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed pl-5">
                            • Sistem menghitung ulang sisa tagihan secara otomatis.<br>
                            • Jika uang yang diterima sudah sama atau melebihi total baru, status otomatis menjadi <strong>LUNAS</strong>.<br>
                            • Jika ada kelebihan bayar, Admin dapat menyesuaikan entri pembayaran di halaman detail nota.
                        </p>
                    </div>
                </div>
                
                <p class="text-[11px] text-gray-500 italic pt-2">
                    * Catatan: Semua foto bukti transfer & riwayat transaksi uang masuk sebelumnya tersimpan aman.
                </p>
            </div>
        </div>

        <div class="slide-panel-footer">
            <button type="button" class="btn-panel-submit w-full text-center justify-center cursor-pointer" onclick="closePanduanPembayaranModal()">Tutup Panduan</button>
        </div>
    </div>
</div>

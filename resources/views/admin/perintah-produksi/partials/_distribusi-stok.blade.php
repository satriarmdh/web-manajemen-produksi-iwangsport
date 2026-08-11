@php
    $stokVirtualAll = $perintahProduksi->stokVirtual->where('id_detail_perintah', $detail->id);

    // Aggregate stok info per karyawan + registry records (cacat + selisih)
    $karyawanStok = collect();
    $stokRecordsRegistry = [];
    foreach ($stokVirtualAll as $stok) {
        $key = $stok->id_karyawan . '|' . $stok->peran;

        // Records cacat milik karyawan current pada detail ini
        $cacatRecords = [];
        if ($stok->relationLoaded('produkCacat')) {
            foreach ($stok->produkCacat->where('id_karyawan', $stok->id_karyawan) as $cacat) {
                $cacatRecords[] = [
                    'qty' => (int) $cacat->qty_reject,
                    'keterangan' => $cacat->keterangan ?? '-',
                    'tahapan' => ucfirst($cacat->tahapan ?? '-'),
                    'tgl' => $cacat->tgl_lapor ? $cacat->tgl_lapor->format('d M Y, H:i') : '-',
                    'jenis' => 'cacat',
                ];
            }
        }

        // Record selisih dari validasi stok. Tahap potong menyimpan validasinya di detail produk.
        $selisihAlasan = $stok->alasan;
        $isSelisih = $stok->status_validasi === 'flag';
        $selisihQty = (int) $stok->qty_hold;
        $selisihTgl = $stok->selisih_dicatat_at ?? $detail->updated_at;
        if ($stok->peran === 'potong' && $detail->status_validasi_potong === 'flag') {
            $isSelisih = true;
            $selisihAlasan = $detail->alasan;
            $batasBawahPotong = (int) $detail->estimasi_pcs - (int) $detail->toleransi_minus;
            $totalInputPotong = (int) $detail->qty_pcs_potong + (int) $stok->total_reject;
            $selisihQty = max(0, $batasBawahPotong - $totalInputPotong);
            $selisihTgl = $stok->selisih_dicatat_at ?? $detail->updated_at;
        }

        $selisihRecord = null;
        if ($isSelisih && !empty($selisihAlasan)) {
            $selisihRecord = [
                'qty' => $selisihQty,
                'keterangan' => $selisihAlasan,
                'tahapan' => ucfirst($stok->peran),
                'tgl' => $selisihTgl ? $selisihTgl->format('d M Y, H:i') : '-',
                'jenis' => 'selisih',
            ];
        }

        // Mutasi records milik karyawan current pada detail ini (sebagai pengirim atau penerima)
        $mutasiRecords = [];
        foreach ($detail->mutasiProduksi->sortByDesc('created_at') as $mutasi) {
            if ($mutasi->dari_karyawan_id == $stok->id_karyawan || $mutasi->ke_karyawan_id == $stok->id_karyawan) {
                $qtyPindah = (int) $mutasi->qty_pindah;
                $tgl = $mutasi->tgl_transaksi ? $mutasi->tgl_transaksi->format('d M Y, H:i') : $mutasi->created_at->format('d M Y, H:i');
                
                if ($mutasi->ke_karyawan_id == $stok->id_karyawan) {
                    $arah = 'masuk';
                    $keterangan = "Menerima dari " . ($mutasi->dariKaryawan->name ?? 'Sistem') . " (" . ucfirst($mutasi->dari_tahapan ?? 'awal') . ")";
                } else {
                    $arah = 'keluar';
                    $keterangan = "Menyerahkan ke " . ($mutasi->keKaryawan->name ?? '-') . " (" . ucfirst($mutasi->ke_tahapan) . ")";
                }

                $mutasiRecords[] = [
                    'qty' => $qtyPindah,
                    'arah' => $arah,
                    'keterangan' => $keterangan,
                    'tgl' => $tgl,
                    'jenis' => 'mutasi',
                ];
            }
        }

        $cacatDiserahkan = (int) $detail->penerimaanHasilProduksi
            ->where('dari_karyawan_id', $stok->id_karyawan)
            ->where('jenis_penerimaan', 'cacat')
            ->sum('qty_diterima');

        $stokRecordsRegistry[$key] = [
            'cacat' => $cacatRecords,
            'selisih' => $selisihRecord,
            'mutasi' => $mutasiRecords,
        ];

        $karyawanStok->push([
            'karyawan_id' => $stok->id_karyawan,
            'stok_key' => $key,
            'karyawan_name' => $stok->karyawan->name ?? '-',
            'peran' => $stok->peran,
            'qty_hold' => (int) $stok->qty_hold,
            'total_selesai' => (int) $stok->total_selesai,
            'total_dikeluarkan' => (int) $stok->total_dikeluarkan,
            'total_reject' => (int) $stok->total_reject,
            'cacat_diserahkan' => $cacatDiserahkan,
            'cacat_sisa' => max(0, (int) $stok->total_reject - $cacatDiserahkan),
            'is_selesai' => $stok->is_selesai,
            'status_barang' => $stok->status_barang,
            'selisih_qty' => $selisihQty,
            'selisih_flag' => $isSelisih,
        ]);
    }

    $estimasi = $detail->estimasi_pcs;
    $diterima = $detail->total_qty_diterima;
    $sisa = $estimasi - $diterima;
@endphp
{{-- Registry JSON per detail produk untuk JS render paginated records di slide panel --}}
<script type="application/json" id="stokRecordsRegistry-{{ $detail->id }}">{!! json_encode($stokRecordsRegistry, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) !!}</script>

<!-- Tab Content: Stok Karyawan -->
<div class="space-y-2">
    @forelse($karyawanStok as $index => $item)
        @php
            $readyToTransfer = max(0, $item['total_selesai'] - $item['total_dikeluarkan']);

            // When selisih recorded, qty_hold is already resolved → treat as 0
            $hasSelisih = $item['selisih_flag'];
            $wipInput = $hasSelisih ? 0 : (int) $item['qty_hold'];

            $statusBadge = '';
            $statusClass = '';
            if ($wipInput > 0) {
                $statusBadge = 'Diproses';
                $statusClass = 'bg-amber-50 text-amber-700 border-amber-100';
            } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer > 0) {
                $statusBadge = 'Siap Diserahkan';
                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
            } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer == 0) {
                $statusBadge = 'Selesai';
                $statusClass = 'bg-gray-50 text-gray-600 border-gray-100';
            }
        @endphp
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center gap-2 min-w-0 flex-1 flex-wrap">
                <span class="font-bold text-gray-800 text-xs">{{ $item['karyawan_name'] }}</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold shrink-0">{{ ucfirst($item['peran']) }}</span>

                @if($statusBadge)
                    <span class="text-[9px] px-2 py-0.5 rounded-full {{ $statusClass }} font-bold shrink-0 border">{{ $statusBadge }}</span>
                @endif

                <div class="flex items-center gap-1.5 ml-auto mr-2">
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-semibold shrink-0 border border-green-100">
                        {{ number_format($item['total_selesai'] + $item['total_reject']) }} selesai
                    </span>
                </div>
            </div>

            <button type="button"
                data-view-stok-detail
                data-registry-id="stokRecordsRegistry-{{ $detail->id }}"
                data-stok-key="{{ $item['stok_key'] }}"
                data-karyawan-name="{{ $item['karyawan_name'] }}"
                data-peran="{{ $item['peran'] }}"
                data-qty-hold="{{ $wipInput }}"
                data-total-selesai="{{ $item['total_selesai'] + $item['total_reject'] }}"
                data-total-dikeluarkan="{{ $item['total_dikeluarkan'] }}"
                data-total-reject="{{ $item['total_reject'] }}"
                data-cacat-diserahkan="{{ $item['cacat_diserahkan'] }}"
                data-cacat-sisa="{{ $item['cacat_sisa'] }}"
                data-ready-qty="{{ $readyToTransfer }}"
                data-status-barang="{{ $item['status_barang'] }}"
                data-is-selesai="{{ $item['is_selesai'] ? '1' : '0' }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-colors border border-blue-100 cursor-pointer shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Detail
            </button>
        </div>
    @empty
        <p class="text-xs text-gray-400 py-4 text-center">Belum ada informasi stok karyawan.</p>
    @endforelse
</div>

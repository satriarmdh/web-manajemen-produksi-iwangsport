<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penjualan - {{ $penjualan->nomor_invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #0F034D;
            padding-bottom: 15px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: bold;
            color: #0F034D;
            margin: 0;
        }
        .brand-sub {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }
        .doc-title {
            font-size: 18px;
            font-weight: bold;
            color: #0F034D;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }
        .invoice-no {
            font-size: 12px;
            font-weight: bold;
            color: #444;
            text-align: right;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
            vertical-align: top;
        }
        .info-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #0F034D;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .info-row {
            margin-bottom: 4px;
            font-size: 11px;
        }
        .info-label {
            display: inline-block;
            width: 110px;
            color: #6b7280;
        }
        .info-val {
            font-weight: bold;
            color: #111827;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0F034D;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 8px 10px;
            text-align: left;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .summary-box {
            width: 40%;
            margin-left: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: #f9fafb;
            padding: 10px 12px;
        }
        .summary-row {
            margin-bottom: 6px;
            font-size: 11px;
        }
        .summary-row.total {
            font-size: 13px;
            font-weight: bold;
            color: #0F034D;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
            margin-bottom: 0;
        }
        .notes-section {
            margin-top: 20px;
            padding: 10px 12px;
            background-color: #f3f4f6;
            border-radius: 6px;
            font-size: 11px;
            color: #4b5563;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            vertical-align: top;
            font-size: 11px;
        }
        .signature-space {
            height: 60px;
        }
        .footer {
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

@php
    $logoPath = public_path('images/logo-primary.png');
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
@endphp

    {{-- Top Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 62%; vertical-align: middle;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        @if($logoBase64)
                            <td style="width: 46px; vertical-align: middle; padding-right: 10px;">
                                <img src="data:image/png;base64,{{ $logoBase64 }}" style="width: 42px; height: 42px; border-radius: 50%;">
                            </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <h1 class="brand-title">IWANG SPORT</h1>
                            <div class="brand-sub">Gang Sindoro Dusun 003, RT 07 RW 03, Desa Ambokulon, Kec. Ulujami, Kab. Pemalang, Kode Pos 52363</div>
                            <div class="brand-sub">No. Telp: 08XXXXX</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 38%; vertical-align: middle; text-align: right;">
                <div class="doc-title">NOTA PENJUALAN</div>
                <div class="invoice-no">{{ $penjualan->nomor_invoice }}</div>
                <div class="brand-sub" style="margin-top: 4px;">Tanggal: {{ $penjualan->tanggal->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Info Pelanggan & Admin --}}
    <table class="info-table">
        <tr>
            <td class="info-box">
                <div class="info-box-title">Informasi Pelanggan</div>
                <div class="info-row">
                    <span class="info-label">Nama Pelanggan:</span>
                    <span class="info-val">{{ $penjualan->pelanggan->nama_pelanggan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Telepon:</span>
                    <span class="info-val">{{ $penjualan->pelanggan->no_telp ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat:</span>
                    <span class="info-val">{{ $penjualan->pelanggan->alamat ?? '-' }}</span>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td class="info-box">
                <div class="info-box-title">Informasi Transaksi</div>
                <div class="info-row">
                    <span class="info-label">No. Invoice:</span>
                    <span class="info-val">{{ $penjualan->nomor_invoice }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Transaksi:</span>
                    <span class="info-val">{{ $penjualan->tanggal->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kasir / Admin:</span>
                    <span class="info-val">{{ $penjualan->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Pembayaran:</span>
                    @php $st = $penjualan->status_pembayaran; @endphp
                    @if($st === 'lunas')
                        <span class="info-val" style="color: #059669; font-weight: bold;">LUNAS</span>
                    @elseif($st === 'sebagian')
                        <span class="info-val" style="color: #d97706; font-weight: bold;">SEBAGIAN (DP)</span>
                    @else
                        <span class="info-val" style="color: #dc2626; font-weight: bold;">BELUM BAYAR</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Detail Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 18%;">Kode</th>
                <th style="width: 37%;">Nama & Warna Produk</th>
                <th style="width: 12%; text-align: center;">Qty</th>
                <th style="width: 14%; text-align: right;">Harga Satuan</th>
                <th style="width: 14%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detailPenjualan as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $detail->produk?->kode_produk ?? '-' }}</td>
                    <td>
                        <strong>{{ $detail->produk?->nama_produk }}</strong>
                        @if($detail->produk?->warna)
                            <span style="color: #6b7280;"> - {{ ucfirst($detail->produk->warna) }}</span>
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ number_format($detail->qty, 0, ',', '.') }}</strong> pcs</td>
                    <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary Box --}}
    <table class="summary-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if($penjualan->catatan)
                    <div class="notes-section">
                        <strong>Catatan / Keterangan:</strong><br>
                        {{ $penjualan->catatan }}
                    </div>
                @endif
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="summary-box" style="width: 90%;">
                    <div class="summary-row">
                        <span style="display: inline-block; width: 50%; color: #6b7280;">Total Kuantitas:</span>
                        <span style="display: inline-block; width: 45%; text-align: right; font-weight: bold;">{{ number_format($penjualan->total_item, 0, ',', '.') }} pcs</span>
                    </div>
                    <div class="summary-row">
                        <span style="display: inline-block; width: 50%; color: #6b7280;">Total Harga:</span>
                        <span style="display: inline-block; width: 45%; text-align: right; font-weight: bold;">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span style="display: inline-block; width: 50%; color: #6b7280;">Telah Dibayar:</span>
                        <span style="display: inline-block; width: 45%; text-align: right; font-weight: bold; color: #059669;">Rp {{ number_format($penjualan->total_dibayar, 0, ',', '.') }}</span>
                    </div>
                    @if($penjualan->sisa_pembayaran > 0)
                        <div class="summary-row total" style="color: #d97706;">
                            <span style="display: inline-block; width: 50%;">Sisa Tagihan:</span>
                            <span style="display: inline-block; width: 45%; text-align: right;">Rp {{ number_format($penjualan->sisa_pembayaran, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="summary-row total">
                            <span style="display: inline-block; width: 50%;">Status Tagihan:</span>
                            <span style="display: inline-block; width: 45%; text-align: right; color: #059669;">LUNAS</span>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Signatures --}}
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                Penerima / Pelanggan,
                <div class="signature-space"></div>
                <strong>( {{ $penjualan->pelanggan->nama_pelanggan }} )</strong>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                Kasir / Petugas Admin,
                <div class="signature-space"></div>
                <strong>( {{ $penjualan->user->name }} )</strong>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Terima kasih atas kepercayaan Anda berbelanja di Iwang Sport. Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan kecuali ada perjanjian sebelumnya.
    </div>

</body>
</html>

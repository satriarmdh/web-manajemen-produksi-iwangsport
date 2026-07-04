<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perintah Produksi - {{ $perintahProduksi->nomor_wo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0F034D;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0F034D;
            font-size: 24px;
        }
        .header h2 {
            margin: 10px 0 0 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            display: inline-block;
            width: 180px;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            display: inline-block;
        }
        .table-section {
            margin-top: 20px;
        }
        .table-section h3 {
            color: #0F034D;
            margin-bottom: 15px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #0F034D;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #ffc107;
            color: #333;
        }
        .status-disetujui {
            background-color: #17a2b8;
            color: white;
        }
        .status-dalam_produksi {
            background-color: #007bff;
            color: white;
        }
        .status-selesai {
            background-color: #28a745;
            color: white;
        }
        .status-ditolak {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PERINTAH PRODUKSI</h1>
        <h2>{{ $perintahProduksi->nomor_wo }}</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nomor Work Order:</span>
            <span class="info-value">{{ $perintahProduksi->nomor_wo }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Mulai:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($perintahProduksi->tgl_mulai)->format('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $perintahProduksi->status_produksi }}">
                    {{ ucfirst(str_replace('_', ' ', $perintahProduksi->status_produksi)) }}
                </span>
            </span>
        </div>
        @if($perintahProduksi->tgl_selesai)
        <div class="info-row">
            <span class="info-label">Tanggal Selesai:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($perintahProduksi->tgl_selesai)->format('d F Y') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Dibuat Oleh:</span>
            <span class="info-value">{{ $perintahProduksi->user->name ?? '-' }}</span>
        </div>
        @if($perintahProduksi->approver)
        <div class="info-row">
            <span class="info-label">Disetujui Oleh:</span>
            <span class="info-value">{{ $perintahProduksi->approver->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Persetujuan:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($perintahProduksi->approved_at)->format('d F Y H:i') }}</span>
        </div>
        @endif
    </div>

    <div class="table-section">
        <h3>Detail Produk</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Produk</th>
                    <th width="20%">Bahan Baku</th>
                    <th width="15%" class="text-center">Qty Roll</th>
                    <th width="15%" class="text-center">Estimasi PCS</th>
                    <th width="15%" class="text-center">Qty Potong</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perintahProduksi->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                    <td>{{ $detail->bahanBaku->nama_bahan ?? '-' }}</td>
                    <td class="text-center">{{ $detail->qty_roll_pakai }}</td>
                    <td class="text-center">{{ $detail->estimasi_pcs }}</td>
                    <td class="text-center">{{ $detail->qty_pcs_potong ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>
</html>

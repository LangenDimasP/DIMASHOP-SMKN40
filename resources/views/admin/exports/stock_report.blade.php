<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #00a8a8;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 22px;
            color: #007b7b;
            margin: 0;
        }
        .period {
            font-size: 14px;
            color: #555;
            margin-top: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px;
        }
        th {
            background-color: #e0f7f7;
            color: #006666;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #b2dfdb;
        }
        td {
            padding: 9px 8px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Highlight low stock */
        .low-stock {
            color: #c62828;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STOK BARANG</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Stok Awal</th>
                <th>Barang Masuk</th>
                <th>Barang Keluar</th>
                <th>Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockData as $data)
                @php
                    $sisa = $data['sisa_stok'] ?? 0;
                    $isLow = $sisa <= 5; // Sesuaikan threshold stok rendah sesuai kebutuhan
                @endphp
                <tr>
                    <td>{{ $data['kode_produk'] }}</td>
                    <td style="text-align: left;">{{ $data['nama_produk'] }}</td>
                    <td>{{ $data['stok_awal'] }}</td>
                    <td>{{ $data['barang_masuk'] }}</td>
                    <td>{{ $data['barang_keluar'] }}</td>
                    <td class="{{ $isLow ? 'low-stock' : '' }}">{{ $sisa }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Tidak ada data stok dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
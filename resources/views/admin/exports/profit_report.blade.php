<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuntungan / Laba-Rugi</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #00a8a8;
        }
        .header h1 {
            font-size: 18px;
            color: #007b7b;
            margin: 0;
        }
        .period {
            font-size: 12px;
            color: #555;
            margin-top: 6px;
        }

        .summary {
            background-color: #f8fdfd;
            border: 1px solid #d1eaea;
            border-radius: 6px;
            padding: 14px;
            margin-bottom: 25px;
        }
        .summary h2 {
            font-size: 14px;
            color: #007b7b;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .summary p {
            margin: 6px 0;
            font-size: 12px;
        }
        .summary .profit {
            color: #2e7d32;
            font-weight: bold;
        }
        .summary .loss {
            color: #c62828;
            font-weight: bold;
        }

        h2.section-title {
            font-size: 14px;
            color: #007b7b;
            margin: 25px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e0f7f7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        th {
            background-color: #e0f7f7;
            color: #006666;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #b2dfdb;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        td.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUNTUNGAN / LABA-RUGI</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <div class="summary">
        <h2>Ringkasan Keuangan</h2>
        <p><strong>Pendapatan Total:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p><strong>HPP (Harga Pokok Penjualan):</strong> Rp {{ number_format($totalHPP, 0, ',', '.') }}</p>
        <p><strong>Diskon / Promo:</strong> Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
        <p>
            <strong>Laba Bersih:</strong> 
            <span class="{{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </span>
        </p>
    </div>

    <h2 class="section-title">Detail Produk Terjual</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
                <th>HPP Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productDetails as $detail)
                <tr>
                    <td>{{ $detail['unique_code'] }}</td>
                    <td>{{ $detail['name'] }}</td>
                    <td style="text-align: center;">{{ $detail['quantity'] }}</td>
                    <td class="amount">Rp {{ number_format($detail['price'] * $detail['quantity'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #888;">Tidak ada penjualan dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
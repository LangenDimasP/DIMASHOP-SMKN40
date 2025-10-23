<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Performa Kasir</title>
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
        td.center {
            text-align: center;
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
        <h1>LAPORAN PERFORMA KASIR</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <h2 class="section-title">Ringkasan Performa</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Kasir</th>
                <th>Total Transaksi</th>
                <th>Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kasirPerformance as $kasir)
                <tr>
                    <td>{{ $kasir->kasir_name }}</td>
                    <td class="center">{{ $kasir->total_transactions }}</td>
                    <td class="amount">Rp {{ number_format($kasir->total_revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #888;">Tidak ada data performa kasir dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
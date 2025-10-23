<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi / Pembayaran</title>
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
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .total {
            font-size: 15px;
            font-weight: bold;
            color: #006666;
            text-align: right;
            margin: 10px 0 25px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-success { background-color: #e8f5e9; color: #2e7d32; }
        .status-pending { background-color: #fff8e1; color: #f57f17; }
        .status-failed { background-color: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI / PEMBAYARAN</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal & Waktu</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->unique_code }}</td>
                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                    <td>
                        @php
                            $status = strtolower($transaction->status);
                            $badgeClass = 'status-' . ($status === 'completed' || $status === 'success' ? 'success' : ($status === 'pending' ? 'pending' : 'failed'));
                        @endphp
                        <span class="status-badge {{ $badgeClass }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #888;">Tidak ada transaksi dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">Total Transaksi: {{ $transactions->count() }}</div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
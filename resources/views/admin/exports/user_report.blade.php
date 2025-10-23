<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengguna / Member</title>
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
            padding: 12px;
            margin-bottom: 20px;
        }
        .summary h2 {
            font-size: 14px;
            color: #007b7b;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .summary p {
            margin: 4px 0;
            font-size: 12px;
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
            font-size: 11px;
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
        <h1>LAPORAN PENGGUNA / MEMBER</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <div class="summary">
        <h2>Ringkasan</h2>
        <p><strong>Jumlah Pengguna Baru:</strong> {{ $newUsersCount }}</p>
        <p><strong>Total Pengguna Aktif:</strong> {{ $activeUsersCount }}</p>
    </div>

    <h2 class="section-title">Daftar Pelanggan Terdaftar</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Tanggal Daftar</th>
                <th>Points</th>
                <th>Saldo Dimascash</th>
                <th>Total Transaksi</th>
                <th>Level Membership</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userData as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['created_at'] }}</td>
                    <td style="text-align: right;">{{ $user['points'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($user['dimascash_balance'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $user['total_transactions'] }}</td>
                    <td>{{ $user['level'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #888;">Tidak ada data pengguna dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
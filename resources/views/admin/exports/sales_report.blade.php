<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
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
            font-size: 12px;
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
        tr.transaction-start {
            border-top: 2px solid #00a8a8 !important;
        }
        tr.transaction-start td {
            border-top: 2px solid #00a8a8 !important;
        }

        .section-title {
            font-size: 16px;
            color: #007b7b;
            margin: 25px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #d1eaea;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <div class="period">Periode: {{ $startDate }} – {{ $endDate }}</div>
    </div>

    <div class="section-title">Detail Transaksi</div>
    <table>
        <thead>
            <tr>
                <th>Nomor Transaksi</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Diskon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                @php
                    $items = json_decode($transaction->items, true);
                @endphp
                @if(is_array($items) && count($items) > 0)
                    @foreach($items as $index => $item)
                        @php
                            $product = \App\Models\Product::find($item['product_id'] ?? null);
                        @endphp
                        <tr class="{{ $index == 0 ? 'transaction-start' : '' }}">
                            <td>{{ $transaction->unique_code }}</td>
                            <td>{{ $product ? $product->name : 'Produk Tidak Ditemukan' }}</td>
                            <td style="text-align: center;">{{ $item['quantity'] ?? 0 }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item['discount'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="total">Total Penjualan: Rp {{ number_format($totalSales, 0, ',', '.') }}</div>

    <div class="section-title">Produk Paling Laris</div>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td style="text-align: center;">{{ $product['quantity'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #888;">Tidak ada data penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} • Sistem Manajemen Toko
    </div>
</body>
</html>
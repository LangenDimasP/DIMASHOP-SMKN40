<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }

        @media print {
            body {
                font-size: 10px;
                margin: 0;
                padding: 5px;
                width: 58mm;
                border: none;
                /* Hapus border saat print */
                background: none;
                /* Hapus background saat print */
                font-weight: bold;
                /* Tambahkan bold untuk tebal */
                color: #000;
                /* Pastikan warna hitam solid */
            }

            .no-print {
                display: none;
            }

            button,
            a {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            /* Font seperti mesin kasir */
            padding: 10px;
            max-width: 203px;
            /* 58mm ≈ 203px pada 203 DPI */
            margin: auto;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 12px;
            line-height: 1.2;
            font-weight: bold;
            /* Tambahkan bold untuk tebal */
            color: #000;
            /* Pastikan warna hitam solid */
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #42bec4;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
            color: #42bec4;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            font-weight: bold;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 10px;
            font-weight: bold;
        }

        .item span:first-child {
            flex: 1;
            margin-right: 5px;
        }

        .item span:last-child {
            text-align: right;
        }

        .total {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
            text-align: center;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .no-print {
            margin-top: 10px;
            text-align: center;
        }

        .no-print button,
        .no-print a {
            margin: 0 3px;
            padding: 5px 8px;
            background: #42bec4;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 10px;
        }

        .no-print a {
            background: #ccc;
        }

        .voucher-info {
            font-size: 9px;
            margin: 3px 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Dimashop</h2>
        <p><strong>ID Transaksi:</strong> {{ $transaction->unique_code }}</p>
        <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
        <p><strong>Kasir:</strong> {{ $transaction->kasir->name ?? 'Offline' }}</p>
    </div>

    <div class="items">
        @php
            $items = json_decode($transaction->items, true);
        @endphp
        @foreach($items as $item)
            <div class="item">
                <span>{{ $item['name'] }} ({{ $item['quantity'] }}x)</span>
                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    @if($transaction->discount_amount > 0)
        <div class="voucher-info">
            <p>Potongan Voucher ({{ $transaction->voucher_code }}): -Rp
                {{ number_format($transaction->discount_amount, 0, ',', '.') }}</p>
        </div>
    @endif

    <div class="total">
        <p><strong>Total: Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong></p>
        <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
    </div>

    <div class="footer">
        <p>Terima Kasih atas Pembelian Anda!</p>
        <p>Selamat Berbelanja Kembali</p>
    </div>

    <div class="no-print">
        <button onclick="window.location.href='/kasir/transaksi/{{ $transaction->id }}/print-direct'">Cetak
            Struk</button>
    </div>
</body>

</html>
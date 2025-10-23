<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        /* Desain untuk thermal printer (lebar 58mm) */
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
            width: 58mm; /* Lebar thermal printer standar */
            max-width: 58mm;
            background: white;
            color: black;
        }
        .center { text-align: center; }
        .left { text-align: left; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed black; margin: 5px 0; }
        .item { margin: 5px 0; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="center bold">Dimashop</div>
    <div class="center">ID Transaksi: {{ $transaction->unique_code }}</div>
    <div class="center">Tanggal: {{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
    <div class="center">User: {{ $transaction->user->name }}</div>
    <div class="center">Metode: {{ ucfirst($transaction->payment_method ?? 'Dimascash') }}</div>
    <div class="line"></div>

    @foreach($items as $item)
        <div class="item">
            <div>{{ $item['name'] }}
                @if(isset($item['is_tebus_murah']) && $item['is_tebus_murah']) [Tebus Murah] @endif
                @if(!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_get_y_free' && !empty($item['free_items'])) [Gratis {{ $item['free_items'] }} pcs] @endif
                @if(!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_for_y') [Promo: {{ $item['promo_desc'] ?? '' }}] @endif
            </div>
            <div>{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }} = Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
        </div>
    @endforeach

    <div class="line"></div>
    <div class="bold">Total: Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
    <div>Status: {{ ucfirst($transaction->status) }}</div>
    <div class="center">Terima Kasih atas Pembelian Anda!</div>
    <div class="center">Selamat Berbelanja Kembali</div>

    <!-- Tombol print (tidak tampil saat print) -->
    <div class="center no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px; background: cyan; color: white; border: none; cursor: pointer;">Print Struk</button>
    </div>

    <script>
        // Auto print saat load (opsional, hapus jika ingin manual)
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">point_of_sale</span>
                    Transaksi Kasir
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola dan lihat riwayat transaksi</p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Search Transaksi Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-icons text-cyan-500">search</span>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Cari Transaksi</h2>
                    </div>
                    <form method="GET" action="{{ route('kasir.transaksi.index') }}" class="space-y-3">
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                            <input 
                                type="text" 
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Masukkan kode transaksi..." 
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            >
                        </div>
                        <button 
                            type="submit"
                            class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <span class="material-icons mr-2">search</span>
                            Cari Transaksi
                        </button>
                    </form>
                </div>

                <!-- New Transaction Card -->
                <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-sm p-4 sm:p-6 text-white">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-icons text-white">add_shopping_cart</span>
                        </div>
                        <h2 class="text-lg font-semibold">Transaksi Baru</h2>
                    </div>
                    
                    <p class="text-white text-opacity-90 mb-4 text-sm">
                        Mulai transaksi baru dengan scan produk
                    </p>
                    
                    <a 
                        href="{{ route('kasir.scan.page') }}" 
                        class="inline-flex items-center bg-white text-cyan-600 font-semibold px-6 py-3 rounded-lg hover:bg-opacity-90 transition-colors"
                    >
                        <span class="material-icons mr-2 text-sm">qr_code_scanner</span>
                        Scan Produk
                    </a>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="material-icons text-cyan-500 mr-2">receipt_long</span>
                            Riwayat Transaksi
                        </h2>
                        <span class="text-sm text-gray-600">{{ $transactions->count() }} transaksi</span>
                    </div>
            
                    <!-- Filter Buttons -->
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-sm text-gray-600">Filter:</span>
                        <form method="GET" action="{{ route('kasir.transaksi.index') }}" class="flex gap-2">
                            <input type="hidden" name="q" value="{{ request('q') }}"> <!-- Pertahankan pencarian -->
                            <button type="submit" name="filter" value="kasir" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter', 'kasir') === 'kasir' ? 'bg-cyan-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Transaksi Kasir
                            </button>
                            <button type="submit" name="filter" value="all" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter', 'kasir') === 'all' ? 'bg-cyan-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Semua Transaksi
                            </button>
                        </form>
                    </div>
                </div>

                @if($transactions->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($transactions as $trans)
                            <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                                <!-- Transaction Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-icons text-cyan-500 text-sm">receipt</span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Kode Transaksi</p>
                                            <p class="font-bold text-gray-900">{{ $trans->unique_code }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $trans->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $statusConfig = [
                                                'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'check_circle'],
                                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'schedule'],
                                                'batal' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel'],
                                            ];
                                            $status = $statusConfig[$trans->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'info'];
                                        @endphp
                                        <span class="px-3 py-1 {{ $status['bg'] }} {{ $status['text'] }} text-xs font-semibold rounded-full flex items-center">
                                            <span class="material-icons text-xs mr-1">{{ $status['icon'] }}</span>
                                            {{ ucfirst($trans->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Payment Summary -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                                            <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($trans->total_price, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Metode Pembayaran</p>
                                            <p class="text-sm font-semibold text-gray-900 flex items-center">
                                                <span class="material-icons text-sm mr-1">
                                                    {{ ($trans->payment_method ?? 'cash') === 'cash' ? 'payments' : 'account_balance_wallet' }}
                                                </span>
                                                {{ ucfirst($trans->payment_method ?? 'Cash') }}
                                            </p>
                                        </div>
                                        @if(($trans->payment_method ?? 'cash') === 'cash' && !empty($trans->cash_paid))
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Kembalian</p>
                                                <p class="text-sm font-semibold text-green-600">
                                                    Rp {{ number_format(max(0, $trans->cash_paid - $trans->total_price), 0, ',', '.') }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Dibayar: Rp {{ number_format($trans->cash_paid, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Products Details -->
                                @php
                                    $items = json_decode($trans->items, true);
                                @endphp
                                @if($items)
                                    <div class="space-y-2 mb-4">
                                        <p class="text-sm font-medium text-gray-700 flex items-center mb-2">
                                            <span class="material-icons text-sm mr-1">inventory_2</span>
                                            Produk ({{ count($items) }} item)
                                        </p>
                                        @foreach($items as $item)
                                            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                                <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                                    <img src="{{ !empty($item['image']) ? asset('storage/' . ltrim($item['image'], '/')) : asset('images/no-image.png') }}"
                                                         alt="{{ $item['name'] }}" 
                                                         class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-gray-900 text-sm">
                                                        {{ $item['name'] }}
                                                        @if(!empty($item['is_tebus_murah']))
                                                            <span class="inline-block bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded ml-1">Tebus Murah</span>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                    </p>
                                                    @if(isset($item['discount']) && $item['discount'] > 0)
                                                        <p class="text-xs text-green-600 mt-1 flex items-center">
                                                            <span class="material-icons text-xs mr-1">discount</span>
                                                            Diskon: - Rp {{ number_format($item['discount'], 0, ',', '.') }}
                                                        </p>
                                                    @elseif(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                        <p class="text-xs text-green-600 mt-1 flex items-center">
                                                            <span class="material-icons text-xs mr-1">discount</span>
                                                            Diskon: - Rp {{ number_format($item['original_price'] - $item['price'], 0, ',', '.') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-900 text-sm">
                                                        Rp {{ number_format(isset($item['total']) ? $item['total'] : ($item['price'] * $item['quantity']), 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex flex-wrap gap-2">
                                    <a 
                                        href="{{ route('kasir.transaksi.print', $trans->id) }}" 
                                        target="_blank"
                                        class="inline-flex items-center bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm"
                                    >
                                        <span class="material-icons text-sm mr-1">print</span>
                                        Cetak Struk
                                    </a>
                                    <button 
                                        onclick="viewTransactionDetail({{ $trans->id }})"
                                        class="inline-flex items-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-lg transition-colors text-sm"
                                    >
                                        <span class="material-icons text-sm mr-1">visibility</span>
                                        Detail
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 sm:p-6 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <span class="material-icons text-gray-300 text-6xl">receipt_long</span>
                        <p class="text-gray-500 mt-4 text-lg">Belum ada transaksi</p>
                        <p class="text-gray-400 text-sm mt-2">Transaksi akan muncul di sini</p>
                        <a href="{{ route('kasir.scan.page') }}" class="inline-flex items-center mt-6 bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                            <span class="material-icons text-sm mr-2">add_shopping_cart</span>
                            Mulai Transaksi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function scanCode() {
            const code = document.getElementById('scan-input').value.trim();
            if (!code) {
                showNotification('Masukkan kode transaksi', 'error');
                return;
            }

            // Show loading
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="material-icons animate-spin mr-2">refresh</span>Memproses...';

            fetch('/kasir/transaksi/scan', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ code })
            })
            .then(res => res.json())
            .then(data => {
                showNotification(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    document.getElementById('scan-input').value = '';
                    setTimeout(() => location.reload(), 1500);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                showNotification('Error: ' + err.message, 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        function viewTransactionDetail(id) {
            // Implement detail view modal or redirect
            showNotification('Fitur detail akan segera hadir', 'info');
        }

        function showNotification(message, type = 'success') {
            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-gray-500';

            const icon = {
                'success': 'check_circle',
                'error': 'error',
                'info': 'info'
            }[type] || 'info';
            
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-slide-up`;
            notification.innerHTML = `
                <span class="material-icons text-sm">${icon}</span>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(100%)';
                notification.style.transition = 'all 0.3s';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
@endsection
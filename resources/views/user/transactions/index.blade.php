@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6 max-w-full overflow-hidden">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[80px] sm:max-w-[120px]">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[100px] sm:max-w-[160px]">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900 truncate max-w-[120px] sm:max-w-[200px]">Riwayat Transaksi</span>
            </nav>
            <!-- Header -->
            <div class="mb-4 sm:mb-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-2xl sm:text-3xl lg:text-4xl">history</span>
                    Riwayat
                </h1>
                <p class="text-gray-600 mt-1 text-xs sm:text-sm lg:text-base">Lihat riwayat transaksi dan top up Anda</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 sm:mb-6 overflow-hidden">
                <nav class="flex">
                    <button id="tab-transactions" onclick="showTab('transactions')"
                        class="tab-button flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm lg:text-base font-semibold text-white bg-cyan-500 border-b-2 border-cyan-500 transition-colors flex items-center justify-center gap-1 sm:gap-2">
                        <span class="material-icons text-base sm:text-lg">receipt_long</span>
                        <span>Transaksi</span>
                    </button>
                    <button id="tab-topup" onclick="showTab('topup')"
                        class="tab-button flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm lg:text-base font-semibold text-gray-600 hover:bg-gray-50 border-b-2 border-transparent transition-colors flex items-center justify-center gap-1 sm:gap-2">
                        <span class="material-icons text-base sm:text-lg">account_balance_wallet</span>
                        <span>Top Up</span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Riwayat Transaksi -->
            <div id="content-transactions" class="tab-content">
                @if($transactions->count() > 0)
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($transactions as $transaction)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Transaction Header -->
                                <div class="p-3 sm:p-4 lg:p-6 border-b border-gray-200">
                                    <div class="flex flex-col gap-3">
                                        <!-- Top Row: Icon + Code + Status -->
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-start gap-2 sm:gap-3 flex-1 min-w-0">
                                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <span class="material-icons text-cyan-500 text-lg sm:text-xl">shopping_bag</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs sm:text-sm text-gray-500">Kode Transaksi</p>
                                                    <p class="font-bold text-sm sm:text-base text-gray-900 truncate">{{ $transaction->unique_code }}</p>
                                                </div>
                                            </div>
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'schedule'],
                                                    'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'check_circle'],
                                                    'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel'],
                                                ];
                                                $status = $statusConfig[$transaction->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'info'];
                                            @endphp
                                                <div class="flex items-center gap-2 flex-shrink-0">
        <span class="px-2 sm:px-3 py-1 {{ $status['bg'] }} {{ $status['text'] }} text-xs font-semibold rounded-full flex items-center whitespace-nowrap">
            <span class="material-icons text-xs mr-1">{{ $status['icon'] }}</span>
            <span class="hidden sm:inline">{{ ucfirst($transaction->status) }}</span>
        </span>
        <a href="javascript:void(0)" onclick="event.preventDefault(); showQRModal('{{ $transaction->unique_code }}')"
    class="px-3 py-2 bg-green-500 text-white text-xs sm:text-sm font-semibold rounded-full flex items-center justify-center hover:bg-green-600 transition-colors gap-1">
    <span class="material-icons text-sm">qr_code</span>
    Lihat QRCode
</a>
        <a href="{{ route('user.transactions.print', $transaction->id) }}"
            class="px-3 py-2 bg-cyan-500 text-white text-xs sm:text-sm font-semibold rounded-full flex items-center justify-center hover:bg-cyan-600 transition-colors gap-1">
            <span class="material-icons text-sm">print</span>
            Cetak Struk
        </a>
    </div>
                                        </div>
                                        
                                    </div>
                                </div>

                                <!-- Transaction Details -->
                                <div class="p-3 sm:p-4 lg:p-6">
                                    <!-- Main Info Grid -->
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4">
                                        <div class="col-span-2 sm:col-span-1">
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Total Pembayaran</p>
                                            <p class="text-base sm:text-lg font-bold text-cyan-500">
                                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Metode</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900">
                                                {{ ucfirst($transaction->payment_method ?? 'Dimascash') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Tanggal</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900">
                                                {{ $transaction->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Discount Info Grid -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-3 sm:mb-4">
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Total Harga Produk</p>
                                            <p class="text-sm sm:text-base font-bold text-gray-900">
                                                Rp {{ number_format(collect(json_decode($transaction->items, true))->sum('total'), 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Potongan Voucher</p>
                                            <p class="text-sm sm:text-base font-bold text-blue-600">
                                                @if($transaction->discount_amount > 0 && $transaction->voucher_code)
                                                    - Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}
                                                    <span class="block text-xs text-gray-500">({{ $transaction->voucher_code }})</span>
                                                @else
                                                    -
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Potongan Points</p>
                                            <p class="text-sm sm:text-base font-bold text-blue-600">
                                                @if($transaction->points_used > 0)
                                                    - Rp {{ number_format($transaction->points_used * 10, 0, ',', '.') }}
                                                    <span class="block text-xs text-gray-500">({{ $transaction->points_used }} pts)</span>
                                                @else
                                                    -
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Discount Notes -->
                                    @if($transaction->discount_amount > 0 && $transaction->voucher_code)
                                        <p class="text-xs text-blue-600 mb-2 bg-blue-50 p-2 rounded">
                                            Total pembayaran sudah termasuk potongan voucher <b>{{ $transaction->voucher_code }}</b>
                                            sebesar Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}.
                                        </p>
                                    @endif
                                    @if($transaction->points_used > 0)
                                        <p class="text-xs text-blue-600 mb-2 bg-blue-50 p-2 rounded">
                                            Total pembayaran sudah termasuk potongan points sebesar Rp {{ number_format($transaction->points_used * 10, 0, ',', '.') }} dari {{ $transaction->points_used }} points.
                                        </p>
                                    @endif

                                    <!-- Items Details (Collapsible) -->
                                    <div class="border-t border-gray-200 pt-3 sm:pt-4">
                                        <button onclick="toggleDetails('transaction-{{ $transaction->id }}')"
                                            class="w-full flex items-center justify-between text-left text-xs sm:text-sm font-medium text-cyan-500 hover:text-cyan-600">
                                            <span class="flex items-center gap-1">
                                                <span class="material-icons text-base sm:text-lg">list</span>
                                                Detail Produk
                                            </span>
                                            <span class="material-icons text-base sm:text-lg transition-transform"
                                                id="icon-transaction-{{ $transaction->id }}">expand_more</span>
                                        </button>

                                        <div id="details-transaction-{{ $transaction->id }}" class="hidden mt-3 sm:mt-4 space-y-2 sm:space-y-3">
                                            @foreach(json_decode($transaction->items, true) as $item)
                                                <div class="flex items-start gap-2 sm:gap-3 p-2 sm:p-3 bg-gray-50 rounded-lg">
                                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded border border-gray-200 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                        <img src="{{ !empty($item['image']) ? asset('storage/' . ltrim($item['image'], '/')) : asset('images/no-image.png') }}"
                                                            alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-medium text-gray-900 text-xs sm:text-sm leading-tight">
                                                            {{ $item['name'] }}
                                                            @if(!empty($item['is_tebus_murah']))
                                                                <span class="inline-block bg-yellow-200 text-yellow-800 text-xs px-1.5 py-0.5 rounded ml-1">Tebus Murah</span>
                                                            @endif
                                                            @if(!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_get_y_free' && !empty($item['free_items']))
                                                                <span class="inline-block bg-green-200 text-green-800 text-xs px-1.5 py-0.5 rounded ml-1">Gratis Promo: {{ $item['free_items'] }} pcs</span>
                                                            @endif
                                                            @if(!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_for_y')
                                                                <span class="inline-block bg-blue-200 text-blue-800 text-xs px-1.5 py-0.5 rounded ml-1">Promo: {{ $item['promo_desc'] ?? '' }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </p>
                                                        @if(isset($item['discount']) && $item['discount'] > 0)
                                                            <p class="text-xs text-green-600 mt-1">
                                                                Diskon: - Rp {{ number_format($item['discount'], 0, ',', '.') }}
                                                            </p>
                                                        @elseif(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                            <p class="text-xs text-green-600 mt-1">
                                                                Diskon: - Rp {{ number_format($item['original_price'] - $item['price'], 0, ',', '.') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right flex-shrink-0">
                                                        <p class="font-semibold text-gray-900 text-xs sm:text-sm">
                                                            Rp {{ number_format(isset($item['total']) ? $item['total'] : ($item['price'] * $item['quantity']), 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                {{-- Tampilkan sub-produk gratis jika ada --}}
                                                @if(!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_get_y_free' && !empty($item['free_items']))
                                                    <div class="ml-12 p-2 bg-green-50 border-l-4 border-green-400 rounded flex items-center gap-2">
                                                        <span class="material-icons text-green-400 text-base">card_giftcard</span>
                                                        <span class="text-xs text-green-700">Gratis {{ $item['free_items'] }} pcs</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 sm:mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                        <span class="material-icons text-gray-300 text-5xl sm:text-6xl">receipt_long</span>
                        <p class="text-gray-500 mt-4 text-base sm:text-lg">Belum ada riwayat transaksi</p>
                        <p class="text-gray-400 text-xs sm:text-sm mt-2">Transaksi Anda akan muncul di sini</p>
                        <a href="{{ route('user.products') }}"
                            class="inline-flex items-center mt-4 sm:mt-6 bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors text-sm sm:text-base gap-1 sm:gap-2">
                            <span class="material-icons text-sm sm:text-base">shopping_cart</span>
                            Mulai Belanja
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tab Content: Riwayat Top Up -->
            <div id="content-topup" class="tab-content hidden">
                @if($topupRequests->count() > 0)
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($topupRequests as $req)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Top Up Header -->
                                <div class="p-3 sm:p-4 lg:p-6 border-b border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex items-start gap-2 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="material-icons text-cyan-500 text-lg sm:text-xl">account_balance_wallet</span>
                                            </div>
                                            <div>
                                                <p class="text-xs sm:text-sm text-gray-500">Jumlah Top Up</p>
                                                <p class="text-lg sm:text-xl font-bold text-gray-900">
                                                    Rp {{ number_format($req->amount, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'schedule'],
                                                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'check_circle'],
                                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel'],
                                                ];
                                                $status = $statusConfig[$req->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'info'];
                                            @endphp
                                            <span class="px-2 sm:px-3 py-1 {{ $status['bg'] }} {{ $status['text'] }} text-xs font-semibold rounded-full flex items-center">
                                                <span class="material-icons text-xs mr-1">{{ $status['icon'] }}</span>
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Up Details -->
                                <div class="p-3 sm:p-4 lg:p-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                                        <div>
                                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Tanggal Pengajuan</p>
                                            <p class="text-xs sm:text-sm font-medium text-gray-900">
                                                {{ $req->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        @if($req->updated_at != $req->created_at)
                                            <div>
                                                <p class="text-xs sm:text-sm text-gray-500 mb-1">Tanggal Diproses</p>
                                                <p class="text-xs sm:text-sm font-medium text-gray-900">
                                                    {{ $req->updated_at->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Proof Image -->
                                    @if($req->proof_image)
                                        <div class="mb-3 sm:mb-4">
                                            <p class="text-xs sm:text-sm font-medium text-gray-700 mb-2 flex items-center gap-1">
                                                <span class="material-icons text-sm">image</span>
                                                Bukti Transfer
                                            </p>
                                            <div class="inline-block border-2 border-gray-200 rounded-lg overflow-hidden">
                                                <img src="{{ asset('storage/' . $req->proof_image) }}" alt="Bukti Transfer"
                                                    class="w-32 sm:w-48 h-auto cursor-pointer hover:opacity-90 transition-opacity"
                                                    onclick="openImageModal('{{ asset('storage/' . $req->proof_image) }}')">
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Admin Note -->
                                    @if($req->admin_note)
                                        <div class="p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-xs sm:text-sm font-medium text-blue-900 mb-1 flex items-center gap-1">
                                                <span class="material-icons text-sm">info</span>
                                                Catatan Admin
                                            </p>
                                            <p class="text-xs sm:text-sm text-blue-700">{{ $req->admin_note }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 sm:mt-6">
                        {{ $topupRequests->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                        <span class="material-icons text-gray-300 text-5xl sm:text-6xl">account_balance_wallet</span>
                        <p class="text-gray-500 mt-4 text-base sm:text-lg">Belum ada riwayat top up</p>
                        <p class="text-gray-400 text-xs sm:text-sm mt-2">Riwayat top up Anda akan muncul di sini</p>
                        <a href="{{ route('user.topup') }}"
                            class="inline-flex items-center mt-4 sm:mt-6 bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors text-sm sm:text-base gap-1 sm:gap-2">
                            <span class="material-icons text-sm sm:text-base">add</span>
                            Top Up Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="image-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-[90vh]">
            <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                <span class="material-icons">close</span>
            </button>
            <img id="modal-image" src="" alt="Preview" class="max-w-full max-h-[85vh] rounded-lg">
        </div>
    </div>
    <!-- QR Code Modal -->
<div id="qr-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code Transaksi</h3>
        <div id="qr-code-container" class="flex justify-center mb-4"></div>
        <p class="text-sm text-gray-600 mb-4">Kode: <span id="qr-code-text" class="font-mono"></span></p>
        <button onclick="closeQRModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Tutup</button>
    </div>
</div>
    <!-- QRCode Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Tab switching
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            document.getElementById('content-' + tab).classList.remove('hidden');
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('bg-cyan-500', 'text-white', 'border-cyan-8996006853400
                500');
                btn.classList.add('text-gray-600', 'border-transparent');
            });
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.remove('text-gray-600', 'border-transparent');
            activeBtn.classList.add('bg-cyan-500', 'text-white', 'border-cyan-500');
        }
    
        // Toggle details
        function toggleDetails(id) {
            const details = document.getElementById('details-' + id);
            const icon = document.getElementById('icon-' + id);
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                details.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    
        // Image modal
        function openImageModal(src) {
            event.stopPropagation();
            document.getElementById('modal-image').src = src;
            document.getElementById('image-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    
        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    
        // QR Code modal
        function showQRModal(code) {
            // Tunggu sampai QRCode sudah termuat
            if (typeof QRCode === 'undefined') {
                alert('Library QRCode belum termuat. Coba refresh halaman.');
                return;
            }
            if (!code || code.trim() === '') {
                alert('Kode transaksi tidak ditemukan.');
                return;
            }
            document.getElementById('qr-code-text').textContent = code;
            const container = document.getElementById('qr-code-container');
            container.innerHTML = '';
            new QRCode(container, {
                text: code,
                width: 200,
                height: 200,
            });
            document.getElementById('qr-modal').classList.remove('hidden');
        }
    
        function closeQRModal() {
            document.getElementById('qr-modal').classList.add('hidden');
        }
    
        // Close modal on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageModal();
                closeQRModal();
            }
        });
    </script>

    <style>
        .tab-button {
            position: relative;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: currentColor;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tab-button.bg-cyan-500::after {
            opacity: 1;
        }

        #image-modal img {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection
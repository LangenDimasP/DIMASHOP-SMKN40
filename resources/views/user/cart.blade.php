@extends('layouts.app')

@section('content')
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900">Keranjang Belanja</span>
            </nav>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">shopping_cart</span>
                    Keranjang Belanja
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola produk yang ingin Anda beli</p>
            </div>

            <!-- Belanja Pintar Section -->
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg shadow-sm border border-cyan-200 p-6 mb-6">
                <h2 class="text-xl font-bold text-white flex items-center mb-4">
                    <span class="material-icons mr-2">smart_toy</span>
                    Belanja Pintar (AI Assistant) <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full ml-2">Beta</span>
                </h2>
                <p class="text-cyan-100 mb-4">Ketik atau katakan apa yang Anda butuhkan, contoh: "Saya mau produk teh dong."</p>
                <div class="flex gap-2">
                    <input type="text" id="shopping-command" placeholder="Masukkan perintah belanja..."
                        class="flex-1 border border-cyan-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                    <button id="shopping-btn" onclick="processShoppingCommand()"
                        class="bg-white text-cyan-600 hover:bg-cyan-50 font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                        <span class="material-icons mr-2" id="shopping-icon">send</span>
                        <span id="shopping-text">Kirim</span>
                    </button>
                </div>
                <div id="shopping-feedback" class="mt-4 text-cyan-100 text-sm hidden"></div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900">Produk</h2>
                                <span id="item-count" class="text-sm text-gray-600">0 item</span>
                                <button onclick="showTebusMurahModal()"
                                    class="ml-4 px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded font-semibold text-sm flex items-center">
                                    <span class="material-icons text-sm mr-1">local_offer</span> Tebus Murah
                                </button>
                            </div>
                        </div>
                        <div id="cart-items" class="divide-y divide-gray-200">
                            <!-- Items akan di-load via JS -->
                        </div>
                        <div id="empty-cart" class="hidden p-12 text-center">
                            <span class="material-icons text-gray-300 text-6xl">shopping_cart</span>
                            <p class="text-gray-500 mt-4">Keranjang belanja Anda kosong</p>
                            <a href="/products" class="inline-block mt-4 text-cyan-500 hover:text-cyan-600 font-medium">
                                Mulai Belanja
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Ringkasan Belanja</h2>
                        </div>

                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Voucher Section -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">Kode Voucher</span>
                                    <button onclick="showAvailableVouchers()"
                                        class="text-cyan-500 hover:text-cyan-600 text-sm font-medium flex items-center">
                                        <span class="material-icons text-sm mr-1">confirmation_number</span>
                                        Lihat Voucher
                                    </button>
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" id="voucher-code" placeholder="Masukkan kode voucher"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                    <button onclick="applyVoucher()"
                                        class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">
                                        Pakai
                                    </button>
                                </div>
                                <p id="discount-info" class="text-green-600 text-sm mt-2 hidden flex items-center">
                                    <span class="material-icons text-sm mr-1">check_circle</span>
                                    <span id="discount-text"></span>
                                </p>
                            </div>

                            <!-- Points Section -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">Gunakan Points</span>
                                    <span class="text-sm text-gray-600">Saldo: {{ auth()->user()->points }} points</span>
                                </div>
                                <div class="flex gap-2">
                                    <input type="number" id="points-used" placeholder="Jumlah points" min="0"
                                        max="{{ auth()->user()->points }}"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                    <button onclick="applyPoints()"
                                        class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">
                                        Pakai
                                    </button>
                                </div>
                                <p id="points-info" class="text-green-600 text-sm mt-2 hidden flex items-center">
                                    <span class="material-icons text-sm mr-1">check_circle</span>
                                    <span id="points-text"></span>
                                </p>
                            </div>

                            <!-- Price Details -->
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Harga</span>
                                    <span id="total-price" class="font-medium text-gray-900">Rp 0</span>
                                </div>
                                <div id="discount-row" class="flex justify-between text-sm hidden">
                                    <span class="text-gray-600">Diskon Voucher</span>
                                    <span id="discount-amount" class="font-medium text-green-600">- Rp 0</span>
                                </div>
                                <div id="points-row" class="flex justify-between text-sm hidden">
                                    <span class="text-gray-600">Potongan Points</span>
                                    <span id="points-amount" class="font-medium text-green-600">- Rp 0</span>
                                </div>

                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-base font-semibold text-gray-900">Total Akhir</span>
                                        <span id="final-total" class="text-lg font-bold text-cyan-500">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Points yang akan didapat</span>
                                        <span id="points-earned" class="font-medium text-cyan-500">0 points</span>
                                    </div>
                                </div>
                                <!-- Tambahkan elemen ini di sini -->
                                <div id="payment-method-display" class="hidden">
                                    <!-- Akan diisi oleh JS -->
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <button id="payment-btn" onclick="showPaymentModal()"
                                class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center">
                                <span class="material-icons mr-2">payment</span>
                                Lanjut Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Metode Pembayaran -->
    <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Pilih Metode Pembayaran</h2>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-3">
                <button onclick="selectPayment('dimascash')" id="dimascash-btn"
                    class="w-full bg-white border-2 border-gray-200 hover:border-cyan-500 rounded-lg p-4 text-left transition-all flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-icons text-cyan-500">account_balance_wallet</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Dimascash</p>
                            <p class="text-sm text-gray-600">Saldo: {{ auth()->user()->dimascash_balance_formatted }}</p>
                        </div>
                    </div>
                    <span class="material-icons text-gray-400">chevron_right</span>
                </button>

                <div class="space-y-2">
                    <button disabled
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-4 text-left flex items-center justify-between opacity-50 cursor-not-allowed">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="material-icons text-gray-400">payment</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">ShopeePay</p>
                                <p class="text-xs text-gray-500">Segera hadir</p>
                            </div>
                        </div>
                    </button>

                    <button disabled
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-4 text-left flex items-center justify-between opacity-50 cursor-not-allowed">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="material-icons text-gray-400">payment</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">Gopay</p>
                                <p class="text-xs text-gray-500">Segera hadir</p>
                            </div>
                        </div>
                    </button>

                    <button disabled
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-4 text-left flex items-center justify-between opacity-50 cursor-not-allowed">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="material-icons text-gray-400">payment</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">DANA</p>
                                <p class="text-xs text-gray-500">Segera hadir</p>
                            </div>
                        </div>
                    </button>

                    <button disabled
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-4 text-left flex items-center justify-between opacity-50 cursor-not-allowed">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="material-icons text-gray-400">payment</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700">OVO</p>
                                <p class="text-xs text-gray-500">Segera hadir</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 space-y-2">
                <button id="confirm-btn" onclick="confirmPayment()" disabled
                    class="w-full bg-cyan-500 hover:bg-cyan-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition-colors">
                    Konfirmasi Pembayaran
                </button>
                <button onclick="closePaymentModal()"
                    class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>


    <!-- Modal Tebus Murah -->
    <div id="tebus-murah-modal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Tebus Murah</h2>
                <button onclick="closeTebusMurahModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="p-6" id="tebus-murah-list">
                <!-- List produk tebus murah akan diisi JS -->
            </div>
        </div>
    </div>

    <!-- Modal Voucher Tersedia -->
    <div id="voucher-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="material-icons text-cyan-500 mr-2">confirmation_number</span>
                        Voucher Tersedia
                    </h2>
                    <button onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <div id="voucher-list" class="p-6 overflow-y-auto flex-1">
                <!-- Vouchers akan di-load via JS -->
            </div>
        </div>
    </div>

        <!-- Modal Konfirmasi Hapus -->
    <div id="delete-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-sm">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <span class="material-icons text-red-500">delete</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hapus Produk</h3>
                        <p class="text-sm text-gray-600">Apakah Anda yakin ingin menghapus produk ini dari keranjang?</p>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button id="cancel-delete-btn" onclick="closeDeleteConfirmModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="confirm-delete-btn" onclick="confirmDelete()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
       <!-- Modal Checkout Sukses -->
    <div id="checkout-success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-sm">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <span class="material-icons text-green-500">check_circle</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Checkout Berhasil</h3>
                        <p id="checkout-success-message" class="text-sm text-gray-600">Pesan sukses akan ditampilkan di sini.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6">
                <button onclick="redirectToTransactions()" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 rounded-lg transition-colors">
                    Lihat Transaksi
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let products = @json($products ?? []);
        let tebusMurahList = @json($tebusMurahList ?? []); // <-- Pindahkan ke sini!
        let selectedMethod = null;
        let appliedVoucher = null;
        let discount = 0;
        let pointsUsed = 0;
        let pointsDiscount = 0;
        let multiplier = @json($multiplier ?? 1);

        function loadCart() {
            const cartItemsEl = document.getElementById('cart-items');
            const emptyCartEl = document.getElementById('empty-cart');
            const itemCountEl = document.getElementById('item-count');

            let html = '';
            let total = 0;
            let itemCount = 0;

            if (cart.length === 0) {
                cartItemsEl.classList.add('hidden');
                emptyCartEl.classList.remove('hidden');
                itemCountEl.textContent = '0 item';
            } else {
                cartItemsEl.classList.remove('hidden');
                emptyCartEl.classList.add('hidden');

                cart.forEach(item => {
                    let product = products.find(p => p.id == item.product_id);
                    if (product) {
                        let isTebusMurah = item.is_tebus_murah;
                        let basePrice = product.final_price || product.selling_price;
                        let price = isTebusMurah ? item.tebus_price : basePrice;
                        let itemTotal = calculatePromoPrice(price, item.quantity, product);

                        // Buy X Get Y Free logic (otomatis sub-produk gratis)
                        if (!isTebusMurah && product.promo_active && product.promo_type == 'buy_x_get_y_free') {
                            let x = product.promo_buy;
                            let y = product.promo_get;
                            let fullSets = Math.floor(item.quantity / x);
                            let paidItems = item.quantity;
                            let freeItems = fullSets * y;

                            itemTotal = paidItems * price;

                            // Tampilkan sub-produk gratis di cart
                            if (freeItems > 0) {
                                html += `
                        <div class="p-3 pl-3 bg-green-50 border-l-4 border-green-400 rounded mb-2 flex items-center gap-3">
                            <img src="${product.image ? '/storage/' + product.image : '/images/no-image.png'}" alt="${product.name}" class="w-10 h-10 rounded object-cover border border-green-200 mr-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-green-700 truncate">${product.name} <span class="ml-1 px-2 py-0.5 bg-green-200 text-green-800 rounded text-[10px]">Gratis Promo</span></div>
                                <div class="text-xs text-green-700">Gratis ${freeItems} pcs</div>
                            </div>
                        </div>
                    `;
                            }
                        }

                        total += itemTotal;
                        itemCount += item.quantity;


                        if (isTebusMurah) {
                            // Cari maxQty dari tebusMurahList
                            let tm = tebusMurahList.find(tm => tm.product_id == item.product_id);
                            let maxQty = tm ? tm.max_qty : 1;
                            // TAMPILAN KECIL/SUBPRODUK DENGAN GAMBAR
                            html += `
                                                                                        <div class="p-3 pl-3 bg-yellow-50 border-l-4 border-yellow-400 rounded mb-2 flex items-center gap-3">
                                                                                            <img src="${product.image ? '/storage/' + product.image : '/images/no-image.png'}" alt="${product.name}" class="w-10 h-10 rounded object-cover border border-yellow-200 mr-2">
                                                                                            <div class="flex-1 min-w-0">
                                                                                                <div class="text-xs font-semibold text-yellow-700 truncate">${product.name} <span class="ml-1 px-2 py-0.5 bg-yellow-200 text-yellow-800 rounded text-[10px]">Tebus Murah</span></div>
                                                                                                <div class="text-xs text-yellow-700">Rp ${price.toLocaleString()} x 
                                                                                                    <input type="number" min="1" max="${maxQty}" value="${item.quantity}" style="width:40px" 
                                                                                                        onchange="setTebusMurahQty(${item.product_id}, this.value, ${maxQty})" 
                                                                                                        class="border rounded px-1 text-xs text-center w-12">
                                                                                                    / Maks ${maxQty}
                                                                                                </div>
                                                                                            </div>
                                                                                            <button onclick="removeItem(${item.product_id}, true)" class="text-yellow-600 hover:text-red-500 p-1" title="Hapus">
                                                                                                <span class="material-icons text-xs">close</span>
                                                                                            </button>
                                                                                        </div>
                                                                                    `;
                        } else {
                            // TAMPILAN PRODUK UTAMA (seperti biasa)
                            html += `
                                                                                                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                                                                                                    <div class="flex gap-4">
                                                                                                        <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                                                                            <img src="${product.image ? '/storage/' + product.image : '/images/no-image.png'}" 
                                                                                                                 alt="${product.name}" 
                                                                                                                 class="w-full h-full object-cover">
                                                                                                        </div>
                                                                                                        <div class="flex-1 min-w-0">
                                                                                                            <h3 class="font-semibold text-gray-900 mb-1 truncate">${product.name}</h3>
                                                                                                            <p class="text-cyan-500 font-semibold mb-3">Rp ${price.toLocaleString()}</p>
                                                                                                            ${product.promo_active && product.promo_type ? `<p class="text-green-600 text-sm">${getPromoDesc(product)}</p>` : ''}
                                                                                                            <div class="flex items-center justify-between">
                                                                                                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                                                                                    <button 
                                                                                                                        onclick="updateQuantity(${item.product_id}, -1)" 
                                                                                                                        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 text-gray-600"
                                                                                                                    >
                                                                                                                        <span class="material-icons text-sm">remove</span>
                                                                                                                    </button>
                                                                                                                    <input 
                                                                                                                        type="number" 
                                                                                                                        value="${item.quantity}" 
                                                                                                                        onchange="setQuantity(${item.product_id}, this.value)" 
                                                                                                                        class="w-12 h-8 text-center border-x border-gray-300 text-sm focus:outline-none"
                                                                                                                        min="1"
                                                                                                                        max="${product.stock}"
                                                                                                                    >
                                                                                                                    <button 
                                                                                                                        onclick="updateQuantity(${item.product_id}, 1)" 
                                                                                                                        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 text-gray-600 ${item.quantity >= product.stock ? 'opacity-50 cursor-not-allowed' : ''}"
                                                                                                                        ${item.quantity >= product.stock ? 'disabled' : ''}
                                                                                                                    >
                                                                                                                        <span class="material-icons text-sm">add</span>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                                <span id="stock-warning-${item.product_id}" class="text-red-500 text-xs ml-2 hidden">Stok hanya ${product.stock} pcs</span>
                                                                                                                <button 
                                                                                                                    onclick="removeItem(${item.product_id})" 
                                                                                                                    class="text-red-500 hover:text-red-600 p-2"
                                                                                                                    title="Hapus"
                                                                                                                >
                                                                                                                    <span class="material-icons text-sm">delete</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                                                                                        <div class="flex justify-between items-center">
                                                                                                            <span class="text-sm text-gray-600">Subtotal</span>
                                                                                                            <span class="font-semibold text-gray-900">Rp ${itemTotal.toLocaleString()}</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `;
                        }
                    }
                });

                itemCountEl.textContent = `${itemCount} item${itemCount > 1 ? 's' : ''}`;
            }

            cartItemsEl.innerHTML = html;
            document.getElementById('total-price').textContent = `Rp ${total.toLocaleString()}`;
            updateFinalTotal(total);

            // Re-apply voucher jika ada, untuk update discount berdasarkan total baru
            if (appliedVoucher) {
                let currentTotal = cart.reduce((sum, item) => {
                    let product = products.find(p => p.id == item.product_id);
                    return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
                }, 0);

                fetch('/user/apply-voucher', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ code: appliedVoucher, total: currentTotal })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            discount = data.discount;
                            document.getElementById('discount-text').textContent = data.message;
                            document.getElementById('discount-info').classList.remove('hidden');
                            updateFinalTotal(currentTotal);
                        } else {
                            appliedVoucher = null;
                            discount = 0;
                            document.getElementById('discount-info').classList.add('hidden');
                            updateFinalTotal(currentTotal);
                        }
                    })
                    .catch(err => {
                        console.error('Error re-applying voucher:', err);
                        appliedVoucher = null;
                        discount = 0;
                        document.getElementById('discount-info').classList.add('hidden');
                        updateFinalTotal(currentTotal);
                    });
            }

            const paymentBtn = document.getElementById('payment-btn');
            paymentBtn.style.display = cart.length === 0 ? 'none' : 'flex';

            // Reset UI saat load cart (tambahkan di sini)
            updatePaymentMethodDisplay(null);
            updatePointsDisplay(null);
            updatePaymentButton(false);

            if (window.updateCartBadge) {
                window.updateCartBadge(cart.reduce((sum, item) => sum + item.quantity, 0));
            }
        }
        function calculatePromoPrice(price, quantity, product) {
            if (!product.promo_active || !product.promo_type) {
                return price * quantity;
            }

            if (product.promo_type == 'buy_x_get_y_free') {
                let x = product.promo_buy;
                let y = product.promo_get;
                let setSize = x + y;
                let fullSets = Math.floor(quantity / setSize);
                let remaining = quantity % setSize;
                let paidItems = fullSets * x + Math.min(remaining, x);
                return paidItems * price;
            } else if (product.promo_type == 'buy_x_for_y') {
                let x = product.promo_buy;
                let y = product.promo_get;
                let fullSets = Math.floor(quantity / x);
                let remaining = quantity % x;
                // Perbaikan: fullSets * y (harga promo untuk kelipatan) + remaining * price (harga normal untuk sisa)
                return fullSets * y + remaining * price;
            }
            return price * quantity;
        }

        function getPromoDesc(product) {
            if (product.promo_type == 'buy_x_get_y_free') {
                return `Beli ${product.promo_buy} Gratis ${product.promo_get}`;
            } else if (product.promo_type == 'buy_x_for_y') {
                return `Beli ${product.promo_buy} Hanya Rp ${product.promo_get.toLocaleString()}`;
            }
            return '';
        }

        function updateQuantity(id, change) {
            let item = cart.find(i => i.product_id == id);
            if (!item) return;

            let product = products.find(p => p.id == id);
            if (!product) return;

            let newQuantity = item.quantity + change;
            let warningEl = document.getElementById(`stock-warning-${id}`);

            if (change > 0 && newQuantity > product.stock) {
                warningEl.textContent = `Stok hanya ${product.stock} pcs`;
                warningEl.classList.remove('hidden');
                setTimeout(() => warningEl.classList.add('hidden'), 3000); // Sembunyikan setelah 3 detik
                return;
            }

            if (newQuantity <= 0) {
                removeItem(id);
                return;
            }

            item.quantity = newQuantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function setQuantity(id, value) {
            let item = cart.find(i => i.product_id == id);
            if (!item) return;

            let product = products.find(p => p.id == id);
            if (!product) return;

            let newQuantity = parseInt(value);
            let warningEl = document.getElementById(`stock-warning-${id}`);

            if (isNaN(newQuantity) || newQuantity <= 0) {
                removeItem(id);
                return;
            }

            if (newQuantity > product.stock) {
                newQuantity = product.stock;
                warningEl.textContent = `Stok hanya ${product.stock} pcs`;
                warningEl.classList.remove('hidden');
                setTimeout(() => warningEl.classList.add('hidden'), 3000);
            }

            item.quantity = newQuantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

                let itemToDelete = null;
                let isTebusMurahDelete = false;
                
                function removeItem(id, isTebusMurah = false) {
                    itemToDelete = id;
                    isTebusMurahDelete = isTebusMurah;
                    document.getElementById('delete-confirm-modal').classList.remove('hidden');
                }
        
                function closeDeleteConfirmModal() {
                    document.getElementById('delete-confirm-modal').classList.add('hidden');
                    itemToDelete = null;
                    isTebusMurahDelete = false;
                }
        
                function confirmDelete() {
                    if (itemToDelete !== null) {
                        cart = cart.filter(item => {
                            if (isTebusMurahDelete) return !(item.product_id == itemToDelete && item.is_tebus_murah);
                            return !(item.product_id == itemToDelete && !item.is_tebus_murah);
                        });
                        localStorage.setItem('cart', JSON.stringify(cart));
                        loadCart();
                    }
                    closeDeleteConfirmModal();
                }

        function updateFinalTotal(total) {
            let finalTotal = total - discount - pointsDiscount;
            document.getElementById('final-total').textContent = `Rp ${finalTotal.toLocaleString()}`;

            // Hitung points: 1 point per 1000 rupiah dari total sebelum diskon
            let points = Math.floor(total / 1000) * multiplier;
            document.getElementById('points-earned').textContent = `${points} points`;

            const discountRow = document.getElementById('discount-row');
            const discountAmount = document.getElementById('discount-amount');

            if (discount > 0) {
                discountRow.classList.remove('hidden');
                discountAmount.textContent = `- Rp ${discount.toLocaleString()}`;
            } else {
                discountRow.classList.add('hidden');
            }

            // Tambahkan row untuk points discount
            const pointsRow = document.getElementById('points-row');
            const pointsAmount = document.getElementById('points-amount');

            if (pointsDiscount > 0) {
                pointsRow.classList.remove('hidden');
                pointsAmount.textContent = `- Rp ${pointsDiscount.toLocaleString()}`;
            } else {
                pointsRow.classList.add('hidden');
            }
        }

        function createPointsRow() {
            const discountRow = document.getElementById('discount-row');
            const pointsRow = document.createElement('div');
            pointsRow.id = 'points-row';
            pointsRow.className = 'flex justify-between text-sm hidden';
            pointsRow.innerHTML = `
                                                <span class="text-gray-600">Potongan Points</span>
                                                <span id="points-amount" class="font-medium text-green-600">- Rp 0</span>
                                            `;
            discountRow.insertAdjacentElement('afterend', pointsRow);
            return pointsRow;
        }
        function updatePointsDisplay(points) {
            if (points === null) {
                pointsUsed = 0;
                pointsDiscount = 0;
                document.getElementById('points-used').value = '';
                document.getElementById('points-info').classList.add('hidden');
            }
        }


        function applyVoucher() {
            let code = document.getElementById('voucher-code').value.trim();
            if (!code) {
                alert('Masukkan kode voucher terlebih dahulu');
                return;
            }

            let total = cart.reduce((sum, item) => {
                let product = products.find(p => p.id == item.product_id);
                return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
            }, 0);

            fetch('/user/apply-voucher', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ code: code, total: total })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        appliedVoucher = code;
                        discount = data.discount;
                        document.getElementById('discount-text').textContent = data.message;
                        document.getElementById('discount-info').classList.remove('hidden');
                        updateFinalTotal(total);
                    } else {
                        alert(data.message);
                        appliedVoucher = null;
                        discount = 0;
                        document.getElementById('discount-info').classList.add('hidden');
                        updateFinalTotal(total);
                    }
                })
                .catch(err => alert('Error: ' + err.message));
        }
        function applyPoints() {
            let pointsInput = parseInt(document.getElementById('points-used').value) || 0;
            let userPoints = {{ auth()->user()->points }};  // Ambil dari backend
            let total = cart.reduce((sum, item) => {
                let product = products.find(p => p.id == item.product_id);
                return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
            }, 0);

            if (pointsInput > userPoints) {
                alert('Points tidak cukup. Saldo Anda: ' + userPoints + ' points');
                return;
            }

            pointsUsed = pointsInput;
            pointsDiscount = Math.min(pointsInput * 10, total);  // Maksimal potongan = total harga

            document.getElementById('points-text').textContent = 'Potongan Rp ' + pointsDiscount.toLocaleString() + ' dari ' + pointsUsed + ' points';
            document.getElementById('points-info').classList.remove('hidden');
            updateFinalTotal(total);
        }

        function showPaymentModal() {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            selectedMethod = null;  // Reset pilihan
            document.getElementById('confirm-btn').disabled = true;
            document.querySelectorAll('#payment-modal button[id$="-btn"]').forEach(btn => {
                btn.classList.remove('border-cyan-500', 'bg-cyan-50');
                btn.classList.add('border-gray-200');
            });
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }

        function selectPayment(method) {
            selectedMethod = method;
            document.getElementById('confirm-btn').disabled = false;

            document.querySelectorAll('#payment-modal button[id$="-btn"]').forEach(btn => {
                btn.classList.remove('border-cyan-500', 'bg-cyan-50');
                btn.classList.add('border-gray-200');
            });

            const selectedBtn = document.getElementById(method + '-btn');
            selectedBtn.classList.remove('border-gray-200');
            selectedBtn.classList.add('border-cyan-500', 'bg-cyan-50');
        }

        function confirmPayment() {
            if (!selectedMethod) {
                alert('Pilih metode pembayaran terlebih dahulu');
                return;
            }

            // Tutup modal dan update UI
            closePaymentModal();
            updatePaymentMethodDisplay(selectedMethod);
            updatePaymentButton(true);  // Ubah tombol menjadi "Konfirmasi Pembayaran"
        }

        function updatePaymentMethodDisplay(method) {
            const paymentMethodDisplay = document.getElementById('payment-method-display');
            if (method === 'dimascash') {
                const balance = {{ auth()->user()->dimascash_balance }};  // Ambil saldo dari backend
                paymentMethodDisplay.innerHTML = `
                                                                                <div class="flex justify-between text-sm">
                                                                                    <span class="text-gray-600">Metode Pembayaran</span>
                                                                                    <span class="font-medium text-gray-900">Dimascash (Saldo Rp ${balance.toLocaleString()})</span>
                                                                                </div>
                                                                            `;
                paymentMethodDisplay.classList.remove('hidden');
            } else {
                // Untuk metode lain jika ditambahkan nanti
                paymentMethodDisplay.classList.add('hidden');
            }
        }

        function updatePaymentButton(isConfirmMode) {
            const paymentBtn = document.getElementById('payment-btn');
            if (isConfirmMode) {
                paymentBtn.innerHTML = `
                                                                                <span class="material-icons mr-2">check_circle</span>
                                                                                Konfirmasi Pembayaran
                                                                            `;
                paymentBtn.onclick = () => checkout(selectedMethod);  // Ubah onclick ke checkout
            } else {
                paymentBtn.innerHTML = `
                                                                                <span class="material-icons mr-2">payment</span>
                                                                                Lanjut Pembayaran
                                                                            `;
                paymentBtn.onclick = showPaymentModal;  // Reset ke showPaymentModal
            }
        }

        function checkout(paymentMethod) {
            fetch('/user/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart: cart, payment_method: paymentMethod, voucher_code: appliedVoucher, points_used: pointsUsed })
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Server error: ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        // Tampilkan modal sukses
                        document.getElementById('checkout-success-message').textContent = data.message;
                        document.getElementById('checkout-success-modal').classList.remove('hidden');
                        // Bersihkan cart
                        localStorage.removeItem('cart');
                        cart = [];
                        loadCart();
                        if (window.updateCartBadge) window.updateCartBadge(0);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error: ' + err.message);
                });
        }

        function redirectToTransactions() {
            document.getElementById('checkout-success-modal').classList.add('hidden');
            window.location.href = '/user/transactions';
        }

        function showAvailableVouchers() {
            let total = cart.reduce((sum, item) => {
                let product = products.find(p => p.id == item.product_id);
                return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
            }, 0);

            fetch('/user/available-vouchers', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.json())
                .then(vouchers => {
                    let html = '';
                    if (vouchers.length === 0) {
                        html = `
                                                                                                <div class="text-center py-8">
                                                                                                    <span class="material-icons text-gray-300 text-5xl">confirmation_number</span>
                                                                                                    <p class="text-gray-500 mt-3">Tidak ada voucher tersedia</p>
                                                                                                </div>
                                                                                                `;
                    } else {
                        vouchers.forEach(voucher => {
                            let isEligible = total >= (voucher.min_order || 0);
                            let isAvailable = !voucher.usage_limit || voucher.usage_count < voucher.usage_limit;
                            html += `
                                                                                                    <div class="border border-gray-200 rounded-lg p-4 mb-3 ${isEligible && isAvailable ? 'bg-white' : 'bg-gray-50'}">
                                                                                                        <div class="flex items-start gap-3 mb-3">
                                                                                                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                                                                                <span class="material-icons text-cyan-500 text-sm">confirmation_number</span>
                                                                                                            </div>
                                                                                                            <div class="flex-1 min-w-0">
                                                                                                                <h3 class="font-semibold text-gray-900 mb-1">${voucher.name}</h3>
                                                                                                                <p class="text-sm text-gray-600 mb-2">${voucher.description}</p>
                                                                                                                <p class="text-xs text-gray-500">Min. Pembelian: Rp ${(voucher.min_order || 0).toLocaleString()}</p>
                                                                                                                ${voucher.usage_limit ? `<p class="text-xs text-gray-500">Penggunaan: ${voucher.usage_count}/${voucher.usage_limit}</p>` : ''}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <button 
                                                                                                            onclick="applyVoucherFromList('${voucher.code}')" 
                                                                                                            ${isEligible && isAvailable ? '' : 'disabled'} 
                                                                                                            class="${isEligible && isAvailable ? 'bg-cyan-500 hover:bg-cyan-600 text-white' : 'bg-gray-200 text-gray-500 cursor-not-allowed'} w-full py-2 rounded-lg text-sm font-medium transition-colors"
                                                                                                        >
                                                                                                            ${isEligible && isAvailable ? 'Gunakan Voucher' : isAvailable ? 'Belum Memenuhi Syarat' : 'Batas Penggunaan Tercapai'}
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    `;
                        });
                    }
                    document.getElementById('voucher-list').innerHTML = html;
                    document.getElementById('voucher-modal').classList.remove('hidden');
                })
                .catch(err => alert('Error: ' + err.message));
        }

        function processShoppingCommand() {
            const command = document.getElementById('shopping-command').value.trim();
            if (!command) {
                showShoppingFeedback('Masukkan perintah terlebih dahulu.', 'error');
                return;
            }

            // Set loading state
            setShoppingLoading(true);

            // Kirim ke endpoint
            fetch('/user/parse-shopping-command', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ command: command })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.added_products.length > 0) {
                        // Tambahkan produk ke cart (localStorage)
                        data.added_products.forEach(product => {
                            let item = cart.find(i => i.product_id == product.product_id);
                            if (item) {
                                item.quantity += product.qty;  // Add qty
                            } else {
                                cart.push({
                                    product_id: product.product_id,
                                    quantity: product.qty,
                                    is_tebus_murah: false  // Asumsikan bukan tebus murah
                                });
                            }
                        });
                        localStorage.setItem('cart', JSON.stringify(cart));
                        loadCart();  // Reload cart
                        showShoppingFeedback(data.message, 'success');
                    } else {
                        showShoppingFeedback(data.message || 'Tidak ada produk yang ditambahkan.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showShoppingFeedback('Terjadi kesalahan. Coba lagi.', 'error');
                })
                .finally(() => {
                    // Reset loading state
                    setShoppingLoading(false);
                });
        }

        function setShoppingLoading(isLoading) {
            const btn = document.getElementById('shopping-btn');
            const icon = document.getElementById('shopping-icon');
            const text = document.getElementById('shopping-text');
            const feedback = document.getElementById('shopping-feedback');

            if (isLoading) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                icon.textContent = 'hourglass_empty';  // Spinner icon
                icon.classList.add('animate-spin');  // Tambahkan animasi spin (lihat CSS di bawah)
                text.textContent = 'Memproses...';
                feedback.classList.add('hidden');  // Sembunyikan feedback selama loading
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.textContent = 'send';
                icon.classList.remove('animate-spin');
                text.textContent = 'Kirim';
            }
        }

        function showShoppingFeedback(message, type) {
            const feedbackEl = document.getElementById('shopping-feedback');
            feedbackEl.textContent = message;
            feedbackEl.className = `mt-4 text-sm ${type === 'success' ? 'text-green-300' : 'text-red-300'}`;
            feedbackEl.classList.remove('hidden');
            setTimeout(() => feedbackEl.classList.add('hidden'), 5000);  // Sembunyikan setelah 5 detik
        }


        function closeVoucherModal() {
            document.getElementById('voucher-modal').classList.add('hidden');
        }

        function applyVoucherFromList(code) {
            document.getElementById('voucher-code').value = code;
            applyVoucher();
            closeVoucherModal();
        }

        loadCart();
    </script>
    <script>
        let tebusMurahSelected = null;

        function showTebusMurahModal() {
            let html = '';
            let totalBelanja = cart.reduce((sum, item) => {
                let product = products.find(p => p.id == item.product_id);
                return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
            }, 0);
        
            if (tebusMurahList.length === 0) {
                html = '<div class="text-center text-gray-500">Tidak ada produk tebus murah saat ini.</div>';
            } else {
                tebusMurahList.forEach(tm => {
                    let eligible = totalBelanja >= tm.min_order;
                    html += `
                        <div class="border-b py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="${tm.product.image ? '/storage/' + tm.product.image : '/images/no-image.png'}" 
                                     alt="${tm.product.name}" 
                                     class="w-12 h-12 rounded object-cover border border-gray-200">
                                <div>
                                    <div class="font-semibold">${tm.product.name}</div>
                                    <div class="text-sm text-gray-500">Tebus Rp ${tm.tebus_price.toLocaleString()} (Min. belanja Rp ${tm.min_order.toLocaleString()})</div>
                                </div>
                            </div>
                            <button class="px-3 py-1 rounded bg-cyan-500 text-white font-semibold text-sm ${eligible ? '' : 'opacity-50 cursor-not-allowed'}"
                                onclick="addTebusMurahToCart(${tm.product_id}, ${tm.tebus_price}, ${tm.max_qty}, ${tm.min_order})"
                                ${eligible ? '' : 'disabled'}>
                                Tebus
                            </button>
                        </div>
                    `;
                });
            }
            document.getElementById('tebus-murah-list').innerHTML = html;
            document.getElementById('tebus-murah-modal').classList.remove('hidden');
        }
        function closeTebusMurahModal() {
            document.getElementById('tebus-murah-modal').classList.add('hidden');
        }

        function addTebusMurahToCart(productId, tebusPrice, maxQty, minOrder) {
            // Cek sudah ada di cart belum
            let item = cart.find(i => i.product_id == productId && i.is_tebus_murah);
            if (item) {
                alert('Produk tebus murah sudah ada di keranjang.');
                return;
            }
            // Cek total belanja
            let totalBelanja = cart.reduce((sum, item) => {
                let product = products.find(p => p.id == item.product_id);
                return sum + (product ? (product.final_price || product.selling_price) * item.quantity : 0);
            }, 0);
            if (totalBelanja < minOrder) {
                alert('Belanja minimal Rp ' + minOrder.toLocaleString() + ' untuk tebus murah.');
                return;
            }
            // Tambahkan ke cart
            cart.push({
                product_id: productId,
                quantity: 1,
                tebus_price: tebusPrice,
                is_tebus_murah: true
            });
            localStorage.setItem('cart', JSON.stringify(cart));
            closeTebusMurahModal();
            loadCart();
        }
        function setTebusMurahQty(productId, value, maxQty) {
            let item = cart.find(i => i.product_id == productId && i.is_tebus_murah);
            if (!item) return;
            let qty = parseInt(value);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > maxQty) qty = maxQty;
            item.quantity = qty;
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }
    </script>
    <style>
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
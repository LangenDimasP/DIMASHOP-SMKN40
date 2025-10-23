@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">qr_code_scanner</span>
                    Scan Produk Kasir
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Scan QR Code atau Barcode produk untuk menambahkan ke
                    keranjang</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- SECTION AI AGENT (PINDAH KE ATAS) -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 flex flex-col gap-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perintah Otomatis (AI Agent)</label>
                        <div class="flex flex-col sm:flex-row gap-2 items-stretch">
                            <textarea id="agent-command" rows="2"
                                class="flex-1 border border-gray-300 rounded-lg p-2 resize-none"
                                placeholder="Contoh: scan beng beng 6, coca cola 5"></textarea>
                            <button id="ai-agent-btn" onclick="processAgentCommand()"
                                class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2 min-w-[140px] justify-center">
                                <span class="material-icons">smart_toy</span>
                                Proses Perintah
                            </button>
                        </div>
                        <!-- Loading State -->
                        <div id="ai-loading" class="flex items-center gap-2 mt-2 hidden">
                            <span class="material-icons animate-spin text-cyan-500">autorenew</span>
                            <span class="text-sm text-cyan-700">AI sedang memproses perintah...</span>
                        </div>
                    </div>
                </div>
                <!-- END SECTION AI AGENT -->

                <!-- Scanner Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Scan Input Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="material-icons text-cyan-500">qr_code_scanner</span>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Scan Produk</h2>
                        </div>

                        <div class="space-y-3">
                            <div class="relative">
                                <span
                                    class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                                <input type="text" id="scan-input"
                                    placeholder="Scan QR/Barcode Produk atau ketik kode manual"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                    autofocus oninput="searchProducts(this.value)"
                                    onkeypress="if(event.key === 'Enter') scanProduct()">
                                <!-- Dropdown Suggestions -->
                                <div id="suggestions-dropdown"
                                    class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden max-h-60 overflow-y-auto">
                                    <!-- Suggestions will be populated here -->
                                </div>
                            </div>
                            <button onclick="scanProduct()"
                                class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center">
                                <span class="material-icons mr-2">add_circle</span>
                                Tambah Produk
                            </button>
                        </div>
                    </div>

                    <!-- Cart Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <span class="material-icons text-cyan-500 mr-2">shopping_cart</span>
                                    Keranjang Belanja
                                </h2>
                                <span id="item-count" class="text-sm text-gray-600">0 item</span>
                                <button onclick="showTebusMurahModal()"
                                    class="mb-4 px-3 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded font-semibold text-sm flex items-center gap-2">
                                    <span class="material-icons text-sm">local_offer</span> Tebus Murah
                                </button>
                            </div>
                        </div>

                        <div id="cart-container">
                            <div id="cart-list" class="divide-y divide-gray-200">
                                <!-- Cart items akan ditampilkan di sini -->
                            </div>

                            <!-- Empty State -->
                            <div id="empty-cart" class="p-12 text-center">
                                <span class="material-icons text-gray-300 text-6xl">shopping_cart</span>
                                <p class="text-gray-500 mt-4">Keranjang masih kosong</p>
                                <p class="text-gray-400 text-sm mt-1">Scan produk untuk memulai transaksi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Ringkasan</h2>
                        </div>

                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Total -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Total Item</span>
                                    <span id="total-items">0</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-base font-semibold text-gray-900">Total Pembayaran</span>
                                        <span id="cart-total" class="text-lg font-bold text-cyan-500">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <button id="checkout-btn" onclick="showCheckoutModal()"
                                class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center"
                                disabled>
                                <span class="material-icons mr-2">payment</span>
                                Checkout
                            </button>

                            <!-- Clear Cart -->
                            <button onclick="clearCart()"
                                class="w-full bg-white border border-red-300 hover:bg-red-50 text-red-600 font-medium py-2 rounded-lg transition-colors flex items-center justify-center">
                                <span class="material-icons text-sm mr-2">delete_outline</span>
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
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


    <!-- Modal Peringatan Stok Kurang -->
    <div id="ai-stock-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                <span class="material-icons text-yellow-500">warning</span>
                Stok Tidak Cukup
            </h3>
            <p class="text-gray-700 mb-4">
                Stok produk <span id="ai-stock-product-name" class="font-semibold"></span> hanya tersedia <span
                    id="ai-stock-available" class="font-semibold"></span>.<br>
                Apakah ingin menambahkan sebanyak stok yang tersedia?
            </p>
            <div class="flex gap-2 justify-end">
                <button onclick="closeAiStockModal()"
                    class="px-4 py-2 rounded bg-cyan-500 text-gray-700 hover:bg-gray-300">Batal</button>
                <button id="ai-stock-accept-btn"
                    class="px-4 py-2 rounded bg-cyan-500 text-white hover:bg-cyan-600 font-semibold">Ya, Tambahkan</button>
            </div>
        </div>
    </div>


    <!-- Checkout Modal -->
    <div id="checkout-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="material-icons text-cyan-500 mr-2">payment</span>
                        Checkout
                    </h2>
                    <button onclick="closeCheckoutModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Member Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Member (Opsional)</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span
                                class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">badge</span>
                            <input type="text" id="member-id" placeholder="Scan/Ketik ID Member"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                onkeypress="if(event.key === 'Enter') checkMember()">
                        </div>
                        <button onclick="checkMember()"
                            class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <span class="material-icons text-sm">search</span>
                        </button>
                    </div>

                    <!-- Member Info Card -->
                    <div id="member-info" class="hidden mt-3 p-4 bg-cyan-50 border border-cyan-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-icons text-cyan-500 text-sm">person</span>
                            </div>
                            <div class="flex-1">
                                <p id="member-name" class="font-semibold text-gray-900 mb-1"></p>
                                <p id="member-balance" class="text-sm text-gray-600"></p>
                            </div>
                            <button onclick="clearMember()" class="text-red-500 hover:text-red-600">
                                <span class="material-icons text-sm">close</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
                    <div class="space-y-2">
                        <button onclick="selectPaymentMethod('cash')" id="cash-btn"
                            class="payment-method-btn w-full border-2 border-gray-200 hover:border-cyan-500 rounded-lg p-4 text-left transition-all flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons text-green-500">payments</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Cash</p>
                                    <p class="text-sm text-gray-600">Pembayaran tunai</p>
                                </div>
                            </div>
                            <span class="material-icons text-gray-400">chevron_right</span>
                        </button>

                        <button onclick="selectPaymentMethod('dimascash')" id="dimascash-btn"
                            class="payment-method-btn w-full border-2 border-gray-200 rounded-lg p-4 text-left transition-all flex items-center justify-between disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="material-icons text-cyan-500">account_balance_wallet</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">DimasCash</p>
                                    <p class="text-sm text-gray-600">Memerlukan member</p>
                                </div>
                            </div>
                            <span class="material-icons text-gray-400">chevron_right</span>
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Input -->
                <div id="cash-payment-section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar (Tunai)</label>
                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">attach_money</span>
                        <input type="number" id="cash-amount" placeholder="Masukkan jumlah uang tunai"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            oninput="calculateChange()" min="0">
                    </div>

                    <!-- Change Display -->
                    <div id="change-display" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="material-icons text-green-500 text-sm">account_balance_wallet</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Kembalian</p>
                                <p id="change-amount" class="text-lg font-bold text-green-600">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Pesanan</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Item</span>
                            <span id="modal-items" class="font-medium text-gray-900">0</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-900">Total Pembayaran</span>
                                <span id="modal-total" class="text-lg font-bold text-cyan-500">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 space-y-2">
                <button id="proceed-checkout-btn" onclick="proceedCheckout()"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 rounded-lg transition-colors">
                    Proses Pembayaran
                </button>
                <button onclick="closeCheckoutModal()"
                    class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        window.allProducts = @json($products);
        window.tebusMurahList = @json($tebusMurahList);
    </script>
    <script>
        let cart = JSON.parse(localStorage.getItem('kasirCart')) || [];
        let productStocks = JSON.parse(localStorage.getItem('productStocks')) || {};
        let selectedMember = null;
        let selectedPaymentMethod = 'cash';

        // Load display saat halaman load
        document.addEventListener('DOMContentLoaded', function () {
            updateCartDisplay();
            document.getElementById('scan-input').focus();
        });

        async function scanProduct() {
            const code = document.getElementById('scan-input').value.trim();
            if (!code) {
                showNotification('Masukkan kode produk', 'error');
                return;
            }
            // Cari produk di allProducts
            const product = window.allProducts.find(p => p.unique_code === code);
            if (!product) {
                showNotification('Produk tidak ditemukan', 'error');
                document.getElementById('scan-input').value = '';
                return;
            }

            // Gunakan stok dari JS tanpa fetch (cepat, tanpa delay)
            let latestStock = product.stock;

            let existing = cart.find(item => item.id === product.id);
            let availableStock = productStocks[product.id] !== undefined ? productStocks[product.id] : latestStock;

            // Tambahkan pengecekan stok kosong (baru)
            if (availableStock <= 0) {
                showNotification('Stok produk "' + product.name + '" habis', 'error');
                document.getElementById('scan-input').value = '';
                return;
            }

            if (existing && existing.quantity >= availableStock) {
                showNotification('Stok produk habis', 'error');
                return;
            }
            if (existing) {
                existing.quantity += 1;
            } else {
                let newProduct = Object.assign({}, product);
                newProduct.quantity = 1;
                cart.push(newProduct);
            }
            productStocks[product.id] = availableStock - 1;
            localStorage.setItem('kasirCart', JSON.stringify(cart));
            localStorage.setItem('productStocks', JSON.stringify(productStocks));
            updateCartDisplay();
            document.getElementById('scan-input').value = '';
            document.getElementById('scan-input').focus();
            showNotification('Produk ditambahkan ke keranjang');
        }
        setInterval(async () => {
            try {
                const ids = window.allProducts.map(p => p.id);
                const res = await fetch('/kasir/get-product-stock', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids })
                });
                const data = await res.json();
                if (data.products) {
                    data.products.forEach(p => {
                        const product = window.allProducts.find(prod => prod.id === p.id);
                        if (product) {
                            product.stock = p.stock; // Update stok di allProducts
                            // Jika belum ada di productStocks, set ke stok baru
                            if (productStocks[p.id] === undefined) {
                                productStocks[p.id] = p.stock;
                            }
                        }
                    });
                    localStorage.setItem('productStocks', JSON.stringify(productStocks));
                }
            } catch (e) {
                console.error('Error updating stocks:', e);
            }
        }, 60000);
        function updateCartDisplay() {
            const cartList = document.getElementById('cart-list');
            const emptyCart = document.getElementById('empty-cart');
            const cartTotal = document.getElementById('cart-total');
            const itemCount = document.getElementById('item-count');
            const totalItems = document.getElementById('total-items');
            const checkoutBtn = document.getElementById('checkout-btn');

            cartList.innerHTML = '';
            let total = 0;
            let count = 0;

            if (cart.length === 0) {
                emptyCart.classList.remove('hidden');
                cartList.classList.add('hidden');
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                emptyCart.classList.add('hidden');
                cartList.classList.remove('hidden');
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                cart.forEach((item, index) => {
                    // Jika item adalah tebus murah, tampilkan sebagai sub-produk kecil
                    if (item.is_tebus_murah) {
                        const tebusDiv = document.createElement('div');
                        tebusDiv.className = 'p-3 pl-3 bg-yellow-50 border-l-4 border-yellow-400 rounded mb-2 flex items-center gap-3';
                        tebusDiv.innerHTML = `
                                    <img src="${item.image ? '/storage/' + item.image : '/images/no-image.png'}" alt="${item.name}" class="w-10 h-10 rounded object-cover border border-yellow-200 mr-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-semibold text-yellow-700 truncate">${item.name} <span class="ml-1 px-2 py-0.5 bg-yellow-200 text-yellow-800 rounded text-[10px]">Tebus Murah</span></div>
                                        <div class="text-xs text-yellow-700">Rp ${item.price.toLocaleString('id-ID')} x 
                                            <input type="number" min="1" max="${item.max_qty}" value="${item.quantity}" style="width:40px" 
                                                onchange="setTebusMurahQtyKasir(${index}, this.value, ${item.max_qty})" 
                                                class="border rounded px-1 text-xs text-center w-12">
                                            / Maks ${item.max_qty}
                                        </div>
                                    </div>
                                    <button onclick="removeFromCart(${index})" class="text-yellow-600 hover:text-red-500 p-1" title="Hapus">
                                        <span class="material-icons text-xs">close</span>
                                    </button>
                                `;
                        cartList.appendChild(tebusDiv); // Tambahkan sub-produk tebus murah
                        total += item.price * item.quantity; // Hitung total untuk tebus murah
                        count += item.quantity;
                        return; // Skip ke item berikutnya, jangan tampilkan sebagai produk utama
                    }

                    // Hitung subtotal berdasarkan promo untuk produk utama
                    let subtotal = item.price * item.quantity; // Default
                    const product = window.allProducts.find(p => p.id === item.id);  // Tetap gunakan ini
                    if (product && product.promo_active && product.promo_type === 'buy_x_for_y') {
                        const x = product.promo_buy;
                        const promoPrice = product.promo_get;
                        const sets = Math.floor(item.quantity / x);
                        const remainder = item.quantity % x;
                        subtotal = (sets * promoPrice) + (remainder * item.price);
                    }
                    // Untuk buy_x_get_y_free, tetap gunakan harga normal (gratis tidak mengurangi subtotal)

                    total += subtotal;
                    count += item.quantity;

                    const div = document.createElement('div');

                    // Langsung gunakan variabel 'product' yang sudah ada
                    let promoDesc = '';
                    let freeItems = 0;

                    // Logika promo (mirip cart.blade.php)
                    if (product && product.promo_active && product.promo_type === 'buy_x_get_y_free') {
                        const x = product.promo_buy;
                        const y = product.promo_get;
                        const fullSets = Math.floor(item.quantity / x);
                        freeItems = fullSets * y;
                        promoDesc = `Beli ${x} Gratis ${y}`;
                    } else if (product && product.promo_active && product.promo_type === 'buy_x_for_y') {
                        promoDesc = `Beli ${product.promo_buy} Hanya Rp ${product.promo_get.toLocaleString('id-ID')}`;
                    }

                    // Jika ada item gratis (buy_x_get_y_free), tampilkan sub-produk gratis dulu
                    if (freeItems > 0) {
                        const freeDiv = document.createElement('div');
                        freeDiv.className = 'p-3 pl-3 bg-green-50 border-l-4 border-green-400 rounded mb-2 flex items-center gap-3';
                        freeDiv.innerHTML = `
                                    <img src="${item.image ? '/storage/' + item.image : '/images/no-image.png'}" alt="${item.name}" class="w-10 h-10 rounded object-cover border border-green-200 mr-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-semibold text-green-700 truncate">${item.name} <span class="ml-1 px-2 py-0.5 bg-green-200 text-green-800 rounded text-[10px]">Gratis Promo</span></div>
                                        <div class="text-xs text-green-700">Gratis ${freeItems} pcs</div>
                                    </div>
                                `;
                        cartList.appendChild(freeDiv); // Tambahkan sub-produk gratis sebelum produk utama
                    }

                    // Tampilan produk utama (dengan deskripsi promo jika ada)
                    div.className = 'p-4 hover:bg-gray-50 transition-colors';
                    div.innerHTML = `
                                <div class="flex gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        <img src="${item.image ? '/storage/' + item.image : '/images/no-image.png'}" 
                                             alt="${item.name}" 
                                             class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 mb-1">${item.name}</h3>
                                        <p class="text-sm text-gray-600 mb-2">Rp ${item.price.toLocaleString('id-ID')}</p>
                                        ${promoDesc ? `<p class="text-green-600 text-sm">${promoDesc}</p>` : ''}

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                <button 
                                                    onclick="updateQuantity(${index}, ${item.quantity - 1})" 
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 text-gray-600"
                                                >
                                                    <span class="material-icons text-sm">remove</span>
                                                </button>
                                                <input 
                                                    type="number" 
                                                    value="${item.quantity}" 
                                                    onchange="updateQuantity(${index}, this.value)" 
                                                    class="w-12 h-8 text-center border-x border-gray-300 text-sm focus:outline-none"
                                                    min="1"
                                                >
                                                <button 
                                                    onclick="updateQuantity(${index}, ${item.quantity + 1})" 
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 text-gray-600"
                                                >
                                                    <span class="material-icons text-sm">add</span>
                                                </button>
                                            </div>

                                            <button 
                                                onclick="removeFromCart(${index})" 
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
                                        <span class="font-semibold text-gray-900">Rp ${subtotal.toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                            `;
                    cartList.appendChild(div);
                });
            }

            cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            itemCount.textContent = `${count} item${count > 1 ? 's' : ''}`;
            totalItems.textContent = `${count} item${count > 1 ? 's' : ''}`;
        }

        // Fungsi untuk update qty tebus murah di kasir
        function setTebusMurahQtyKasir(index, value, maxQty) {
            let item = cart[index];
            if (!item || !item.is_tebus_murah) return;
            let qty = parseInt(value);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > maxQty) qty = maxQty;
            item.quantity = qty;
            localStorage.setItem('kasirCart', JSON.stringify(cart));
            updateCartDisplay();
        }



        async function updateQuantity(index, qty) {
            qty = parseInt(qty);
            if (qty <= 0) {
                removeFromCart(index);
                return;
            }
            const product = cart[index];

            // Ambil stok terbaru dari server
            let latestStock = product.stock;
            try {
                const res = await fetch('/kasir/get-product-stock', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids: [product.id] })
                });
                const data = await res.json();
                if (data.products && data.products.length > 0) {
                    latestStock = data.products[0].stock;
                }
            } catch (e) {
                // fallback pakai stok lama
            }

            // Cek stok terbaru
            if (qty > latestStock) {
                showNotification('Stok produk "' + product.name + '" hanya tersedia ' + latestStock, 'error');
                qty = latestStock;
            }

            // Update stok lokal
            if (productStocks[product.id] === undefined) {
                productStocks[product.id] = latestStock;
            }
            // Hitung selisih qty
            let diff = qty - product.quantity;
            productStocks[product.id] -= diff;
            product.quantity = qty;
            localStorage.setItem('kasirCart', JSON.stringify(cart));
            localStorage.setItem('productStocks', JSON.stringify(productStocks));
            updateCartDisplay();
        }

        function removeFromCart(index) {
            if (confirm('Hapus produk ini dari keranjang?')) {
                // Kembalikan stok
                const item = cart[index];
                // Pastikan productStocks benar-benar diupdate walaupun nilainya 0
                if (typeof productStocks[item.id] !== 'undefined') {
                    productStocks[item.id] += item.quantity;
                } else {
                    // Jika belum ada di productStocks, set ke stok awal produk
                    productStocks[item.id] = item.stock;
                }
                cart.splice(index, 1);
                localStorage.setItem('kasirCart', JSON.stringify(cart));
                localStorage.setItem('productStocks', JSON.stringify(productStocks));
                updateCartDisplay();
            }
        }

        function clearCart() {
            if (cart.length === 0) {
                showNotification('Keranjang sudah kosong', 'error');
                return;
            }
            if (confirm('Kosongkan semua item di keranjang?')) {
                // Kembalikan semua stok
                cart.forEach(item => {
                    if (typeof productStocks[item.id] !== 'undefined') {
                        productStocks[item.id] += item.quantity;
                    } else {
                        productStocks[item.id] = item.stock;
                    }
                });
                cart = [];
                localStorage.setItem('kasirCart', JSON.stringify(cart));
                localStorage.setItem('productStocks', JSON.stringify(productStocks));
                updateCartDisplay();
                showNotification('Keranjang dikosongkan');
            }
        }

        // Fungsi helper untuk menghitung total dengan promo
        function calculateTotal() {
            let total = 0;
            cart.forEach(item => {
                if (item.is_tebus_murah) {
                    total += item.price * item.quantity;
                    return;
                }
                let subtotal = item.price * item.quantity; // Default
                const product = window.allProducts.find(p => p.id === item.id);
                if (product && product.promo_active && product.promo_type === 'buy_x_for_y') {
                    const x = product.promo_buy;
                    const promoPrice = product.promo_get;
                    const sets = Math.floor(item.quantity / x);
                    const remainder = item.quantity % x;
                    subtotal = (sets * promoPrice) + (remainder * item.price);
                }
                // Untuk buy_x_get_y_free, tetap gunakan harga normal
                total += subtotal;
            });
            return total;
        }
        function showCheckoutModal() {
            if (cart.length === 0) {
                showNotification('Keranjang kosong!', 'error');
                return;
            }

            // Gunakan fungsi helper untuk total
            let total = calculateTotal();
            let count = cart.reduce((sum, item) => sum + item.quantity, 0);

            document.getElementById('modal-items').textContent = `${count} item${count > 1 ? 's' : ''}`;
            document.getElementById('modal-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;

            document.getElementById('checkout-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('member-id').focus();
        }

        function closeCheckoutModal() {
            document.getElementById('checkout-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            clearMember();
            selectedPaymentMethod = 'cash';
            updatePaymentMethodButtons();
            // Reset cash input
            document.getElementById('cash-amount').value = '';
            document.getElementById('change-display').classList.add('hidden');
        }

        function checkMember() {
            const memberId = document.getElementById('member-id').value.trim();
            if (!memberId) {
                showNotification('Masukkan ID Member', 'error');
                return;
            }

            fetch('/kasir/get-member', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ member_id: memberId })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    selectedMember = data.member;
                    document.getElementById('member-name').textContent = `${data.member.name}`;
                    document.getElementById('member-balance').textContent = `Saldo: Rp ${data.member.balance.toLocaleString('id-ID')}`;
                    document.getElementById('member-info').classList.remove('hidden');
                    document.getElementById('dimascash-btn').disabled = false;
                    showNotification('Member ditemukan');
                } else {
                    showNotification(data.message, 'error');
                    clearMember();
                }
            }).catch(err => {
                showNotification('Error: ' + err.message, 'error');
            });
        }

        function clearMember() {
            selectedMember = null;
            document.getElementById('member-id').value = '';
            document.getElementById('member-info').classList.add('hidden');
            document.getElementById('dimascash-btn').disabled = true;
            selectedPaymentMethod = 'cash';
            updatePaymentMethodButtons();
        }

        function selectPaymentMethod(method) {
            if (method === 'dimascash' && !selectedMember) {
                showNotification('Pilih member terlebih dahulu', 'error');
                return;
            }
            selectedPaymentMethod = method;
            updatePaymentMethodButtons();

            // Show/hide cash input
            const cashSection = document.getElementById('cash-payment-section');
            if (method === 'cash') {
                cashSection.classList.remove('hidden');
                document.getElementById('cash-amount').focus();
            } else {
                cashSection.classList.add('hidden');
                document.getElementById('cash-amount').value = '';
                document.getElementById('change-display').classList.add('hidden');
            }
        }

        function calculateChange() {
            const cashAmount = parseInt(document.getElementById('cash-amount').value) || 0;
            let total = calculateTotal(); // Gunakan fungsi helper
        
            const change = cashAmount - total;
            const changeDisplay = document.getElementById('change-display');
            const changeAmount = document.getElementById('change-amount');
        
            if (cashAmount >= total && cashAmount > 0) {
                changeAmount.textContent = `Rp ${change.toLocaleString('id-ID')}`;
                changeDisplay.classList.remove('hidden');
            } else {
                changeDisplay.classList.add('hidden');
            }
        }

        function updatePaymentMethodButtons() {
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('border-cyan-500', 'bg-cyan-50');
                btn.classList.add('border-gray-200');
            });

            const selectedBtn = document.getElementById(selectedPaymentMethod + '-btn');
            if (selectedBtn && !selectedBtn.disabled) {
                selectedBtn.classList.remove('border-gray-200');
                selectedBtn.classList.add('border-cyan-500', 'bg-cyan-50');
            }
        }

        function proceedCheckout() {
            const memberId = document.getElementById('member-id').value.trim();
            const cashAmount = parseInt(document.getElementById('cash-amount').value) || 0;

            if (selectedPaymentMethod === 'dimascash' && !memberId) {
                showNotification('Member diperlukan untuk pembayaran DimasCash', 'error');
                return;
            }

            if (selectedPaymentMethod === 'cash') {
                let total = calculateTotal(); // Gunakan fungsi helper
                if (cashAmount < total) {
                    showNotification('Jumlah bayar kurang dari total belanja', 'error');
                    return;
                }
            }

            // Disable button to prevent double click
            const btn = document.getElementById('proceed-checkout-btn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            fetch('/kasir/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cart,
                    payment_method: selectedPaymentMethod,
                    member_id: memberId,
                    cash_amount: selectedPaymentMethod === 'cash' ? cashAmount : null
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Transaksi berhasil!');
                        // Kosongkan keranjang dan localStorage
                        cart = [];
                        productStocks = {};
                        localStorage.removeItem('kasirCart');
                        localStorage.removeItem('productStocks');
                        updateCartDisplay();
                        closeCheckoutModal();

                        // Redirect to print page
                        setTimeout(() => {
                            window.location.href = '/kasir/transaksi/' + data.transaction_id + '/print';
                        }, 1000);
                    } else {
                        showNotification(data.message, 'error');
                        btn.disabled = false;
                        btn.textContent = 'Proses Pembayaran';
                    }
                })
                .catch(err => {
                    showNotification('Error: ' + err.message, 'error');
                    btn.disabled = false;
                    btn.textContent = 'Proses Pembayaran';
                });
        }

        function searchProducts(query) {
            const dropdown = document.getElementById('suggestions-dropdown');
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }

            fetch('/kasir/search-products', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ query })
            }).then(res => res.json()).then(data => {
                dropdown.innerHTML = '';
                if (data.products && data.products.length > 0) {
                    data.products.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                        div.innerHTML = `
                                            <div class="flex items-center gap-3">
                                                <img src="${product.image ? '/storage/' + product.image : '/images/no-image.png'}" 
                                                     alt="${product.name}" 
                                                     class="w-10 h-10 object-cover rounded">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900">${product.name}</p>
                                                    <p class="text-sm text-gray-600">Rp ${product.price.toLocaleString('id-ID')} | Stok: ${product.stock}</p>
                                                </div>
                                            </div>
                                        `;
                        div.onclick = () => selectProductFromSuggestion(product);
                        dropdown.appendChild(div);
                    });
                    dropdown.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                }
            }).catch(err => {
                console.error('Error searching products:', err);
                dropdown.classList.add('hidden');
            });
        }

        function selectProduct(product, forceQty = null, action = 'add') { // Tambah parameter action
            // Cari produk di cart
            let existing = cart.find(item => item.id === product.id);
            // Hitung stok yang tersisa (ambil dari productStocks jika ada, kalau tidak dari product.stock)
            let availableStock = productStocks[product.id] !== undefined ? productStocks[product.id] : product.stock;
            let currentQty = existing ? existing.quantity : 0;

            // Jika forceQty diberikan (untuk AI agent atau suggestion), tambahkan sekaligus
            if (forceQty !== null) {
                // Jika action == 'set', kosongkan qty yang ada dulu
                if (action === 'set' && existing) {
                    // Kembalikan stok yang sudah ada ke productStocks
                    if (productStocks[product.id] !== undefined) {
                        productStocks[product.id] += existing.quantity;
                    } else {
                        productStocks[product.id] = product.stock;
                    }
                    existing.quantity = 0; // Kosongkan qty di cart
                }

                // Batasi qty maksimal ke stok yang tersedia
                let addQty = Math.min(forceQty, availableStock - (action === 'set' ? 0 : currentQty)); // Untuk set, hitung dari 0
                if (addQty <= 0) {
                    showNotification('Jumlah produk "' + product.name + '" di keranjang sudah sama dengan stok tersedia', 'error');
                    return;
                }
                if (existing) {
                    existing.quantity += addQty; // Tambah ke qty yang ada (atau 0 jika set)
                } else {
                    // Penting: clone object agar tidak mengubah referensi product global!
                    let newProduct = Object.assign({}, product);
                    newProduct.quantity = addQty;
                    cart.push(newProduct);
                }
                // Kurangi stok lokal
                if (productStocks[product.id] === undefined) {
                    productStocks[product.id] = product.stock;
                }
                productStocks[product.id] -= addQty;
            } else {
                // Default: tambah 1
                if (existing) {
                    existing.quantity += 1;
                } else {
                    let newProduct = Object.assign({}, product);
                    newProduct.quantity = 1;
                    cart.push(newProduct);
                }
                if (productStocks[product.id] === undefined) {
                    productStocks[product.id] = product.stock;
                }
                productStocks[product.id] -= 1;
            }
            // Simpan ke localStorage
            localStorage.setItem('kasirCart', JSON.stringify(cart));
            localStorage.setItem('productStocks', JSON.stringify(productStocks));
            updateCartDisplay();
            // Kosongkan input dan hide dropdown
            document.getElementById('scan-input').value = '';
            document.getElementById('suggestions-dropdown').classList.add('hidden');
            document.getElementById('scan-input').focus();
            showNotification('Produk ditambahkan ke keranjang');
        }

        window.aiAgentQtyMap = {}; // { [productId]: qty }

        function selectProductFromSuggestion(product) {
            // Cek apakah ada qty permintaan AI agent untuk produk ini
            const aiQty = window.aiAgentQtyMap[product.id];
            if (aiQty) {
                selectProduct(product, aiQty);
                delete window.aiAgentQtyMap[product.id]; // reset hanya untuk produk ini
            } else {
                selectProduct(product);
            }
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('suggestions-dropdown');
            const input = document.getElementById('scan-input');
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        let aiStockModalData = null;

        async function processAgentCommand() {
            const command = document.getElementById('agent-command').value.trim();
            if (!command) {
                showNotification('Masukkan perintah terlebih dahulu', 'error');
                return;
            }
            document.getElementById('ai-loading').classList.remove('hidden');
            document.getElementById('ai-agent-btn').disabled = true;

            try {
                const res = await fetch('/kasir/parse-agent-command', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ command })
                });
                const data = await res.json();
                if (!data.items || !Array.isArray(data.items) || data.items.length === 0) {
                    showNotification('Perintah tidak dipahami AI', 'error');
                    return;
                }

                for (const item of data.items) {
                    // Cari produk di database
                    const searchRes = await fetch('/kasir/search-products', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ query: item.name })
                    });
                    const searchData = await searchRes.json();
                    if (searchData.products && searchData.products.length > 0) {
                        const product = searchData.products[0];
                        // Jika qty == -1 (semua stok), set ke stok maksimal produk
                        let qtyToAdd = item.qty;
                        if (item.qty === -1) {
                            qtyToAdd = product.stock;
                            showNotification(`Menambahkan semua stok "${product.name}" (${product.stock} pcs)`, 'success');
                        }
                        // Cek stok
                        if (product.stock <= 0) {
                            showNotification('Produk "' + product.name + '" stok habis', 'error');
                            continue;
                        }
                        if (qtyToAdd > product.stock) {
                            // Tampilkan modal peringatan stok kurang
                            showAiStockModal(product, qtyToAdd);
                            const accepted = await waitForAiStockModal();
                            if (accepted) {
                                // Simpan info agar suggestion tahu qty dari AI
                                window.aiAgentQtyMap[product.id] = product.stock;
                                selectProduct(product, product.stock, item.action || 'add'); // Pass action
                                showNotification('Produk "' + product.name + '" ditambahkan sebanyak ' + product.stock);
                            } else {
                                showNotification('Penambahan produk "' + product.name + '" dibatalkan', 'error');
                            }
                        } else {
                            window.aiAgentQtyMap[product.id] = qtyToAdd;
                            selectProduct(product, qtyToAdd, item.action || 'add'); // Pass action
                            showNotification('Produk "' + product.name + '" ditambahkan sebanyak ' + qtyToAdd);
                        }
                    } else {
                        showNotification('Produk "' + item.name + '" tidak tersedia', 'error');
                    }
                }
                document.getElementById('agent-command').value = '';
            } catch (err) {
                showNotification('Error: ' + err.message, 'error');
            } finally {
                document.getElementById('ai-loading').classList.add('hidden');
                document.getElementById('ai-agent-btn').disabled = false;
            }
        }

        // Modal stok kurang
        function showAiStockModal(product, requestedQty) {
            aiStockModalData = { product, requestedQty, resolve: null };
            document.getElementById('ai-stock-product-name').textContent = product.name;
            document.getElementById('ai-stock-available').textContent = product.stock;
            document.getElementById('ai-stock-modal').classList.remove('hidden');
            // Set handler untuk tombol Ya
            document.getElementById('ai-stock-accept-btn').onclick = function () {
                closeAiStockModal(true);
            };
        }
        function closeAiStockModal(accept = false) {
            document.getElementById('ai-stock-modal').classList.add('hidden');
            if (aiStockModalData && aiStockModalData.resolve) {
                aiStockModalData.resolve(accept);
            }
            aiStockModalData = null;
        }
        function waitForAiStockModal() {
            return new Promise(resolve => {
                if (aiStockModalData) {
                    aiStockModalData.resolve = resolve;
                }
            });
        }

        function showTebusMurahModal() {
            let html = '';
            let totalBelanja = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            if (!window.tebusMurahList || window.tebusMurahList.length === 0) {
                html = '<div class="text-center text-gray-500">Tidak ada produk tebus murah saat ini.</div>';
            } else {
                window.tebusMurahList.forEach(tm => {
                    let eligible = totalBelanja >= tm.min_order;
                    html += `
                                        <div class="border-b py-3 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <img src="${tm.image ? '/storage/' + tm.image : '/images/no-image.png'}" class="w-10 h-10 rounded object-cover border border-yellow-200">
                                                <div>
                                                    <div class="font-semibold">${tm.name}</div>
                                                    <div class="text-sm text-gray-500">Tebus Rp ${tm.tebus_price.toLocaleString('id-ID')} (Min. belanja Rp ${tm.min_order.toLocaleString('id-ID')})</div>
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
            let item = cart.find(i => i.id == productId && i.is_tebus_murah);
            if (item) {
                showNotification('Produk tebus murah sudah ada di keranjang.', 'error');
                return;
            }
            // Cek total belanja
            let totalBelanja = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            if (totalBelanja < minOrder) {
                showNotification('Belanja minimal Rp ' + minOrder.toLocaleString('id-ID') + ' untuk tebus murah.', 'error');
                return;
            }
            // Ambil data produk
            let product = window.allProducts.find(p => p.id == productId);
            if (!product) {
                showNotification('Produk tidak ditemukan', 'error');
                return;
            }
            // Tambahkan ke cart
            cart.push({
                id: productId,
                name: product.name,
                price: tebusPrice,
                quantity: 1,
                image: product.image,
                is_tebus_murah: true,
                max_qty: maxQty
            });
            localStorage.setItem('kasirCart', JSON.stringify(cart));
            closeTebusMurahModal();
            updateCartDisplay();
        }


        function showNotification(message, type = 'success') {
            // Hapus toast lama jika masih ada
            const existingToast = document.getElementById('global-toast');
            if (existingToast) {
                existingToast.remove();
            }

            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? 'check_circle' : 'error';

            const notification = document.createElement('div');
            notification.id = 'global-toast';
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

        // Initialize on load
        updatePaymentMethodButtons();
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

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
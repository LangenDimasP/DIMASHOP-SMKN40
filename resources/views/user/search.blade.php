@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900">Pencarian Produk</span>
            </nav>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pencarian Produk</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Temukan produk yang Anda butuhkan</p>
            </div>

            <!-- Search Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <form action="{{ route('search') }}" method="GET" class="flex gap-2">
                    <div class="relative flex-1">
                        <span
                            class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                        <input type="text" name="q" value="{{ $query }}" placeholder="Cari produk..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <span class="material-icons text-sm">search</span>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                </form>
            </div>

            <!-- Price & Promo Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <!-- Price Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <div class="relative">
                            <select id="price-filter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent appearance-none bg-white pr-10">
                                <option value="">Semua Harga</option>
                                <option value="0-50000">
                                    < Rp 50.000</option>
                                <option value="50000-100000">Rp 50.000 - 100.000</option>
                                <option value="100000+">> Rp 100.000</option>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Promo Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo</label>
                        <div class="relative">
                            <select id="promo-filter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent appearance-none bg-white pr-10">
                                <option value="">Semua Produk</option>
                                <option value="promo">Sedang Promo</option>
                                <option value="no-promo">Harga Normal</option>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Active Filters & Reset -->
                <div class="flex items-center justify-between">
                    <span id="result-count" class="text-sm text-gray-600">Menampilkan semua produk</span>
                    <button id="reset-filters" onclick="resetFilters()"
                        class="text-sm text-cyan-500 hover:text-cyan-600 font-medium flex items-center">
                        <span class="material-icons text-sm mr-1">refresh</span>
                        Reset Filter
                    </button>
                </div>
            </div>

                        <!-- Search Results Info -->
            @if($query)
                <div class="mb-6">
                    <p class="text-gray-600">
                        Hasil pencarian untuk "<strong class="text-gray-900">{{ $query }}</strong>":
                        <span class="font-semibold text-cyan-600">{{ $products->count() }} produk ditemukan</span>
                    </p>
                </div>
            @else
                <div class="mb-6">
                    <p class="text-gray-600">
                        Menampilkan semua produk:
                        <span class="font-semibold text-cyan-600">{{ $products->count() }} produk ditemukan</span>
                    </p>
                </div>
            @endif

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                @forelse($products as $product)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow product-item"
                        data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}"
                        data-price="{{ $product->final_price ?? $product->selling_price }}"
                        data-discount="{{ $product->discount_value > 0 ? 'promo' : 'no-promo' }}"
                        data-stock="{{ $product->stock }}">

                        <!-- Product Image -->
                        <div class="relative aspect-square bg-gray-50 p-3">
                            @if(isset($product->final_price) && $product->final_price < $product->selling_price)
                                @php
                                    $discountPercent = round((($product->selling_price - $product->final_price) / $product->selling_price) * 100, 0);
                                @endphp
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
                                    {{ $discountPercent }}%
                                </div>
                            @endif
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-contain" onerror="this.src='/images/no-image.png'">
                        </div>

                        <!-- Product Info -->
                        <div class="p-3 flex flex-col h-[180px]">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
                                {{ $product->name }}
                            </h3>

                            <!-- Price -->
                            <div class="mb-3">
                                @if(isset($product->final_price) && $product->final_price < $product->selling_price)
                                    <div class="flex items-baseline gap-1">
                                        <p class="text-lg font-bold text-cyan-500">
                                            Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-400 line-through">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </p>
                                @else
                                    <p class="text-lg font-bold text-cyan-500">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto flex gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                    class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                    title="Detail">
                                    <span class="material-icons text-sm">info</span>
                                </a>
                                <button onclick="addToCart({{ $product->id }})"
                                    class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                    title="Tambah ke Keranjang">
                                    <span class="material-icons text-sm">add_shopping_cart</span>
                                    <span class="hidden sm:inline">Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        @if($query)
                            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                                <span class="material-icons text-6xl text-gray-300">search_off</span>
                                <h3 class="text-xl font-semibold text-gray-900 mt-4">Tidak ada produk ditemukan</h3>
                                <p class="text-gray-600 mt-2">Coba kata kunci lain atau periksa ejaan.</p>
                                <a href="{{ route('user.products') }}"
                                    class="inline-block mt-4 px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-medium transition-colors">
                                    Lihat Semua Produk
                                </a>
                            </div>
                        @else
                            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                                <span class="material-icons text-6xl text-gray-300">search</span>
                                <h3 class="text-xl font-semibold text-gray-900 mt-4">Tidak ada produk tersedia</h3>
                                <p class="text-gray-600 mt-2">Belum ada produk yang ditambahkan.</p>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- No Results -->
            <div id="no-results" class="hidden text-center py-12">
                <img src="/images/Product_not_found.png" alt="Tidak ada produk" class="w-32 h-32 mx-auto mb-4">
                <p class="text-gray-500 mt-4 text-lg">Tidak ada produk yang sesuai</p>
                <p class="text-gray-400 text-sm mt-2">Coba ubah filter pencarian Anda</p>
            </div>
        </div>
    </div>

    <script>
const isLoggedIn = @auth true @else false @endauth;

function addToCart(id) {
    if (!isLoggedIn) {
        window.location.href = '{{ route("login") }}';
        return;
    }

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let existing = cart.find(item => item.product_id == id);
    
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ product_id: id, quantity: 1 });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update badge
    let totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
    if (window.updateCartBadge) {
        window.updateCartBadge(totalItems);
    }
    
    // Show notification
    showNotification('Produk ditambahkan ke keranjang!');
}

function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'check_circle' : 'error';
    
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
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Filter produk
function filterProducts() {
    const search = '{{ $query }}'.toLowerCase(); // Gunakan query dari backend
    const price = document.getElementById('price-filter').value;
    const promo = document.getElementById('promo-filter').value;
    const products = document.querySelectorAll('.product-item');
    const noResults = document.getElementById('no-results');
    const resultCount = document.getElementById('result-count');
    
    let visibleCount = 0;

    products.forEach(product => {
        const name = product.dataset.name;
        const priceVal = parseInt(product.dataset.price);
        const disc = product.dataset.discount;
        let show = true;

        if (search && !name.includes(search)) show = false;
        if (price) {
            if (price === '0-50000' && priceVal >= 50000) show = false;
            if (price === '50000-100000' && (priceVal < 50000 || priceVal > 100000)) show = false;
            if (price === '100000+' && priceVal <= 100000) show = false;
        }
        if (promo && promo !== disc) show = false;

        product.style.display = show ? 'block' : 'none';
        if (show) visibleCount++;
    });

    // Update result count
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
        document.getElementById('products-grid').classList.add('hidden');
        resultCount.textContent = 'Tidak ada produk ditemukan';
    } else {
        noResults.classList.add('hidden');
        document.getElementById('products-grid').classList.remove('hidden');
        resultCount.textContent = `Menampilkan ${visibleCount} produk`;
    }
}

// Event untuk filter
document.getElementById('price-filter').addEventListener('change', filterProducts);
document.getElementById('promo-filter').addEventListener('change', filterProducts);

function resetFilters() {
    document.getElementById('price-filter').value = '';
    document.getElementById('promo-filter').value = '';
    filterProducts();
}

// Initial filter
document.addEventListener('DOMContentLoaded', filterProducts);
</script>

        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

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
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
        </style>
    @endsection
@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6 max-w-full overflow-hidden">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[80px] sm:max-w-[120px]">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[100px] sm:max-w-[160px]">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900 truncate max-w-[120px] sm:max-w-[200px]">Promo Produk</span>
            </nav>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Promo Produk</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ ucfirst(str_replace('_', ' ', $type)) }}</p>
            </div>

            @if($products->count() > 0)
                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow product-item"
                            data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}"
                            data-price="{{ $product->final_price ?? $product->selling_price }}"
                            data-category="{{ $product->category }}"
                            data-discount="{{ $product->discount_value > 0 ? 'promo' : 'no-promo' }}"
                            data-image="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                            data-stock="{{ $product->stock }}">

                            <!-- Product Image -->
                            <div class="relative aspect-square bg-gray-50 p-3">
                                @if($product->discount_value > 0)
                                    @php
                                        $discountPercent = 0;
                                        if ($product->discount_type == 'percent') {
                                            $discountPercent = $product->discount_value;
                                        } elseif ($product->discount_type == 'fixed') {
                                            $discountPercent = round(($product->discount_value / $product->selling_price) * 100, 0);
                                        }
                                    @endphp
                                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
                                        {{ number_format($discountPercent, 0) }}%
                                    </div>
                                @endif
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-contain" loading="lazy"
                                    onerror="this.src='/images/no-image.png';">
                            </div>

                            <!-- Product Info -->
                            <div class="p-3 flex flex-col h-[180px]">
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 min-h-[40px]">
                                    {{ $product->name }}
                                </h3>

                                <!-- Tambah Keterangan Promo -->
                                @if($product->promo_type == 'buy_x_get_y_free')
                                    <p class="text-xs text-green-600 mb-2">Beli {{ $product->promo_buy }} Gratis
                                        {{ $product->promo_get }}</p>
                                @elseif($product->promo_type == 'buy_x_for_y')
                                    <p class="text-xs text-green-600 mb-2">Beli {{ $product->promo_buy }} Hanya Rp
                                        {{ number_format($product->promo_get, 0, ',', '.') }}</p>
                                @endif

                                <!-- Price -->
                                <div class="mb-3">
                                    @if($product->discount_value > 0)
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
                    @endforeach
                </div>
            @else
                <div class="bg-gray-100 rounded-lg p-12 text-center">
                    <span class="material-icons text-6xl text-gray-400">shopping_cart</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Tidak ada produk untuk promo ini</h3>
                    <p class="text-gray-600 mt-2">Cek kembali nanti untuk penawaran spesial!</p>
                    <a href="{{ route('user.dashboard') }}"
                        class="mt-6 inline-block bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
const isLoggedIn = @auth true @else false @endauth;

            function addToCart(id) {
                if (!isLoggedIn) {
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                const product = document.querySelector(`.product-item[data-id="${id}"]`);
                if (!product) {
                    console.error('Product not found');
                    return;
                }

                const stock = parseInt(product.dataset.stock);
                if (stock <= 0) {
                    showNotification('Stok produk habis, tidak dapat ditambahkan ke keranjang!', 'error');
                    return;
                }

                let existing = cart.find(item => item.product_id == id);
                if (existing) {
                    existing.quantity += 1;
                } else {
                    cart.push({ product_id: id, quantity: 1 });
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                updateBadge();

                showNotification('Produk ditambahkan ke keranjang!');
            }

            function updateBadge() {
                try {
                    let totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                    if (window.updateCartBadge) {
                        window.updateCartBadge(totalItems);
                    }
                } catch (e) {
                    console.error('Error updating badge:', e);
                }
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

            document.addEventListener('DOMContentLoaded', updateBadge);
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
            }
        </style>
    @endsection
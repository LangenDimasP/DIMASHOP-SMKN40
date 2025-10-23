@extends('layouts.app')

@section('content')
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6 max-w-full overflow-hidden">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[80px] sm:max-w-[120px]">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[800px] sm:max-w-[160px]">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900 truncate max-w-[800px] sm:max-w-[200px]">{{ $product->name }}</span>
            </nav>

            <!-- Product Detail Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">
                    <!-- Product Image Section with Slider -->
                    <div class="flex flex-col">
                        @php
                            $allImages = [];
                            if ($product->image) {
                                $allImages[] = $product->image;
                            }
                            if ($product->images) {
                                $allImages = array_merge($allImages, $product->images);
                            }
                        @endphp
                        @if(count($allImages) > 0)
                            <!-- Main Slider -->
                            <div class="relative bg-gray-50 rounded-lg overflow-hidden aspect-square mb-4">
                                @if($product->discount_value > 0)
                                    @php
                                        $discountPercent = 0;
                                        if ($product->discount_type == 'percent') {
                                            $discountPercent = $product->discount_value;
                                        } elseif ($product->discount_type == 'fixed') {
                                            $discountPercent = round(($product->discount_value / $product->selling_price) * 100, 0);
                                        }
                                    @endphp
                                    <div class="absolute top-4 left-4 bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-lg z-10 flex items-center shadow-lg">
                                        <span class="material-icons text-sm mr-1">local_offer</span>
                                        {{ number_format($discountPercent, 0) }}% OFF
                                    </div>
                                @endif
                    
                                <div class="swiper productMainSwiper">
                                    <div class="swiper-wrapper">
                                        @foreach($allImages as $img)
                                            <div class="swiper-slide">
                                                <div class="w-full h-full flex items-center justify-center p-6">
                                                    <img src="{{ asset('storage/' . $img) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="w-full h-full object-contain cursor-zoom-in"
                                                         onclick="openImageModal('{{ asset('storage/' . $img) }}')">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Navigation Buttons -->
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    
                                    <!-- Pagination -->
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                    
<!-- Thumbnail Slider -->
<div class="swiper productThumbSwiper px-1">
    <div class="swiper-wrapper">
        @foreach($allImages as $img)
            <div class="swiper-slide">
                <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-cyan-500 transition-all duration-300 h-full bg-gray-50">
                    <img src="{{ asset('storage/' . $img) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-contain">
                </div>
            </div>
        @endforeach
    </div>
</div>
                        @else
                            <!-- Fallback to no image -->
                            <div class="relative bg-gray-50 rounded-lg overflow-hidden aspect-square flex items-center justify-center p-6">
                                <div class="text-center text-gray-400">
                                    <span class="material-icons text-6xl">image</span>
                                    <p class="mt-2 text-sm">No Image Available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info Section -->
                    <div class="flex flex-col">
                        <!-- Product Name -->
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                            {{ $product->name }}
                        </h1>

                        <!-- Category -->
                        @if($product->category)
                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <span class="material-icons text-sm mr-1">category</span>
                                <span>{{ $product->category }}</span>
                            </div>
                        @endif

                        <!-- Price Section -->
                        <div class="border-t border-b border-gray-200 py-4 mb-4">
                            @if($product->discount_value > 0)
                                <div class="flex items-baseline gap-3 mb-2">
                                    <p class="text-3xl font-bold text-cyan-500">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </p>
                                    <span class="bg-red-100 text-red-600 text-sm font-semibold px-2 py-1 rounded">
                                        {{ number_format($discountPercent, 0) }}%
                                    </span>
                                </div>
                                <p class="text-gray-500 text-lg line-through">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                                <p class="text-green-600 text-sm font-medium mt-1">
                                    Hemat Rp {{ number_format($product->selling_price - $product->final_price, 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-3xl font-bold text-cyan-500">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>

                        <!-- Promo Section -->
                        @if($product->promo_active && $product->promo_type)
                            @php
                                $promoDesc = '';
                                if ($product->promo_type == 'buy_x_get_y_free') {
                                    $promoDesc = "Beli {$product->promo_buy} Gratis {$product->promo_get}";
                                } elseif ($product->promo_type == 'buy_x_for_y') {
                                    $promoDesc = "Beli {$product->promo_buy} Hanya Rp " . number_format($product->promo_get, 0, ',', '.');
                                }
                            @endphp
                            <div class="bg-green-100 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <span class="material-icons text-green-600 mr-2">local_offer</span>
                                    <span class="text-green-800 font-semibold">Promo Spesial!</span>
                                </div>
                                <p class="text-green-700 mt-1">{{ $promoDesc }}</p>
                            </div>
                        @endif

                        <!-- Stock Info -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <span class="material-icons text-gray-600 mr-2">inventory_2</span>
                                    <span class="text-sm font-medium text-gray-700">Stok Tersedia</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900">{{ $product->stock }} pcs</span>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($product->description)
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="material-icons text-sm mr-2">description</span>
                                    Deskripsi Produk
                                </h2>
                                <div class="text-gray-700 leading-relaxed">
                                    @if(strlen($product->description) > 150)
                                        <p id="description-text" class="line-clamp-3">{{ $product->description }}</p>
                                        <button id="toggle-description-btn" onclick="toggleDescription()" class="mt-2 text-cyan-500 hover:text-cyan-600 font-medium transition-colors flex items-center">
                                            <span id="toggle-text">Tampilkan Detail</span>
                                            <span class="material-icons text-sm ml-1" id="toggle-icon" style="line-height:1;">expand_more</span>
                                        </button>
                                    @else
                                        <p>{{ $product->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Quantity Selector -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                            <div class="flex items-center border-2 border-gray-300 rounded-lg w-32 overflow-hidden hover:border-cyan-400 transition-colors">
                                <button 
                                    onclick="decreaseQuantity()"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors"
                                >
                                    <span class="material-icons">remove</span>
                                </button>
                                <input 
                                    type="number" 
                                    id="quantity-input"
                                    value="1" 
                                    min="1"
                                    max="{{ $product->stock }}"
                                    class="w-12 h-10 text-center border-x-2 border-gray-300 focus:outline-none font-medium"
                                >
                                <button 
                                    onclick="increaseQuantity()"
                                    class="w-10 h-10 flex items-center justify-center hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors"
                                >
                                    <span class="material-icons">add</span>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if($product->stock == 0)
                            <button 
                                class="w-full bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg flex items-center justify-center shadow-md cursor-not-allowed"
                                disabled
                            >
                                <span class="material-icons mr-2">inventory_2</span>
                                Stok Habis
                            </button>
                        @else
                            <div class="flex flex-col sm:flex-row gap-3 mt-auto">
                                <button 
                                    onclick="addToCart({{ $product->id }})"
                                    class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                >
                                    <span class="material-icons mr-2">add_shopping_cart</span>
                                    Tambah ke Keranjang
                                </button>
                                <button 
                                    onclick="buyNow({{ $product->id }})"
                                    class="flex-1 bg-white border-2 border-cyan-500 hover:bg-cyan-50 text-cyan-500 font-semibold py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                >
                                    <span class="material-icons mr-2">shopping_bag</span>
                                    Beli Sekarang
                                </button>
                            </div>
                        @endif
                        
                        <!-- Lihat Barcode Button - Always visible -->
                        <div class="mt-3">
                            <button 
                                onclick="showBarcodeModal()"
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                            >
                                <span class="material-icons mr-2">qr_code_scanner</span>
                                Lihat Barcode
                            </button>
                        </div>
                        

                        <!-- Additional Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center text-gray-600 hover:text-cyan-500 transition-colors">
                                    <span class="material-icons text-sm mr-2">local_shipping</span>
                                    <span>Pengiriman Cepat</span>
                                </div>
                                <div class="flex items-center text-gray-600 hover:text-cyan-500 transition-colors">
                                    <span class="material-icons text-sm mr-2">verified_user</span>
                                    <span>Produk Original</span>
                                </div>
                                <div class="flex items-center text-gray-600 hover:text-cyan-500 transition-colors">
                                    <span class="material-icons text-sm mr-2">sync_alt</span>
                                    <span>Tukar Jika Rusak</span>
                                </div>
                                <div class="flex items-center text-gray-600 hover:text-cyan-500 transition-colors">
                                    <span class="material-icons text-sm mr-2">support_agent</span>
                                    <span>Customer Service</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- You May Also Like Section -->
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Produk Terkait</h2>
                @php
                    $relatedInStock = $relatedProducts->filter(function($item) {
                        return $item->stock > 0;
                    });
                @endphp
                @if($relatedInStock->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedInStock as $related)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                                <a href="{{ route('products.show', $related->id) }}" class="block">
                                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4">
                                        @if($related->image)
                                            <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}" class="w-full h-full object-contain">
                                        @else
                                            <span class="material-icons text-4xl text-gray-400">image</span>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $related->name }}</h3>
                                        <div class="flex items-center justify-between">
                                            @if($related->discount_value > 0)
                                                <div class="flex flex-col">
                                                    <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($related->final_price, 0, ',', '.') }}</p>
                                                    <p class="text-xs text-gray-500 line-through">Rp {{ number_format($related->selling_price, 0, ',', '.') }}</p>
                                                </div>
                                            @else
                                                <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($related->selling_price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8 bg-white rounded-lg border border-gray-200">
                        <span class="material-icons text-4xl">inventory</span>
                        <p class="mt-2">Tidak ada produk terkait</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

        <!-- Barcode Modal -->
<div id="barcode-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto barcode-modal-content flex flex-col items-center">
        <div class="p-6 border-b border-gray-200 w-full">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">Barcode Produk</h2>
                <button onclick="closeBarcodeModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>
        <div class="p-6 text-center w-full flex flex-col items-center">
            <div class="flex justify-center w-full">
                <canvas id="barcode-canvas" class="block mx-auto max-w-full" style="max-width:260px;width:100%;"></canvas>
            </div>
            <p class="mt-4 text-sm text-gray-600 break-all w-full">Kode: {{ $product->unique_code }}</p>
        </div>
    </div>
</div>

    <!-- Image Modal -->
    <div id="image-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
            <span class="material-icons text-4xl">close</span>
        </button>
        <div class="relative max-w-6xl max-h-[90vh]" onclick="event.stopPropagation()">
            <img id="modal-image" src="" alt="Preview" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        </div>
    </div>

    <!-- Success Toast -->
    <div id="success-toast" class="hidden fixed bottom-4 right-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 max-w-sm">
        <span class="material-icons">check_circle</span>
        <div>
            <p class="font-semibold">Berhasil!</p>
            <p class="text-sm" id="toast-message">Produk ditambahkan ke keranjang</p>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="hidden fixed bottom-4 right-4 bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 max-w-sm">
        <span class="material-icons">error</span>
        <div>
            <p class="font-semibold">Gagal!</p>
            <p class="text-sm" id="error-toast-message">Terjadi kesalahan</p>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        const maxStock = {{ $product->stock }};
        const isLoggedIn = @auth true @else false @endauth;
        
        // Initialize Swiper
        document.addEventListener('DOMContentLoaded', function() {
            // Thumbnail Swiper
            const thumbSwiper = new Swiper('.productThumbSwiper', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                    },
                    640: {
                        slidesPerView: 4,
                    },
                }
            });

            // Main Swiper
            const mainSwiper = new Swiper('.productMainSwiper', {
                spaceBetween: 10,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                thumbs: {
                    swiper: thumbSwiper,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
            });
        });

        function getQuantity() {
            return parseInt(document.getElementById('quantity-input').value) || 1;
        }

        function setQuantity(value) {
            const input = document.getElementById('quantity-input');
            if (value < 1) value = 1;
            if (value > maxStock) value = maxStock;
            input.value = value;
        }

        function increaseQuantity() {
            const current = getQuantity();
            setQuantity(current + 1);
        }

        function decreaseQuantity() {
            const current = getQuantity();
            setQuantity(current - 1);
        }

        document.getElementById('quantity-input').addEventListener('change', function() {
            setQuantity(parseInt(this.value));
        });

        function showToast(message) {
            const toast = document.getElementById('success-toast');
            const messageEl = document.getElementById('toast-message');
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.style.opacity = '1';
                }, 300);
            }, 3000);
        }

        function showErrorToast(message) {
            const toast = document.getElementById('error-toast');
            const messageEl = document.getElementById('error-toast-message');
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.style.opacity = '1';
                }, 300);
            }, 3000);
        }

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

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        function addToCart(id) {
            if (!isLoggedIn) {
                // Jika belum login, redirect ke login
                window.location.href = '{{ route("login") }}';
                return;
            }

            const quantity = getQuantity();
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingItem = cart.find(item => item.product_id == id);
            let currentInCart = existingItem ? existingItem.quantity : 0;
            
            if (currentInCart + quantity > maxStock) {
                showErrorToast(`Stok maksimal ${maxStock}, sudah ada ${currentInCart} di keranjang.`);
                return;
            }
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({ product_id: id, quantity: quantity });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            
            if (window.updateCartBadge) {
                const totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                window.updateCartBadge(totalItems);
            }
            
            showToast(`${quantity} produk ditambahkan ke keranjang!`);
        }

        function showBarcodeModal() {
            document.getElementById('barcode-modal').classList.remove('hidden');
            // Generate barcode
            JsBarcode("#barcode-canvas", "{{ $product->unique_code }}", {
                format: "CODE128",
                width: 2,
                height: 100,
                displayValue: false
            });
        }

        function closeBarcodeModal() {
            document.getElementById('barcode-modal').classList.add('hidden');
        }

        function buyNow(id) {
            if (!isLoggedIn) {
                // Jika belum login, redirect ke login
                window.location.href = '{{ route("login") }}';
                return;
            }

            const quantity = getQuantity();
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingItem = cart.find(item => item.product_id == id);
            let currentInCart = existingItem ? existingItem.quantity : 0;
            
            if (currentInCart + quantity > maxStock) {
                showErrorToast(`Stok maksimal ${maxStock}, sudah ada ${currentInCart} di keranjang.`);
                return;
            }
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({ product_id: id, quantity: quantity });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            
            if (window.updateCartBadge) {
                const totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                window.updateCartBadge(totalItems);
            }
            
            window.location.href = '{{ route("user.cart") }}';
        }
                function toggleDescription() {
            const textEl = document.getElementById('description-text');
            const btnText = document.getElementById('toggle-text');
            const btnIcon = document.getElementById('toggle-icon');
            
            if (textEl.classList.contains('line-clamp-3')) {
                textEl.classList.remove('line-clamp-3');
                btnText.textContent = 'Tutup Detail';
                btnIcon.textContent = 'expand_less';
            } else {
                textEl.classList.add('line-clamp-3');
                btnText.textContent = 'Tampilkan Detail';
                btnIcon.textContent = 'expand_more';
            }
        }
    </script>

    <style>
        /* Swiper Styling */
        .swiper {
            width: 100%;
            height: 100%;
        }

        .productMainSwiper {
            height: 100%;
            border-radius: 0.5rem;
        }

    .productThumbSwiper {
        height: 80px;
        box-sizing: border-box;
        padding: 0;
        margin-top: 10px;
        margin-bottom: 10px; /* Tambah margin bawah agar tidak tertutup */
        position: relative;
        z-index: 1;
        overflow: visible; /* Pastikan tidak overflow hidden */
    }

        /* Line clamp utility */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

.productThumbSwiper {
    height: 80px;
    box-sizing: border-box;
    padding: 4px 0; /* Tambah padding vertikal */
    margin-top: 10px;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}

.productThumbSwiper .swiper-slide {
    height: 80px;
    opacity: 0.6;
    transition: all 0.3s ease;
}

.productThumbSwiper .swiper-slide > div {
    height: 100%; /* Pastikan div dalam slide full height */
}

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #06b6d4;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: #06b6d4;
            color: white;
            transform: scale(1.1);
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 16px;
            font-weight: bold;
        }

        .swiper-pagination-bullet {
            background: #d1d5db;
            opacity: 1;
            transition: all 0.3s ease;
        }

        .swiper-pagination-bullet-active {
            background: #06b6d4;
            width: 24px;
            border-radius: 4px;
        }

        /* Toast Animation */
        #success-toast, #error-toast {
            animation: slideInRight 0.4s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Image Modal */
        #image-modal img {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            animation: zoomIn 0.3s ease-out;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    .barcode-modal-content {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    #barcode-canvas {
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 260px;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 640px) {
        .barcode-modal-content {
            max-width: 95vw;
            padding: 0;
        }
        #barcode-modal .p-6 {
            padding: 1rem !important;
        }
        #barcode-modal h2 {
            font-size: 1.1rem;
        }
        #barcode-canvas {
            max-width: 260px;
            width: 100% !important;
            height: auto !important;
        }
    }

        /* Remove number input arrows */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Hover Effects */
        .cursor-zoom-in {
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .cursor-zoom-in:hover {
            transform: scale(1.05);
        }
    </style>
@endsection
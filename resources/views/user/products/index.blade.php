@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Produk</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Temukan produk yang Anda butuhkan</p>
            </div>

            <!-- Promo Slider Section -->
            @if($activePromos->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="material-icons text-cyan-500 mr-2">campaign</span>
                            Promo Spesial
                        </h2>
                    </div>

                    <div class="relative px-1 py-2 md:px-2 md:py-4">
                        <!-- Swiper untuk Promo -->
                        <div class="swiper promoSwiper">
                            <div class="swiper-wrapper">
                                @foreach($activePromos as $promo)
                                    <div class="swiper-slide">
                                        <a href="{{ route('promo-products', ['type' => $promo->type]) }}" class="block">
                                            <img src="{{ $promo->image ? asset('storage/' . $promo->image) : '/images/no-image.png' }}"
                                                alt="{{ ucfirst(str_replace('_', ' ', $promo->type)) }}"
                                                class="w-full h-48 sm:h-56 md:h-64 lg:h-80 object-cover rounded-xl md:rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300"
                                                loading="lazy" onerror="this.src='/images/no-image.png';">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Navigation Buttons -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <!-- Dots Indicator -->
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-100 rounded-lg p-6 text-center mb-6">
                    <span class="material-icons text-4xl text-gray-400">campaign</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Tidak ada promo aktif saat ini</h3>
                    <p class="text-gray-600">Cek kembali nanti untuk penawaran spesial!</p>
                </div>
            @endif

            @if(isset($flashSales) && $flashSales->count() > 0)
                <!-- Flash Sale Section -->
                <div class="bg-white rounded-lg shadow-sm border-2 border-red-500 overflow-hidden mb-6 relative"
                    id="flash-sale-section">
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="material-icons text-red-500 mr-2">flash_on</span>
                            Flash Sale
                            @if($isUpcoming)
                                <span class="ml-4 text-red-500 font-bold">Segera Dimulai! <span id="flash-countdown"></span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="p-4">
                        @if($totalFlashProducts > 5)
                            <!-- Slider untuk >5 produk -->
                            <div class="swiper flashSaleSwiper">
                                <div class="swiper-wrapper">
                                    @foreach($flashSales as $flashSale)
                                        @foreach($flashSale->products as $product)
                                            @php
                                                $finalPrice = $isUpcoming ? '???' : ($product->selling_price * (1 - $flashSale->discount_percent / 100));
                                            @endphp
                                            <div class="swiper-slide">
                                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow product-item"
                                                    data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}"
                                                    data-price="{{ $finalPrice }}" data-category="{{ $product->category }}"
                                                    data-discount="promo"
                                                    data-image="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                                    data-stock="{{ $product->stock }}">

                                                    <!-- Product Image -->
                                                    <div class="relative aspect-square bg-gray-50 p-3">
                                                        <div
                                                            class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
                                                            Flash Sale
                                                        </div>
                                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                                            alt="{{ $product->name }}" class="w-full h-full object-contain">
                                                    </div>

                                                    <!-- Product Info -->
                                                    <div class="p-3 flex flex-col h-[180px]">
                                                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 min-h-[40px]">
                                                            {{ $product->name }}
                                                        </h3>

                                                        <!-- Price -->
                                                        <div class="mb-3">
                                                            @if($isUpcoming)
                                                                <div class="flex items-baseline gap-1">
                                                                    <p class="text-lg font-bold text-red-500">Rp ???</p>
                                                                </div>
                                                                <p class="text-xs text-gray-400 line-through">Rp ???</p>
                                                                <div class="text-xs text-gray-500 mt-1">Diskon: ???%</div>
                                                            @else
                                                                <div class="flex items-baseline gap-1">
                                                                    <p class="text-lg font-bold text-red-500">
                                                                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                                                    </p>
                                                                </div>
                                                                <p class="text-xs text-gray-400 line-through">
                                                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                                                </p>
                                                                <div class="text-xs text-red-500 mt-1">Diskon:
                                                                    {{ $flashSale->discount_percent }}%</div>
                                                            @endif
                                                        </div>

                                                        <!-- Actions -->
                                                        <div class="mt-auto flex gap-2">
                                                            @if($isUpcoming)
                                                                <button disabled
                                                                    class="w-full bg-gray-300 text-gray-500 text-xs font-medium py-2 rounded-lg cursor-not-allowed flex items-center justify-center gap-1"
                                                                    title="Menunggu Flash Sale">
                                                                    <span class="material-icons text-sm">schedule</span>
                                                                    <span>Menunggu Flash Sale</span>
                                                                </button>
                                                            @else
                                                                <a href="{{ route('products.show', $product) }}"
                                                                    class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                                                    title="Detail">
                                                                    <span class="material-icons text-sm">info</span>
                                                                </a>
                                                                <button onclick="addToCart({{ $product->id }})"
                                                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                                                    title="Tambah ke Keranjang">
                                                                    <span class="material-icons text-sm">add_shopping_cart</span>
                                                                    <span class="hidden sm:inline">Keranjang</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                                <!-- Navigation Buttons -->
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        @else
                            <!-- Grid untuk <=5 produk -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                                @foreach($flashSales as $flashSale)
                                    @foreach($flashSale->products as $product)
                                        @php
                                            $finalPrice = $isUpcoming ? '???' : ($product->selling_price * (1 - $flashSale->discount_percent / 100));
                                        @endphp
                                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow product-item"
                                            data-id="{{ $product->id }}" data-name="{{ strtolower($product->name) }}"
                                            data-price="{{ $finalPrice }}" data-category="{{ $product->category }}" data-discount="promo"
                                            data-image="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                            data-stock="{{ $product->stock }}">

                                            <!-- Product Image -->
                                            <div class="relative aspect-square bg-gray-50 p-3">
                                                <div
                                                    class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
                                                    Flash Sale
                                                </div>
                                                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-contain">
                                            </div>

                                            <!-- Product Info -->
                                            <div class="p-3 flex flex-col h-[180px]">
                                                <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
                                                    {{ $product->name }}
                                                </h3>

                                                <!-- Price -->
                                                <div class="mb-3">
                                                    @if($isUpcoming)
                                                        <div class="flex items-baseline gap-1">
                                                            <p class="text-lg font-bold text-red-500">Rp ???</p>
                                                        </div>
                                                        <p class="text-xs text-gray-400 line-through">Rp ???</p>
                                                        <div class="text-xs text-gray-500 mt-1">Diskon: ???%</div>
                                                    @else
                                                        <div class="flex items-baseline gap-1">
                                                            <p class="text-lg font-bold text-red-500">
                                                                Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                        <p class="text-xs text-gray-400 line-through">
                                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                                        </p>
                                                        <div class="text-xs text-red-500 mt-1">Diskon: {{ $flashSale->discount_percent }}%</div>
                                                    @endif
                                                </div>

                                                <!-- Actions -->
                                                <div class="mt-auto flex gap-2">
                                                    @if($isUpcoming)
                                                        <button disabled
                                                            class="w-full bg-gray-300 text-gray-500 text-xs font-medium py-2 rounded-lg cursor-not-allowed flex items-center justify-center gap-1"
                                                            title="Menunggu Flash Sale">
                                                            <span class="material-icons text-sm">schedule</span>
                                                            <span>Menunggu Flash Sale</span>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('products.show', $product) }}"
                                                            class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                                            title="Detail">
                                                            <span class="material-icons text-sm">info</span>
                                                        </a>
                                                        <button onclick="addToCart({{ $product->id }})"
                                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                                            title="Tambah ke Keranjang">
                                                            <span class="material-icons text-sm">add_shopping_cart</span>
                                                            <span class="hidden sm:inline">Keranjang</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <!-- Category Slider -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <div class="swiper categorySwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <button onclick="setCategory('')"
                                    class="category-btn active px-4 py-2 bg-cyan-500 text-white rounded-lg text-sm font-medium whitespace-nowrap">
                                    Semua Kategori
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Makanan & Minuman')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Makanan & Minuman
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Susu & Produk Olahan')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Susu & Produk Olahan
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Bumbu & Sembako')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Bumbu & Sembako
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Kesehatan & Obat Ringan')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Kesehatan & Obat Ringan
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Perawatan Tubuh')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Perawatan Tubuh
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Perawatan Rumah Tangga')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Perawatan Rumah Tangga
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Perlengkapan Sekolah & Kantor')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Perlengkapan Sekolah & Kantor
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Elektronik & Aksesoris')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Elektronik & Aksesoris
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Mainan & Hobi')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Mainan & Hobi
                                </button>
                            </div>
                            <div class="swiper-slide">
                                <button onclick="setCategory('Makanan Beku & Siap Saji')"
                                    class="category-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 whitespace-nowrap">
                                    Makanan Beku & Siap Saji
                                </button>
                            </div>
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

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                @foreach($products->take(10) as $product)
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
                            <!-- Hapus kondisi stok habis -->
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}"
                                alt="{{ $product->name }}" class="w-full h-full object-contain">
                        </div>


                        <!-- Product Info -->
                        <div class="p-3 flex flex-col h-[180px]">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
                                {{ $product->name }}
                            </h3>

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

            <!-- Lihat Lainnya Button -->
            <div class="flex justify-end mt-6">
                <a href="{{ route('search') }}"
                    class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg font-medium">
                    Lihat Lainnya
                </a>
            </div>
..
            <!-- No Results -->
            <div id="no-results" class="hidden text-center py-12">
                <img src="/images/Product_not_found.png" alt="Tidak ada produk" class="w-32 h-32 mx-auto mb-4">
                <p class="text-gray-500 mt-4 text-lg">Tidak ada produk yang sesuai</p>
                <p class="text-gray-400 text-sm mt-2">Coba ubah filter pencarian Anda</p>
            </div>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let selectedCategory = ''; // Variabel untuk menyimpan kategori yang dipilih
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }}; // Cek status login

        // Initialize Swiper for categories
        document.addEventListener('DOMContentLoaded', function() {
            const categorySwiper = new Swiper('.categorySwiper', {
                slidesPerView: 'auto',
                spaceBetween: 8,
                freeMode: true,
                grabCursor: true,
                mousewheel: {
                    forceToAxis: true,
                },
                scrollbar: {
                    el: '.swiper-scrollbar',
                    hide: true,
                },
            });

            // Initialize Swiper for Promo
            @if($activePromos->count() > 0)
                const promoSwiper = new Swiper('.promoSwiper', {
                    slidesPerView: 1,
                    spaceBetween: 10,
                    loop: true,
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                    },
                });
            @endif

            @if(isset($flashSales) && $flashSales->count() > 0)
                @php
                    $totalFlashProducts = 0;
                    foreach($flashSales as $flashSale) {
                        $totalFlashProducts += $flashSale->products->count();
                    }
                @endphp
                @if($totalFlashProducts > 5)
                    // Initialize Swiper for Flash Sale
                    const flashSaleSwiper = new Swiper('.flashSaleSwiper', {
                        slidesPerView: 'auto',
                        spaceBetween: 12,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 3,
                            },
                            768: {
                                slidesPerView: 4,
                            },
                            1024: {
                                slidesPerView: 5,
                            },
                        },
                    });
                @endif
            @endif
        });

        function setCategory(category) {
            selectedCategory = category; // Set kategori yang dipilih
            // Update button active state
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-cyan-500', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            event.target.classList.add('active', 'bg-cyan-500', 'text-white');
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            filterProducts();
        }

        function addToCart(id) {
            if (!isLoggedIn) {
                // Jika belum login, redirect ke login
                window.location.href = '{{ route("login") }}';
                return;
            }

            // Cari produk berdasarkan id
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

            // Show success notification
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

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Filter produk berdasarkan kategori saja
        function filterProducts() {
            const category = selectedCategory;
            const products = document.querySelectorAll('#products-grid .product-item');
            const noResults = document.getElementById('no-results');
            const resultCount = document.getElementById('result-count');

            let visibleCount = 0;

            products.forEach(product => {
                const cat = product.dataset.category;
                let show = true;

                if (category && category !== cat) show = false;

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

        function resetFilters() {
            selectedCategory = '';
            // Reset category buttons
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-cyan-500', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            document.querySelector('.category-btn').classList.add('active', 'bg-cyan-500', 'text-white');
            document.querySelector('.category-btn').classList.remove('bg-gray-200', 'text-gray-700');
            filterProducts();
        }
    </script>
    <script>
        @if(isset($countdownTime))
        // Countdown Flash Sale
        function startFlashCountdown() {
            const countdownEl = document.getElementById('flash-countdown');
            const overlayEl = document.getElementById('flash-countdown-overlay');
            const targetTime = new Date("{{ $countdownTime }}").getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                let distance = targetTime - now;

                if (distance < 0) {
                    if (countdownEl) countdownEl.textContent = "Flash Sale Dimulai!";
                    if (overlayEl) overlayEl.textContent = "Flash Sale Dimulai!";
                    setTimeout(() => location.reload(), 1000);
                    return;
                }

                const hours = Math.floor((distance / (1000 * 60 * 60)));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const text = `${hours}j ${minutes}m ${seconds}d`;
                if (countdownEl) countdownEl.textContent = text;
                if (overlayEl) overlayEl.textContent = text;
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
        document.addEventListener('DOMContentLoaded', startFlashCountdown);
        @endif
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

        .categorySwiper .swiper-slide {
            width: auto;
        }

        .categorySwiper {
            cursor: grab;
        }

        .categorySwiper:active {
            cursor: grabbing;
        }

        .categorySwiper .swiper-wrapper {
            transition-timing-function: linear;
        }

        .category-btn.active {
            background-color: #06b6d4 !important;
            color: white !important;
        }

        .flashSaleSwiper .swiper-slide {
            width: 200px;
            /* Lebar slide */
        }

        .flashSaleSwiper .swiper-button-next,
        .flashSaleSwiper .swiper-button-prev {
            color: #dc2626;
            /* red-600 */
        }

        .flashSaleSwiper .swiper-button-next::after,
        .flashSaleSwiper .swiper-button-prev::after {
            font-size: 20px;
        }

        /* Hover effect hanya di desktop */
        @media (min-width: 768px) {
            #promo-slider img:hover {
                transform: scale(1.02);
            }
        }

        /* Hide navigation buttons by default, show on hover */
        #promo-slider-container button {
            transition: opacity 0.3s ease;
        }

        /* Mobile: Show buttons always with opacity, desktop: show on hover */
        @media (max-width: 768px) {
            #promo-slider-container button {
                opacity: 0.8 !important;
            }

            #promo-slider-container button:active {
                opacity: 1 !important;
            }
        }

        /* Dots styling */
        .dot {
            cursor: pointer;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .dot.bg-opacity-100 {
            opacity: 1;
            background-color: #06b6d4;
            /* cyan-500 */
        }

        /* Dots responsive width */
        @media (min-width: 768px) {
            .dot.bg-opacity-100 {
                width: 1.5rem;
                /* 24px */
            }
        }

        @media (max-width: 767px) {
            .dot.bg-opacity-100 {
                width: 1rem;
                /* 16px */
            }
        }

        .dot.bg-opacity-50 {
            opacity: 0.7;
            background-color: white;
        }

        /* Shadow responsive */
        .shadow-lg {
            box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.1), 0 4px 6px -3px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 768px) {
            .shadow-lg {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            }
        }

        .hover\:shadow-xl:hover {
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.15), 0 10px 15px -7px rgba(0, 0, 0, 0.1);
        }

        /* Smooth transitions */
        #promo-slider {
            transition: transform 0.7s ease-in-out;
        }

        /* Dots styling */
        .dot-mobile,
        .dot-desktop {
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .dot-mobile.bg-opacity-100,
        .dot-desktop.bg-opacity-100 {
            opacity: 1;
        }

        .dot-mobile.bg-opacity-50,
        .dot-desktop.bg-opacity-50 {
            opacity: 0.5;
            background-color: white;
        }

        /* Smooth transitions */
        #promo-slider-mobile,
        #promo-slider-desktop {
            transition: transform 0.7s ease-in-out;
        }

        /* Mobile slider width */
        #promo-slider-mobile {
            width: calc(100% * {{ $activePromos->count() }});
        }

        /* Desktop slider width */
        #promo-slider-desktop {
            width: calc(100% * {{ ceil($activePromos->count() / 2) }});
        }

        /* Hover effect hanya di desktop */
        @media (min-width: 768px) {
            #promo-slider-desktop img:hover {
                transform: scale(1.02);
            }
        }

        /* Swiper custom styles for promo */
        .promoSwiper .swiper-button-next,
        .promoSwiper .swiper-button-prev {
            color: #06b6d4;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-top: -20px;
        }

        .promoSwiper .swiper-button-next::after,
        .promoSwiper .swiper-button-prev::after {
            font-size: 18px;
        }

        .promoSwiper .swiper-pagination-bullet {
            background: white;
            opacity: 0.7;
        }

        .promoSwiper .swiper-pagination-bullet-active {
            background: #06b6d4;
            opacity: 1;
        }
    </style>
@endsection
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dimashop - Toko Online Terpercaya</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-cyan-600">Dimashop</h1>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('products') }}" class="text-gray-700 hover:text-cyan-600 text-sm font-medium transition-colors">Produk</a>
                    <a href="#categories" class="text-gray-700 hover:text-cyan-600 text-sm font-medium transition-colors">Kategori</a>
                    <a href="#" class="text-gray-700 hover:text-cyan-600 text-sm font-medium transition-colors">Promo</a>
                </nav>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="flex items-center text-gray-700 hover:text-cyan-600 text-sm font-medium transition-colors">
                            <span class="material-icons text-xl mr-1">person</span>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-cyan-600 text-sm font-medium transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm font-medium transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-cyan-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-3xl">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Belanja Kebutuhan Sehari-hari dengan Mudah</h2>
                <p class="text-lg md:text-xl text-cyan-50 mb-8">Temukan produk berkualitas dengan harga terbaik hanya di Dimashop</p>
                <a href="{{ route('products') }}" class="inline-flex items-center bg-white text-cyan-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Jelajahi Produk
                    <span class="material-icons ml-2">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Kategori Section -->
    <section id="categories" class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900">Kategori Produk</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($categories as $category)
                    <a href="#" class="group">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:border-cyan-600 hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-cyan-100 rounded-lg mb-4 flex items-center justify-center group-hover:bg-cyan-600 transition-colors">
                                <span class="text-cyan-600 text-xl font-bold group-hover:text-white">{{ strtoupper(substr($category, 0, 1)) }}</span>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-cyan-600 transition-colors">{{ ucfirst($category) }}</h4>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Produk Terlaris Section -->
    <section class="py-12 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900">Produk Terlaris</h3>
                <a href="{{ route('products') }}" class="text-cyan-600 hover:text-cyan-700 font-medium text-sm flex items-center">
                    Lihat Semua
                    <span class="material-icons text-lg ml-1">arrow_forward</span>
                </a>
            </div>
            @if($bestSellingProducts->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($bestSellingProducts as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow group">
                            <div class="relative aspect-square bg-gray-100">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                                @if($product->discount_value > 0)
                                    @php
                                        $discountPercent = 0;
                                        if ($product->discount_type == 'percent') {
                                            $discountPercent = $product->discount_value;
                                        } elseif ($product->discount_type == 'fixed') {
                                            $discountPercent = round(($product->discount_value / $product->selling_price) * 100, 0);
                                        }
                                    @endphp
                                    <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                        -{{ $discountPercent }}%
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2 group-hover:text-cyan-600 transition-colors">{{ $product->name }}</h4>
                                <div class="mb-3">
                                    @if($product->discount_value > 0)
                                        <div class="flex items-center space-x-2">
                                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->final_price ?? ($product->selling_price - $product->discount_value), 0, ',', '.') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @else
                                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <button class="w-full bg-cyan-600 text-white py-2 rounded-lg hover:bg-cyan-700 transition-colors text-sm font-medium flex items-center justify-center">
                                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                                    Beli Sekarang
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <span class="material-icons text-gray-300 text-6xl mb-4">inventory_2</span>
                    <p class="text-gray-500 text-lg">Belum ada produk terlaris saat ini.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Kenapa Pilih Kami Section -->
    <section class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-2xl md:text-3xl font-bold text-center mb-12 text-gray-900">Kenapa Pilih Dimashop?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="material-icons text-cyan-600 text-3xl">verified</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Kualitas Terjamin</h4>
                    <p class="text-gray-600 text-sm">Produk berkualitas tinggi dengan garansi kepuasan pelanggan</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="material-icons text-cyan-600 text-3xl">local_shipping</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Pengiriman Cepat</h4>
                    <p class="text-gray-600 text-sm">Layanan pengiriman cepat dan aman ke seluruh Indonesia</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="material-icons text-cyan-600 text-3xl">price_check</span>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Harga Terbaik</h4>
                    <p class="text-gray-600 text-sm">Harga kompetitif dengan berbagai promo menarik setiap hari</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 md:py-20 bg-cyan-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl md:text-3xl font-bold mb-4">Siap Berbelanja di Dimashop?</h3>
            <p class="text-lg text-cyan-50 mb-8">Daftar sekarang dan nikmati pengalaman belanja online yang mudah dan menyenangkan</p>
            <a href="{{ route('register') }}" class="inline-flex items-center bg-white text-cyan-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                Mulai Belanja
                <span class="material-icons ml-2">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="text-white text-lg font-bold mb-4">Dimashop</h4>
                    <p class="text-sm">Toko online terpercaya untuk kebutuhan sehari-hari Anda.</p>
                </div>
                <div>
                    <h4 class="text-white text-sm font-semibold mb-4">Belanja</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('products') }}" class="hover:text-white transition-colors">Semua Produk</a></li>
                        <li><a href="#categories" class="hover:text-white transition-colors">Kategori</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Promo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-sm font-semibold mb-4">Akun</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pesanan Saya</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-sm font-semibold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <span class="material-icons text-lg mr-2">email</span>
                            info@dimashop.com
                        </li>
                        <li class="flex items-center">
                            <span class="material-icons text-lg mr-2">phone</span>
                            +62 123 456 789
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; 2025 Dimashop. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>
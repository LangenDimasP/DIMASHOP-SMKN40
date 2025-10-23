@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Pelanggan</h1>
            <p class="text-gray-600 mt-1 text-sm sm:text-base">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Donut Chart: 5 Barang Paling Sering Dibeli -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col items-center h-[220px] overflow-hidden">
                        <h3 class="text-md font-semibold text-gray-900 mb-4">5 Barang Paling Sering Dibeli</h3>
                        <div class="w-full flex-1 flex items-center justify-center overflow-hidden">
                            <canvas id="donutChart" class="w-full h-full block"></canvas>
                        </div>
                    </div>
                
                    <!-- Line Chart: Pengeluaran Harian (7 Hari Terakhir) -->
                    <div>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col items-center h-[220px] overflow-hidden">
                            <h3 class="text-md font-semibold text-gray-900 mb-4">Pengeluaran Harian (7 Hari Terakhir)</h3>
                            <div class="w-full flex-1 flex items-center justify-center overflow-hidden">
                                <canvas id="lineChart" class="w-full h-full block"></canvas>
                            </div>
                        </div>
                        <div class="w-full flex items-center justify-center">
                            <div class="mt-2 text-sm text-gray-600 text-center">
                                @if($spendingComparison)
                                    <p>{{ $spendingComparison }}</p>
                                @else
                                    <p>Belum ada data pengeluaran.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        
        <!-- Section Penawaran Terbaik -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Penawaran Terbaik</h2>
            
            <!-- Skeleton Loading -->
            <div id="best-offers-skeleton" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                @for($i = 0; $i < 8; $i++)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="aspect-square bg-gray-200 animate-pulse"></div>
                        <div class="p-3 space-y-2">
                            <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                            <div class="h-6 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                            <div class="flex gap-2">
                                <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                                <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Actual Products -->
            <div id="best-offers-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 hidden">
                @foreach($bestOffers as $product)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
                            <img 
                                src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}" 
                                alt="{{ $product->name }}"
                                class="w-full h-full object-contain"
                                loading="lazy"
                            >
                        </div>
                        <div class="p-3 flex flex-col h-[180px]">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">{{ $product->name }}</h3>
                            <div class="mb-3">
                                @if($product->discount_value > 0)
                                    <div class="flex items-baseline gap-1">
                                        <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->final_price, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400 line-through">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <div class="mt-auto flex gap-2">
                                <a 
                                    href="{{ route('user.products.show', $product) }}"
                                    class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                    title="Detail"
                                >
                                    <span class="material-icons text-sm">info</span>
                                </a>
                                <button 
                                    onclick="addToCart({{ $product->id }})"
                                    class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                    title="Tambah ke Keranjang"
                                >
                                    <span class="material-icons text-sm">add_shopping_cart</span>
                                    <span class="hidden sm:inline">Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Section Produk Terpopuler Berdasarkan Kategori -->
        @if($popularCategory)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Beli Produk dengan Kategori {{ $popularCategory }}</h2>
                
                <!-- Skeleton Loading -->
                <div id="popular-skeleton" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @for($i = 0; $i < 8; $i++)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="aspect-square bg-gray-200 animate-pulse"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                                <div class="h-6 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                                <div class="flex gap-2">
                                    <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Actual Products -->
                <div id="popular-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 hidden">
                    @foreach($popularProducts as $product)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
                                <img 
                                    src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-contain"
                                    loading="lazy"
                                >
                            </div>
                            <div class="p-3 flex flex-col h-[180px]">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">{{ $product->name }}</h3>
                                <div class="mb-3">
                                    @if($product->discount_value > 0)
                                        <div class="flex items-baseline gap-1">
                                            <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->final_price, 0, ',', '.') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @else
                                        <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="mt-auto flex gap-2">
                                    <a 
                                        href="{{ route('user.products.show', $product) }}"
                                        class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                        title="Detail"
                                    >
                                        <span class="material-icons text-sm">info</span>
                                    </a>
                                    <button 
                                        onclick="addToCart({{ $product->id }})"
                                        class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                        title="Tambah ke Keranjang"
                                    >
                                        <span class="material-icons text-sm">add_shopping_cart</span>
                                        <span class="hidden sm:inline">Keranjang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Section Baru Saja Ditambahkan -->
        @if($newProducts->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Baru Saja Ditambahkan</h2>
                
                <!-- Skeleton Loading -->
                <div id="new-products-skeleton" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @for($i = 0; $i < 8; $i++)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="aspect-square bg-gray-200 animate-pulse"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                                <div class="h-6 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                                <div class="flex gap-2">
                                    <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="flex-1 h-8 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- Actual Products -->
                <div id="new-products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 hidden">
                    @foreach($newProducts as $product)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
                                <img 
                                    src="{{ $product->image ? asset('storage/' . $product->image) : '/images/no-image.png' }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-contain"
                                    loading="lazy"
                                >
                            </div>
                            <div class="p-3 flex flex-col h-[180px]">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">{{ $product->name }}</h3>
                                <div class="mb-3">
                                    @if($product->discount_value > 0)
                                        <div class="flex items-baseline gap-1">
                                            <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->final_price, 0, ',', '.') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @else
                                        <p class="text-lg font-bold text-cyan-500">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="mt-auto flex gap-2">
                                    <a 
                                        href="{{ route('user.products.show', $product) }}"
                                        class="flex-1 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium py-2 rounded-lg transition-colors text-center flex items-center justify-center"
                                        title="Detail"
                                    >
                                        <span class="material-icons text-sm">info</span>
                                    </a>
                                    <button 
                                        onclick="addToCart({{ $product->id }})"
                                        class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium py-2 rounded-lg transition-colors flex items-center justify-center gap-1"
                                        title="Tambah ke Keranjang"
                                    >
                                        <span class="material-icons text-sm">add_shopping_cart</span>
                                        <span class="hidden sm:inline">Keranjang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    #donutChart, #lineChart {
    width: 100% !important;
    height: 100% !important;
    max-width: 100%;
    max-height: 100%;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    padding: 8px;
    display: block;
    object-fit: contain;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Simulate loading time and show content
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate loading for promo slider
        setTimeout(() => {
            const promoSkeleton = document.getElementById('promo-skeleton');
            const promoSlider = document.getElementById('promo-slider-container');
            if (promoSkeleton && promoSlider) {
                promoSkeleton.classList.add('hidden');
                promoSlider.classList.remove('hidden');
                initializeSlider();
            }
        }, 500);

        // Simulate loading for best offers
        setTimeout(() => {
            const skeleton = document.getElementById('best-offers-skeleton');
            const grid = document.getElementById('best-offers-grid');
            if (skeleton && grid) {
                skeleton.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        }, 800);

        // Simulate loading for popular products
        setTimeout(() => {
            const skeleton = document.getElementById('popular-skeleton');
            const grid = document.getElementById('popular-grid');
            if (skeleton && grid) {
                skeleton.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        }, 1100);

        // Simulate loading for new products
        setTimeout(() => {
            const skeleton = document.getElementById('new-products-skeleton');
            const grid = document.getElementById('new-products-grid');
            if (skeleton && grid) {
                skeleton.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        }, 1400);

        updateBadge();
    });

    function addToCart(id) {
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

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-slide-up';
        notification.innerHTML = `
            <span class="material-icons text-sm">check_circle</span>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

        // Donut Chart Data
    const donutData = @json($topProducts);
    const donutLabels = donutData.map(item => item.name);
    const donutValues = donutData.map(item => item.count);

    // Line Chart Data
    const lineData = @json($dailySpending);
    const lineLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    const lineValues = lineLabels.map(day => lineData[day] || 0);

    // Donut Chart
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutValues,
                backgroundColor: [
                    '#06b6d4', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6'
                ],
                borderWidth: 2,
                hoverOffset: 8,
                borderColor: '#fff',
                cutout: '70%',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed}x dibeli`;
                        }
                    }
                }
            }
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: 'Pengeluaran (Rp)',
                data: lineValues,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.15)',
                tension: 0.45,
                fill: true,
                pointBackgroundColor: '#06b6d4',
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: { size: 11 }
                    },
                    grid: { color: '#e5e7eb' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>

@endsection
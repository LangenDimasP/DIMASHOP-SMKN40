@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6 max-w-full overflow-hidden">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[80px] sm:max-w-[120px]">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[100px] sm:max-w-[160px]">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900 truncate max-w-[120px] sm:max-w-[200px]">Redeem Voucher</span>
            </nav>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">confirmation_number</span>
                    Redeem Voucher
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Tukarkan kode atau points Anda dengan voucher diskon</p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
                    <span class="material-icons text-green-500 mr-3 flex-shrink-0">check_circle</span>
                    <div class="flex-1">
                        <p class="font-semibold text-green-800">Berhasil!</p>
                        <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <span class="material-icons text-red-500 mr-3 flex-shrink-0">error</span>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800">Terjadi Kesalahan</p>
                            <ul class="text-red-700 text-sm mt-2 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Member Points Card -->
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-sm p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-opacity-90 text-sm mb-1">Total Member Points Anda</p>
                        <p class="text-3xl font-bold">{{ number_format(auth()->user()->points, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="material-icons text-white text-4xl">stars</span>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <nav class="flex border-b border-gray-200">
                    <button 
                        id="tab-kode" 
                        onclick="showTab('kode')"
                        class="tab-button flex-1 px-6 py-4 text-sm sm:text-base font-semibold text-white bg-cyan-500 border-b-2 border-cyan-500 transition-colors flex items-center justify-center"
                    >
                        <span class="material-icons text-sm sm:text-base mr-2">qr_code</span>
                        <span>Redeem Kode</span>
                    </button>
                    <button 
                        id="tab-points" 
                        onclick="showTab('points')"
                        class="tab-button flex-1 px-6 py-4 text-sm sm:text-base font-semibold text-gray-600 hover:bg-gray-50 border-b-2 border-transparent transition-colors flex items-center justify-center"
                    >
                        <span class="material-icons text-sm sm:text-base mr-2">stars</span>
                        <span>Tukar Points</span>
                    </button>
                </nav>

                <!-- Tab Content: Redeem Kode Voucher -->
                <div id="content-kode" class="tab-content p-4 sm:p-6">
                    <div class="mb-6">
                        <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <span class="material-icons text-blue-500 flex-shrink-0">info</span>
                            <div>
                                <p class="text-sm text-blue-900 font-medium mb-1">Cara Redeem Voucher</p>
                                <p class="text-sm text-blue-700">Masukkan kode voucher yang Anda miliki untuk menukarkannya dengan diskon.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user.submit-redeem') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <span class="material-icons text-sm mr-1">confirmation_number</span>
                                Kode Voucher
                            </label>
                            <div class="relative">
                                <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                                <input 
                                    type="text" 
                                    name="code" 
                                    id="code" 
                                    placeholder="Contoh: VOUCHER123" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent uppercase"
                                    required
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Kode voucher tidak case-sensitive</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button 
                                type="submit" 
                                class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                            >
                                <span class="material-icons text-sm mr-2">redeem</span>
                                Redeem Voucher
                            </button>
                            <button 
                                type="button"
                                onclick="document.getElementById('code').value = ''"
                                class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                            >
                                <span class="material-icons text-sm mr-2">refresh</span>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab Content: Tukar Points ke Voucher -->
                <div id="content-points" class="tab-content p-4 sm:p-6 hidden">
                    <div class="mb-6">
                        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <span class="material-icons text-yellow-500 flex-shrink-0">stars</span>
                            <div>
                                <p class="text-sm text-yellow-900 font-medium mb-1">Tukar Points dengan Voucher</p>
                                <p class="text-sm text-yellow-700">
                                    Points Anda: <strong>{{ number_format(auth()->user()->points, 0, ',', '.') }}</strong> • 
                                    Pilih voucher yang ingin Anda tukarkan
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user.redeem-points-for-voucher') }}" class="space-y-6">
                        @csrf
                        
                        @if($redeemableVouchers->count() > 0)
                            <!-- Voucher Selection -->
                            <div>
                                <label for="voucher_id" class="block text-sm font-medium text-gray-700 mb-3">Pilih Voucher</label>
                                <div class="space-y-3">
                                    @foreach($redeemableVouchers as $voucher)
                                        <label class="voucher-option cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="voucher_id" 
                                                value="{{ $voucher->id }}" 
                                                data-points="{{ $voucher->points_required }}"
                                                class="hidden voucher-radio"
                                                required
                                            >
                                            <div class="voucher-card border-2 border-gray-200 rounded-lg p-4 hover:border-cyan-500 transition-colors">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <span class="material-icons text-cyan-500 text-sm">confirmation_number</span>
                                                            <h3 class="font-semibold text-gray-900">{{ $voucher->name }}</h3>
                                                        </div>
                                                        <p class="text-sm text-gray-600 mb-3">{{ $voucher->description }}</p>
                                                        <div class="flex flex-wrap gap-3 text-xs">
                                                            <span class="bg-cyan-100 text-cyan-700 px-2 py-1 rounded flex items-center">
                                                                <span class="material-icons text-xs mr-1">discount</span>
                                                                {{ $voucher->discount_type == 'percent' ? $voucher->discount_value . '%' : 'Rp ' . number_format($voucher->discount_value, 0, ',', '.') }}
                                                            </span>
                                                            @if($voucher->min_order)
                                                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded flex items-center">
                                                                    <span class="material-icons text-xs mr-1">shopping_cart</span>
                                                                    Min. Rp {{ number_format($voucher->min_order, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="bg-yellow-100 text-yellow-700 px-3 py-2 rounded-lg inline-flex items-center">
                                                            <span class="material-icons text-sm mr-1">stars</span>
                                                            <span class="font-bold">{{ number_format($voucher->points_required, 0, ',', '.') }}</span>
                                                        </div>
                                                        @if(auth()->user()->points < $voucher->points_required)
                                                            <p class="text-xs text-red-500 mt-1">Points kurang</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <button 
                                    type="submit"
                                    id="submit-points-btn"
                                    class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    disabled
                                >
                                    <span class="material-icons text-sm mr-2">swap_horiz</span>
                                    Tukar Points
                                </button>
                                <button 
                                    type="button"
                                    onclick="clearSelection()"
                                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <span class="material-icons text-sm mr-2">refresh</span>
                                    Reset
                                </button>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <span class="material-icons text-gray-300 text-6xl">inventory_2</span>
                                <p class="text-gray-500 mt-4 text-lg">Tidak ada voucher tersedia</p>
                                <p class="text-gray-400 text-sm mt-2">Voucher untuk ditukar dengan points akan muncul di sini</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function showTab(tab) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            
            // Show selected content
            document.getElementById('content-' + tab).classList.remove('hidden');
            
            // Update button styles
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('bg-cyan-500', 'text-white', 'border-cyan-500');
                btn.classList.add('text-gray-600', 'border-transparent');
            });
            
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.remove('text-gray-600', 'border-transparent');
            activeBtn.classList.add('bg-cyan-500', 'text-white', 'border-cyan-500');
        }

        // Voucher selection handling
        document.querySelectorAll('.voucher-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected state from all cards
                document.querySelectorAll('.voucher-card').forEach(card => {
                    card.classList.remove('border-cyan-500', 'bg-cyan-50');
                    card.classList.add('border-gray-200');
                });
                
                // Add selected state to chosen card
                if (this.checked) {
                    const card = this.closest('.voucher-option').querySelector('.voucher-card');
                    card.classList.remove('border-gray-200');
                    card.classList.add('border-cyan-500', 'bg-cyan-50');
                    
                    // Enable submit button
                    document.getElementById('submit-points-btn').disabled = false;
                }
            });
        });

        // Click on card to select radio
        document.querySelectorAll('.voucher-option').forEach(option => {
            option.addEventListener('click', function(e) {
                if (e.target.type !== 'radio') {
                    const radio = this.querySelector('.voucher-radio');
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        });

        function clearSelection() {
            document.querySelectorAll('.voucher-radio').forEach(radio => {
                radio.checked = false;
            });
            document.querySelectorAll('.voucher-card').forEach(card => {
                card.classList.remove('border-cyan-500', 'bg-cyan-50');
                card.classList.add('border-gray-200');
            });
            document.getElementById('submit-points-btn').disabled = true;
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    <style>
        .tab-button {
            position: relative;
        }

        .voucher-card {
            transition: all 0.3s ease;
        }

        .voucher-option:hover .voucher-card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
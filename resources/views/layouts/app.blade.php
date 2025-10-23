<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_Dimashop_tab.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Scripts -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Overlay -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"
        onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 z-50 flex flex-col">
        <!-- Fixed Header: Logo & Close Button -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <span class="bg-cyan-500 p-1 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/Dimashop_logo.png') }}" alt="Dimashop Logo"
                            class="w-8 h-8 object-contain" style="background:transparent;">
                    </span>
                    <span class="text-xl font-bold text-gray-900">Dimashop</span>
                </a>
                <button onclick="toggleSidebar()" class="p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Navigation Menu -->
        <div class="flex-1 overflow-y-auto">
            <nav class="p-4 space-y-1">
                @guest
                    <!-- Menu untuk user belum login -->
                    <a href="{{ route('products') }}"
                        class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition-colors {{ Route::is('products') ? 'bg-cyan-100 text-cyan-700' : '' }}">
                        <span class="material-icons text-xl mr-3">inventory_2</span>
                        Produk
                    </a>
                @else
                    @if(auth()->user()->hasRole('user'))
                        <!-- Balance Card -->
                        <a href="{{ route('user.topup') }}"
                            class="flex items-center justify-between px-3 py-3 bg-gradient-to-r from-cyan-50 to-cyan-100 rounded-lg mb-3 hover:from-cyan-100 hover:to-cyan-200 transition-all">
                            <div class="flex items-center space-x-2">
                                <span class="material-icons text-cyan-600">account_balance_wallet</span>
                                <span class="text-sm font-medium text-gray-700">Dimascash</span>
                            </div>
                            <span
                                class="text-sm font-bold text-cyan-700">{{ auth()->user()->dimascash_balance_formatted }}</span>
                        </a>

                        <a href="{{ route('user.dashboard') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('user.dashboard') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">dashboard</span>
                            Dashboard
                        </a>
                        <a href="{{ route('user.products') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('user.products') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">inventory_2</span>
                            Produk
                        </a>
                        <a href="{{ route('user.redeem-voucher') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('user.redeem-voucher') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">confirmation_number</span>
                            Redeem
                        </a>
                        <a href="{{ route('user.transactions') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('user.transactions') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">receipt_long</span>
                            Riwayat
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('admin'))
                        <!-- Admin Panel Separator -->
                        <div class="pt-4 pb-2">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin Panel</h3>
                            <hr class="mt-2 border-gray-300">
                        </div>

                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.dashboard') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">dashboard</span>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.users.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">people</span>
                            Users
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.products.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">inventory</span>
                            Produk
                        </a>
                        <a href="{{ route('admin.profit') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.profit') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">trending_up</span>
                            Keuntungan
                        </a>
                        <a href="{{ route('admin.vouchers.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.vouchers.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">local_offer</span>
                            Vouchers
                        </a>
                        <a href="{{ route('admin.promos.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.promos.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">campaign</span>
                            Promos
                        </a>
                        <a href="{{ route('admin.tebus-murah.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.tebus-murah.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">local_offer</span>
                            Tebus Murah
                        </a>
                        <a href="{{ route('admin.topupRequests') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.topupRequests') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">account_balance_wallet</span>
                            Topup Requests
                        </a>
                        <a href="{{ route('admin.flash-sales.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('admin.flash-sales.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">flash_on</span>
                            Flash Sales
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('kasir'))
                        <!-- Kasir Panel Separator -->
                        <div class="pt-4 pb-2">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kasir Panel</h3>
                            <hr class="mt-2 border-gray-300">
                        </div>

                        <a href="{{ route('kasir.transaksi.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('kasir.transaksi.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">point_of_sale</span>
                            Transaksi
                        </a>
                        <a href="{{ route('kasir.scan.page') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('kasir.scan.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                            <span class="material-icons text-xl mr-3">qr_code_scanner</span>
                            Scan
                        </a>
                    @endif
                @endguest

                @guest
                    <!-- Login/Register untuk guest -->
                    <div class="pt-4 mt-4 border-t border-gray-200 space-y-1">
                        <a href="{{ route('login') }}"
                            class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                            <span class="material-icons text-xl mr-3">login</span>
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex items-center px-3 py-2.5 bg-cyan-500 text-white rounded-lg text-sm font-medium hover:bg-cyan-600 transition-colors">
                            <span class="material-icons text-xl mr-3">person_add</span>
                            Register
                        </a>
                    </div>
                @endguest
            </nav>
        </div>

        <!-- Fixed Footer: User Profile & Logout -->
        @auth
            <div class="flex-shrink-0 p-4 border-t border-gray-200">
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ Route::is('profile.*') ? 'bg-cyan-100 text-cyan-700' : 'text-gray-700 hover:bg-cyan-50 hover:text-cyan-600' }}">
                        <span class="material-icons text-xl mr-3">account_circle</span>
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <span class="material-icons text-xl mr-3">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left Side: Hamburger + Logo -->
                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-shrink">
                    <button onclick="toggleSidebar()"
                        class="p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0">
                        <span class="material-icons">menu</span>
                    </button>
                    <a href="/" class="flex items-center space-x-2 min-w-0">
                        <span class="bg-cyan-500 p-1 rounded-lg flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('images/Dimashop_logo.png') }}" alt="Dimashop Logo"
                                class="w-7 h-7 sm:w-8 sm:h-8 object-contain" style="background:transparent;">
                        </span>
                        <span class="text-lg sm:text-xl font-bold text-gray-900 truncate">Dimashop</span>
                    </a>
                </div>

                <!-- Right Side: Search, Cart, Notifications -->
                <div class="flex items-center space-x-1 sm:space-x-2 flex-shrink-0">
                    @guest
                        <!-- Search Icon - Mobile Toggle -->
                        <button onclick="toggleSearch('guest')"
                            class="p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors lg:hidden">
                            <span class="material-icons">search</span>
                        </button>

                        <!-- Search Box - Desktop -->
                        <div class="hidden lg:block relative">
                            <form action="{{ route('search') }}" method="GET" class="flex items-center space-x-2">
                                <input type="text" id="search-input-guest" name="q" placeholder="Cari produk..."
                                    class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 text-sm w-40">
                                <button type="submit" class="p-1 text-gray-700 hover:bg-gray-100 rounded-lg">
                                    <span class="material-icons">search</span>
                                </button>
                            </form>
                            <div id="suggestions-guest"
                                class="absolute top-full left-0 bg-white border border-gray-300 rounded-b-lg shadow-lg z-50 hidden max-h-60 overflow-y-auto w-40">
                            </div>
                        </div>

                        <!-- Cart Icon -->
                        <a href="{{ route('login') }}"
                            class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Login untuk melihat keranjang">
                            <span class="material-icons text-xl sm:text-2xl">shopping_cart</span>
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center font-semibold">0</span>
                        </a>
                    @else
                        @if(auth()->user()->hasRole('user'))
                            <!-- Search Icon - Mobile Toggle -->
                            <button onclick="toggleSearch('user')"
                                class="p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors lg:hidden">
                                <span class="material-icons">search</span>
                            </button>

                            <!-- Search Box - Desktop -->
                            <div class="hidden lg:block relative">
                                <form action="{{ route('search') }}" method="GET" class="flex items-center space-x-2">
                                    <input type="text" id="search-input-user" name="q" placeholder="Cari produk..."
                                        class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 text-sm w-40">
                                    <button type="submit" class="p-1 text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <span class="material-icons">search</span>
                                    </button>
                                </form>
                                <div id="suggestions-user"
                                    class="absolute top-full left-0 bg-white border border-gray-300 rounded-b-lg shadow-lg z-50 hidden max-h-60 overflow-y-auto w-40">
                                </div>
                            </div>

                            <!-- Cart Icon -->
                            <a href="{{ route('user.cart') }}"
                                class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <span class="material-icons text-xl sm:text-2xl">shopping_cart</span>
                                <span id="cart-badge"
                                    class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center font-semibold">0</span>
                            </a>
                        @endif

                        @php
                            $notifs = Auth::user()->notifications()->where('read', false)->latest()->take(10)->get();
                            $notifCount = $notifs->count();
                        @endphp

                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notifBtn"
                                class="relative p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none"
                                title="Notifikasi">
                                <span class="material-icons text-xl sm:text-2xl">notifications</span>
                                @if($notifCount > 0)
                                    <span id="notif-badge"
                                        class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center font-semibold shadow">
                                        {{ $notifCount }}
                                    </span>
                                @endif
                            </button>
                            <!-- Panel Notifikasi -->
                            <div id="notifPanel"
                                class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 transition-all duration-200 animate-fade-in">
                                <div class="px-4 py-3 border-b flex items-center justify-between">
                                    <span class="font-semibold text-gray-900 text-base flex items-center">
                                        <span class="material-icons text-cyan-500 mr-2">notifications</span>
                                        Notifikasi
                                    </span>
                                    <button onclick="closeNotifPanel()"
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <span class="material-icons text-base">close</span>
                                    </button>
                                </div>
                                <ul class="max-h-80 overflow-y-auto divide-y">
                                    @forelse($notifs as $notif)
                                        <li class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 transition">
                                            <span
                                                class="mt-1 material-icons {{ $notif->type == 'success' ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $notif->type == 'success' ? 'check_circle' : 'error' }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 text-sm">
                                                    {{ $notif->title ?? 'Notifikasi' }}
                                                </div>
                                                <div class="text-gray-700 text-sm">{{ $notif->message }}</div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    {{ $notif->created_at->format('d M H:i') }}
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="px-4 py-6 text-center text-gray-400 text-sm">
                                            <span class="material-icons text-3xl mb-2 block">notifications_off</span>
                                            Tidak ada notifikasi baru
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Mobile Search Bar - Collapsible -->
        @guest
            <div id="mobile-search-guest" class="hidden lg:hidden border-t border-gray-200 px-3 py-3 animate-slide-down">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input type="text" id="search-input-guest-mobile" name="q" placeholder="Cari produk..."
                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <span class="material-icons">search</span>
                    </button>
                </form>
                <div id="suggestions-guest-mobile"
                    class="bg-white border border-gray-300 rounded-b-lg shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                </div>
            </div>
        @else
            @if(auth()->user()->hasRole('user'))
                <div id="mobile-search-user" class="hidden lg:hidden border-t border-gray-200 px-3 py-3 animate-slide-down">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="text" id="search-input-user-mobile" name="q" placeholder="Cari produk..."
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                    <div id="suggestions-user-mobile"
                        class="bg-white border border-gray-300 rounded-b-lg shadow-lg hidden max-h-60 overflow-y-auto mt-1">
                    </div>
                </div>
            @endif
        @endguest
    </nav>

    <!-- Main Content -->
    <main class="min-h-[calc(100vh-4rem)]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-2">
                    <span class="bg-cyan-500 p-1 rounded flex items-center justify-center">
                        <img src="{{ asset('images/Dimashop_logo.png') }}" alt="Dimashop Logo"
                            class="w-6 h-6 object-contain" style="background:transparent;">
                    </span>
                    <span class="text-sm text-gray-600">© 2025 Dimashop. All rights reserved.</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="#" class="text-sm text-gray-600 hover:text-cyan-500 transition-colors">Tentang Kami</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-cyan-500 transition-colors">Bantuan</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-cyan-500 transition-colors">Kebijakan
                        Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Toggle Mobile Search
        function toggleSearch(userType) {
            const searchBar = document.getElementById(`mobile-search-${userType}`);
            const searchInput = document.getElementById(`search-input-${userType}-mobile`);

            searchBar.classList.toggle('hidden');

            if (!searchBar.classList.contains('hidden')) {
                setTimeout(() => searchInput.focus(), 100);
            }
        }

        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');

            if (!sidebar.classList.contains('-translate-x-full')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Notification Panel
        document.addEventListener('DOMContentLoaded', function () {
            const notifBtn = document.getElementById('notifBtn');
            const notifPanel = document.getElementById('notifPanel');
            const notifBadge = document.getElementById('notif-badge');

            notifBtn?.addEventListener('click', function (e) {
                e.stopPropagation();
                notifPanel.classList.toggle('hidden');

                if (!notifPanel.classList.contains('hidden') && notifBadge) {
                    notifBadge.classList.add('hidden');
                    fetch('{{ route('user.notifications.read') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                }
            });

            window.closeNotifPanel = function () {
                notifPanel.classList.add('hidden');
            }

            document.addEventListener('click', function (e) {
                if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifPanel.classList.add('hidden');
                }
            });
        });

        // Cart Badge Update
        function updateCartBadge(count) {
            try {
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = count;
                    if (count === 0) {
                        badge.classList.add('hidden');
                    } else {
                        badge.classList.remove('hidden');
                    }
                }
            } catch (e) {
                console.error('Error updating cart badge:', e);
            }
        }

        // Search Suggestions
        function setupSearchSuggestions(inputId, suggestionsId) {
            const input = document.getElementById(inputId);
            const suggestions = document.getElementById(suggestionsId);

            if (!input || !suggestions) return;

            input.addEventListener('input', function () {
                const query = this.value.trim();
                if (query.length < 2) {
                    suggestions.classList.add('hidden');
                    return;
                }

                fetch(`/api/products/suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                                div.textContent = item.name;
                                div.addEventListener('click', () => {
                                    input.value = item.name;
                                    suggestions.classList.add('hidden');
                                    input.closest('form').submit();
                                });
                                suggestions.appendChild(div);
                            });
                            suggestions.classList.remove('hidden');
                        } else {
                            suggestions.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching suggestions:', error);
                        suggestions.classList.add('hidden');
                    });
            });

            input.addEventListener('blur', function () {
                setTimeout(() => suggestions.classList.add('hidden'), 150);
            });

            input.addEventListener('focus', function () {
                if (this.value.trim().length >= 2) {
                    this.dispatchEvent(new Event('input'));
                }
            });
        }

        // Setup suggestions for all search boxes
        setupSearchSuggestions('search-input-guest', 'suggestions-guest');
        setupSearchSuggestions('search-input-user', 'suggestions-user');
        setupSearchSuggestions('search-input-guest-mobile', 'suggestions-guest-mobile');
        setupSearchSuggestions('search-input-user-mobile', 'suggestions-user-mobile');

        // Load initial cart count
        try {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
            updateCartBadge(totalItems);
        } catch (e) {
            console.error('Error loading cart:', e);
            updateCartBadge(0);
        }

        window.updateCartBadge = updateCartBadge;
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                max-height: 0;
            }

            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.2s;
        }

        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }

        /* Smooth sidebar transition */
        #sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #sidebar-overlay {
            transition: opacity 0.3s ease-in-out;
        }

        /* Prevent logo squishing on mobile */
        @media (max-width: 640px) {
            nav img {
                min-width: 28px;
                min-height: 28px;
            }
        }
    </style>
</body>

</html>
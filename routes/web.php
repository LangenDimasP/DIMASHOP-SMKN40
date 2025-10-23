<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ProductPromoController;
use App\Http\Controllers\Admin\TebusMurahController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\Admin\FlashSaleController;

// Home route default
Route::get('/', function () {
    return redirect()->route('products');
})->name('home');

Route::get('/dashboard', function () {
    // Redirect sesuai role
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->hasRole('kasir')) {
        return redirect()->route('kasir.transaksi.index');
    } else {
        return redirect()->route('user.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Auth routes dari Breeze (register, login, dll.)
require __DIR__ . '/auth.php';

// Public routes (tanpa auth)
// Contoh: Halaman landing atau about
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/search', [UserController::class, 'search'])->name('search');

Route::get('/api/products/suggestions', function (Request $request) {
    $query = $request->q;
    $products = \App\Models\Product::where('name', 'like', "%{$query}%")
        ->select('name')
        ->limit(5)
        ->get();
    return response()->json($products);
});

Route::post('/kasir/parse-agent-command', [KasirController::class, 'parseAgentCommand']);

// Daftar produk untuk PO
Route::get('/products', [ProductController::class, 'indexPublic'])->name('products'); // URL: /products
Route::get('/products/{product}', [ProductController::class, 'showPublic'])->name('products.show');

        Route::get('/promo-products', [UserController::class, 'promoProducts'])->name('promo-products');

// Protected routes (butuh login)
Route::middleware('auth')->group(function () {
    // Route profile dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes (hanya admin)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard admin dengan laporan
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

                Route::resource('users', AdminUserController::class, [
        'names' => [
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ],
        'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy'],
    ]);

        Route::resource('promos', ProductPromoController::class);

        Route::resource('tebus-murah', TebusMurahController::class);

        // Resource untuk produk (CRUD)
        Route::resource('products', ProductController::class);

        Route::get('/export-sales-pdf', [AdminController::class, 'exportSalesPdf'])->name('export-sales-pdf'); // Name tanpa 'admin.' karena sudah di group

        Route::resource('vouchers', AdminController::class)->parameters(['vouchers' => 'voucher']);

        // Perbaiki ini: Ubah path dan name
        Route::get('/profit', [AdminController::class, 'profit'])->name('profit');  // URL: /admin/profit, Name: admin.profit
        Route::post('/profit', [AdminController::class, 'updateProfit'])->name('profit.update');  // URL: /admin/profit (POST), Name: admin.profit.update

        Route::get('/topup-requests', [AdminController::class, 'topupRequests'])->name('topupRequests');
        Route::post('/approve-topup/{id}', [AdminController::class, 'approveTopup'])->name('approveTopup');
        Route::post('/reject-topup/{id}', [AdminController::class, 'rejectTopup'])->name('rejectTopup');

        // Semua transaksi (lihat dari admin)
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

        Route::post('/top-up-dimascash', [AdminController::class, 'topUpDimascash'])->name('topUpDimascash');

        Route::resource('flash-sales', FlashSaleController::class)->names([
            'index' => 'flash-sales.index',
            'create' => 'flash-sales.create',
            'store' => 'flash-sales.store',
            'show' => 'flash-sales.show',
            'edit' => 'flash-sales.edit',
            'update' => 'flash-sales.update',
            'destroy' => 'flash-sales.destroy',
        ]);
    });

    Route::post('/user/notifications/read', function () {
        auth()->user()->notifications()->where('read', false)->update(['read' => true]);
        return response()->json(['success' => true]);
    })->middleware('auth')->name('user.notifications.read');

    // Kasir routes (hanya kasir)
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {
        // Halaman transaksi utama
        Route::get('/transaksi', [KasirController::class, 'index'])->name('transaksi.index');

        Route::post('/get-product-stock', [KasirController::class, 'getProductStock']);

        Route::post('/search-products', [KasirController::class, 'searchProducts'])->name('kasir.search.products');

        Route::get('/scan', [KasirController::class, 'scanPage'])->name('scan.page');

        // API untuk scan produk
        Route::post('/scan-product', [KasirController::class, 'scanProduct'])->name('scan.product');

        // Tambah route untuk get member
        Route::post('/get-member', [KasirController::class, 'getMember'])->name('get-member');

        // Scan QR/Barcode
        Route::post('/transaksi/scan', [KasirController::class, 'scan'])->name('transaksi.scan');

        // Sync offline transactions
        Route::post('/transaksi/sync-offline', [KasirController::class, 'syncOffline'])->name('transaksi.sync-offline');

        // Cetak struk
        Route::get('/transaksi/{id}/print', [KasirController::class, 'printStruk'])->name('transaksi.print');

        // Input transaksi manual (opsional)
        Route::post('/transaksi/store', [KasirController::class, 'store'])->name('transaksi.store');

        Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');

        Route::get('/kasir/transaksi/{id}/print-direct', [KasirController::class, 'printStruk']);
    });

    // User (pelanggan) routes (hanya user)
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        // Dashboard user dengan QR & riwayat
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        Route::post('/parse-shopping-command', [UserController::class, 'parseShoppingCommand'])->name('user.parseShoppingCommand');

        Route::get('/products', [ProductController::class, 'indexPublic'])->name('products'); // URL: /products
        Route::get('/products/{product}', [ProductController::class, 'showPublic'])->name('products.show');

        Route::post('/redeem-points-for-voucher', [UserController::class, 'redeemPointsForVoucher'])->name('redeem-points-for-voucher');

        Route::get('/transactions/{id}/print', [UserController::class, 'printStruk'])->name('transactions.print');

        Route::get('/products/{id}', [UserController::class, 'show'])->name('products.detail');

        Route::get('/available-vouchers', [UserController::class, 'availableVouchers'])->name('available-vouchers');

        Route::post('/apply-voucher', [UserController::class, 'applyVoucher'])->name('applyVoucher');

        Route::get('/redeem-voucher', [UserController::class, 'redeemVoucher'])->name('redeem-voucher');
        Route::post('/redeem-voucher', [UserController::class, 'submitRedeem'])->name('submit-redeem');

        // Proses order/PO
        Route::post('/order', [OrderController::class, 'store'])->name('order.store');

        Route::get('/topup', [UserController::class, 'topup'])->name('topup');
        Route::post('/topup', [UserController::class, 'submitTopup'])->name('submitTopup');

        // Keranjang
        Route::get('/cart', [UserController::class, 'cart'])->name('cart');
        Route::post('/cart/update', [UserController::class, 'updateCart'])->name('cart.update');

        // Validasi promo
        Route::post('/validate-promo', [OrderController::class, 'validatePromo'])->name('validate-promo');

        // Checkout
        Route::post('/checkout', [UserController::class, 'checkout'])->name('checkout');
        // History Transaksi
        Route::get('/transactions', [UserController::class, 'transactions'])->name('transactions');

        // Riwayat transaksi user
        Route::get('/riwayat', [UserController::class, 'riwayat'])->name('riwayat');
    });
});
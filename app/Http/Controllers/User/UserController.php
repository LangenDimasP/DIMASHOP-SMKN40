<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Notification;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\TopUpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class UserController extends Controller
{
    /**
     * Display the user's dashboard with PO history and QR code.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Cek jika po_code belum ada, generate jika perlu (misal setelah PO sukses)
        if (!$user->po_code) {
            $user->update(['po_code' => 'USER-' . \Illuminate\Support\Str::random(8)]);
        }

        // Generate QR Code sebagai HTML (langsung renderable di view)
        $qrCode = QrCode::size(200)->color(34, 190, 196)->generate($user->po_code);  // Warna cyan #42bec4

        // Generate Barcode sebagai base64 (untuk embed di img src)
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($user->po_code, $generator::TYPE_CODE_128);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        // Ambil riwayat transaksi user
        $transactions = $user->transactions()->latest()->get();  // Asumsi relasi hasMany di User model

        // Hitung kategori terpopuler berdasarkan pembelian user
        $categoryCounts = collect();
        foreach ($transactions as $transaction) {
            $items = json_decode($transaction->items, true);
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->category) {
                    $categoryCounts[$product->category] = ($categoryCounts[$product->category] ?? 0) + $item['quantity'];
                }
            }
        }
        $popularCategory = $categoryCounts->sortDesc()->keys()->first();

        // Ambil produk dari kategori terpopuler, limit 4, stock > 0, dengan sinkronisasi harga
        $popularProducts = collect();
        if ($popularCategory) {
            $popularProducts = Product::with('flashSales')->where('category', $popularCategory)
                ->where('stock', '>', 0)
                ->limit(4)
                ->get()
                ->map(function ($product) {
                    $now = now();
                    $activeFlashSale = $product->flashSales
                        ->where('active', true)
                        ->where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now)
                        ->first();
                    if ($activeFlashSale) {
                        $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                        $product->discount_value = $activeFlashSale->discount_percent;
                        $product->discount_type = 'percent';
                    } elseif ($product->discount_value > 0) {
                        if ($product->discount_type == 'percent') {
                            $product->final_price = $product->selling_price * (1 - $product->discount_value / 100);
                        } elseif ($product->discount_type == 'fixed') {
                            $product->final_price = $product->selling_price - $product->discount_value;
                        }
                    }
                    return $product;
                });
        }

        // Ambil produk dengan penawaran terbaik (diskon terbesar), limit 4, stock > 0, dengan sinkronisasi harga
        $bestOffers = Product::with('flashSales')->where('stock', '>', 0)
            ->get()
            ->map(function ($product) {
                $now = now();
                $activeFlashSale = $product->flashSales
                    ->where('active', true)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->first();
                if ($activeFlashSale) {
                    $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                    $product->discount_value = $activeFlashSale->discount_percent;
                    $product->discount_type = 'percent';
                } elseif ($product->discount_value > 0) {
                    if ($product->discount_type == 'percent') {
                        $product->final_price = $product->selling_price * (1 - $product->discount_value / 100);
                    } elseif ($product->discount_type == 'fixed') {
                        $product->final_price = $product->selling_price - $product->discount_value;
                    }
                }
                return $product;
            })
            ->filter(function ($product) {
                return isset($product->final_price) && $product->final_price < $product->selling_price;
            })
            ->sortByDesc(function ($product) {
                $discountPercent = 0;
                if ($product->discount_type == 'percent') {
                    $discountPercent = $product->discount_value;
                } elseif ($product->discount_type == 'fixed') {
                    $discountPercent = round(($product->discount_value / $product->selling_price) * 100, 0);
                }
                return $discountPercent;
            })
            ->take(4);

        // Ambil produk baru saja ditambahkan, limit 4, stock > 0, dengan sinkronisasi harga
        $newProducts = Product::with('flashSales')->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($product) {
                $now = now();
                $activeFlashSale = $product->flashSales
                    ->where('active', true)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->first();
                if ($activeFlashSale) {
                    $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                    $product->discount_value = $activeFlashSale->discount_percent;
                    $product->discount_type = 'percent';
                } elseif ($product->discount_value > 0) {
                    if ($product->discount_type == 'percent') {
                        $product->final_price = $product->selling_price * (1 - $product->discount_value / 100);
                    } elseif ($product->discount_type == 'fixed') {
                        $product->final_price = $product->selling_price - $product->discount_value;
                    }
                }
                return $product;
            });

        // Fetch promo produk aktif untuk slider (dari tabel product_promos)
        $activePromos = \App\Models\ProductPromo::where('active', true)->get();

        // Hitung 5 barang paling sering dibeli user
        $productCounts = collect();
        foreach ($transactions as $transaction) {
            $items = json_decode($transaction->items, true);
            foreach ($items as $item) {
                $productCounts[$item['product_id']] = ($productCounts[$item['product_id']] ?? 0) + $item['quantity'];
            }
        }
        $topProductIds = $productCounts->sortDesc()->take(5)->keys();
        $topProducts = Product::whereIn('id', $topProductIds)->get()->map(function ($product) use ($productCounts) {
            return [
                'name' => $product->name,
                'count' => $productCounts[$product->id]
            ];
        });

        // Hitung pengeluaran harian 7 hari terakhir
        $dailySpending = [];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $now = Carbon::now();
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayName = $days[$date->dayOfWeek];
            $spending = $user->transactions()
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_price');
            $dailySpending[$dayName] = $spending;
        }

        // Hitung perbandingan pengeluaran hari ini vs kemarin
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $todaySpending = $user->transactions()->whereDate('created_at', $today)->sum('total_price');
        $yesterdaySpending = $user->transactions()->whereDate('created_at', $yesterday)->sum('total_price');
        $spendingComparison = null;
        if ($yesterdaySpending > 0) {
            $percentage = (($todaySpending - $yesterdaySpending) / $yesterdaySpending) * 100;
            $direction = $percentage > 0 ? 'lebih banyak' : 'lebih sedikit';
            $spendingComparison = "Hari ini Anda mengeluarkan " . abs($percentage) . "% uang $direction dari kemarin.";
        } elseif ($todaySpending > 0) {
            $spendingComparison = "Hari ini Anda mulai mengeluarkan uang.";
        }

        // Return view dengan semua data, termasuk activePromos untuk slider
        return view('user.dashboard', compact('user', 'qrCode', 'barcodeBase64', 'transactions', 'popularCategory', 'popularProducts', 'bestOffers', 'newProducts', 'activePromos', 'topProducts', 'dailySpending', 'spendingComparison'));
    }



    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Ambil produk terkait berdasarkan kategori, exclude produk saat ini, limit 4
        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('user.products.show', compact('product', 'relatedProducts'));
    }

    public function profile()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->get();
        $qrCode = QrCode::size(200)->generate($user->po_code);
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($user->po_code, $generator::TYPE_CODE_128);
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcode);

        return view('user.profile', compact('user', 'transactions', 'qrCode', 'barcodeBase64'));
    }
    public function promoProducts(Request $request)
    {
        $type = $request->query('type'); // Ambil type dari query parameter (e.g., ?type=buy_x_get_y_free)

        // Validasi type
        $validTypes = ['buy_x_get_y_free', 'buy_x_for_y'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Tipe promo tidak valid.');
        }

        // Fetch produk berdasarkan promo_type yang sama, aktif, dan stock > 0, dengan eager loading flashSales
        $products = Product::with('flashSales')->where('promo_type', $type)
            ->where('promo_active', 1) // Hanya produk dengan promo aktif
            ->where('stock', '>', 0)
            ->get();

        // Set harga diskon jika produk sedang flash sale
        $now = now();
        foreach ($products as $product) {
            $flashSale = $product->flashSales
                ->where('active', true)
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->first();

            if ($flashSale) {
                $product->final_price = $product->selling_price * (1 - $flashSale->discount_percent / 100);
                $product->discount_value = $flashSale->discount_percent;
                $product->discount_type = 'percent';
            }
        }

        // Return view dengan produk
        return view('user.promo-products', compact('products', 'type'));
    }

    /**
     * Display the user's cart.
     */
    public function cart()
    {
        $products = Product::with('flashSales')->where('stock', '>', 0)->get();
        $tebusMurahList = \App\Models\TebusMurah::with('product')->where('active', 1)->get();

        // Set harga diskon jika produk sedang flash sale
        $now = now();
        foreach ($products as $product) {
            // Ambil flash sale aktif (jika ada)
            $activeFlashSale = $product->flashSales
                ->where('active', true)
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->first();

            if ($activeFlashSale) {
                $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                $product->discount_value = $activeFlashSale->discount_percent;
                $product->discount_type = 'percent';
            }
        }

        $redeemedVouchers = auth()->user()->redeemedVouchers;
        $user = auth()->user();
        $multiplier = $this->getPointsMultiplier($user->xp);

        return view('user.cart', compact('products', 'tebusMurahList', 'redeemedVouchers', 'multiplier'));
    }

    /**
     * Update cart quantity via AJAX.
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        // Untuk sekarang, return JSON success (karena cart di localStorage)
        // Jika ingin sync ke server, simpan di session atau database
        return response()->json(['success' => true, 'message' => 'Cart updated']);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|integer',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:dimascash',
            'voucher_code' => 'nullable|string',
            'points_used' => 'nullable|integer|min:0',
        ]);

        $cart = $request->cart;
        $user = auth()->user();
        $total = 0;
        $items = [];
        $discount = 0;
        $appliedVoucher = null;

        foreach ($cart as $item) {
            $product = Product::with('flashSales')->find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json(['success' => false, 'message' => 'Stok tidak cukup untuk ' . ($product->name ?? 'produk')]);
            }

            // Harga flash sale
            $now = now();
            $price = $product->final_price; // Gunakan final_price yang sudah termasuk discount_value dari produk
            if (
                $product->flashSales->isNotEmpty() &&
                $product->flashSales->first()->active &&
                $product->flashSales->first()->start_time <= $now &&
                $product->flashSales->first()->end_time >= $now
            ) {
                $price = $product->selling_price * (1 - $product->flashSales->first()->discount_percent / 100); // Override dengan harga flash sale jika aktif
            }

            // Tebus Murah
            $isTebusMurah = isset($item['is_tebus_murah']) && $item['is_tebus_murah'];
            if ($isTebusMurah && isset($item['tebus_price'])) {
                $price = $item['tebus_price'];
            }

            // Promo logic
            $promoType = $product->promo_type;
            $promoDesc = '';
            $freeItems = 0;
            $discountItem = 0;
            $itemTotal = $price * $item['quantity'];

            if ($product->promo_active && $promoType == 'buy_x_for_y') {
                $x = $product->promo_buy;
                $y = $product->promo_get;
                $fullSets = floor($item['quantity'] / $x);
                $remaining = $item['quantity'] % $x;
                $itemTotal = $fullSets * $y + $remaining * $price;
                $promoDesc = "Beli $x hanya Rp " . number_format($y, 0, ',', '.');
                $discountItem = ($price * $item['quantity']) - $itemTotal;
            } elseif ($product->promo_active && $promoType == 'buy_x_get_y_free') {
                $x = $product->promo_buy;
                $y = $product->promo_get;
                $fullSets = floor($item['quantity'] / $x);
                $freeItems = $fullSets * $y;
                $itemTotal = $price * $item['quantity'];
                $promoDesc = "Beli $x Gratis $y";
                $discountItem = $freeItems * $price;
            }

            $total += $itemTotal;
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $price,
                'original_price' => $product->selling_price,
                'promo_type' => $promoType,
                'promo_desc' => $promoDesc,
                'free_items' => $freeItems,
                'total' => $itemTotal,
                'discount' => $discountItem,
                'image' => $product->image,
                'is_tebus_murah' => $isTebusMurah,
                'tebus_price' => $isTebusMurah ? $item['tebus_price'] : null,
            ];
            $product->decrement('stock', $item['quantity']);
        }

        // Apply voucher jika ada
        if ($request->voucher_code) {
            $voucher = Voucher::active()->where('code', $request->voucher_code)->first();
            if ($voucher && (!$voucher->min_order || $total >= $voucher->min_order)) {
                if ($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit) {
                    return response()->json(['success' => false, 'message' => 'Voucher ini sudah mencapai batas penggunaan.']);
                }
                if ($voucher->discount_type == 'percent') {
                    $discount = ($total * $voucher->discount_value) / 100;
                } else {
                    $discount = min($voucher->discount_value, $total);
                }
                $appliedVoucher = $voucher;
            }
        }

        $finalTotal = $total - $discount;

        // Validasi saldo Dimascash
        if ($request->payment_method === 'dimascash') {
            if ($user->dimascash_balance < $finalTotal) {
                return response()->json(['success' => false, 'message' => 'Saldo Dimascash tidak cukup. Saldo Anda: ' . $user->dimascash_balance_formatted]);
            }
            $user->decrement('dimascash_balance', $finalTotal);
        }

        // Buat transaksi
        $pointsUsed = $request->points_used ?? 0;
        $pointsDiscount = min($pointsUsed * 10, $total);  // Hitung potongan points

        // Validasi points user
        if ($pointsUsed > $user->points) {
            return response()->json(['success' => false, 'message' => 'Points tidak cukup.']);
        }

        $finalTotal = $total - $discount - $pointsDiscount;

        // Validasi saldo Dimascash
        if ($request->payment_method === 'dimascash') {
            if ($user->dimascash_balance < $finalTotal) {
                return response()->json(['success' => false, 'message' => 'Saldo Dimascash tidak cukup. Saldo Anda: ' . $user->dimascash_balance_formatted]);
            }
            $user->decrement('dimascash_balance', $finalTotal);
        }

        // Kurangi points
        if ($pointsUsed > 0) {
            $user->decrement('points', $pointsUsed);
        }

        // Buat transaksi
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'kasir_id' => null,
            'status' => 'selesai',
            'payment_method' => $request->payment_method,
            'total_price' => $finalTotal,
            'items' => json_encode($items),
            'voucher_code' => $request->voucher_code,
            'discount_amount' => $discount,  // Hanya potongan voucher
            'points_used' => $pointsUsed,  // Potongan points terpisah
        ]);

        // Tambahkan XP dan Points berdasarkan total belanja (setelah transaksi sukses)
        $belanjaPer1000 = floor($total / 1000); // Hitung berapa kali Rp 1000 dari total belanja (sebelum diskon)
        $xpEarned = $belanjaPer1000 * 4; // 4 XP per Rp 1000 belanja

        if ($xpEarned > 0) {
            $user->increment('xp', $xpEarned);
        }

        if ($appliedVoucher) {
            $appliedVoucher->increment('usage_count');
        }

        $pointsEarned = floor($total / 1000) * $this->getPointsMultiplier($user->xp);

        if ($pointsEarned > 0) {
            $user->increment('points', $pointsEarned);
        }

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'success',
            'title' => 'Pembayaran Berhasil',
            'message' => 'Checkout berhasil dengan ' . ucfirst($request->payment_method) . '!',
        ]);

        // Tambahkan kode HTTP POST di sini
        try {
            $postData = json_encode([
                'action' => 'print_struk',
                'transaction_id' => $transaction->id,
                'items' => $items,
                'total_price' => $finalTotal,
                'user_name' => $user->name,
            ]);

            $ch = curl_init('http://192.168.0.104:3000/print');// Ganti [IP_PC_ANDA] dengan IP PC lokal (misal 192.168.1.100)
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData)
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                if ($responseData['success']) {
                    \Log::info('Print berhasil: ' . $responseData['message']);
                } else {
                    \Log::error('Print gagal: ' . $responseData['message']);
                }
            } else {
                \Log::error('HTTP error saat kirim ke aplikasi lokal: ' . $httpCode);
            }
        } catch (\Exception $e) {
            \Log::error('Gagal kirim ke aplikasi lokal: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Checkout berhasil dengan ' . ucfirst($request->payment_method) . '!']);
    }
    private function getPointsMultiplier($xp)
    {
        $levels = [
            1 => ['required' => 0, 'multiplier' => 1],
            2 => ['required' => 1500, 'multiplier' => 1.5],
            3 => ['required' => 3000, 'multiplier' => 2.5],
            4 => ['required' => 8500, 'multiplier' => 5],
        ];

        $currentLevel = 1;
        foreach ($levels as $lvl => $data) {
            if ($xp >= $data['required']) {
                $currentLevel = $lvl;
            }
        }

        return $levels[$currentLevel]['multiplier'];
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->latest()->paginate(10);
        $topupRequests = auth()->user()->topUpRequests()->latest()->paginate(10);  // Ubah kembali ke paginate
        return view('user.transactions.index', compact('transactions', 'topupRequests'));
    }
    public function topup()
    {
        return view('user.topup');
    }

    public function submitTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('proof_image')->store('topup_proofs', 'public');

        TopUpRequest::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'proof_image' => $path,
        ]);

        // Tambahkan notifikasi di sini
        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'success',
            'title' => 'Top Up Berhasil',
            'message' => 'Request top up berhasil dikirim. Tunggu konfirmasi admin.',
        ]);

        return redirect()->back()->with('success', 'Request top up berhasil dikirim. Tunggu konfirmasi admin.');
    }
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', $request->code)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher tidak valid atau sudah kadaluarsa.']);
        }

        if ($request->total < $voucher->min_order) {
            return response()->json(['success' => false, 'message' => 'Minimal pembelian Rp ' . number_format($voucher->min_order, 0, ',', '.') . ' untuk menggunakan voucher ini.']);
        }

        // Cek batas penggunaan
        if ($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Voucher ini sudah mencapai batas penggunaan.']);
        }

        $discount = 0;
        if ($voucher->discount_type == 'percent') {  // Ubah dari 'percentage' ke 'percent' untuk konsistensi
            $discount = ($request->total * $voucher->discount_value) / 100;
        } else {
            $discount = min($voucher->discount_value, $request->total);
        }

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'message' => 'Voucher berhasil diterapkan! Potongan Rp ' . number_format($discount, 0, ',', '.'),
        ]);
    }

    public function redeemVoucher()
    {
        $redeemableVouchers = Voucher::where('is_redeemable_with_points', true)->active()->get(); // Baru
        return view('user.redeem-voucher', compact('redeemableVouchers')); // Baru
    }

    public function submitRedeem(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $voucher = Voucher::where('code', $request->code)->first();
        if (!$voucher) {
            return back()->withErrors(['code' => 'Voucher tidak ditemukan.']);
        }

        if ($voucher->redeemedByUsers()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['code' => 'Voucher sudah diredeem.']);
        }

        // Cek syarat (misal min purchase, dll. - sesuaikan dengan field voucher)
        // Contoh: if ($voucher->min_purchase > auth()->user()->total_purchases) { ... }

        $voucher->redeemedByUsers()->attach(auth()->id(), ['redeemed_at' => now()]);

        return back()->with('success', 'Voucher berhasil diredeem!');
    }
    public function redeemPointsForVoucher(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $user = auth()->user();
        $voucher = Voucher::findOrFail($request->voucher_id);

        // Pastikan voucher bisa diredeem dengan points
        if (!$voucher->is_redeemable_with_points) {
            return back()->withErrors(['voucher_id' => 'Voucher ini tidak bisa diredeem dengan points.']);
        }

        // Cek points cukup
        if ($user->points < $voucher->points_required) {
            return back()->withErrors(['voucher_id' => 'Points tidak cukup. Anda butuh ' . $voucher->points_required . ' points.']);
        }

        // Kurangi points
        $user->decrement('points', $voucher->points_required);

        // Assign voucher ke user (asumsi ada relasi redeemedByUsers)
        $voucher->redeemedByUsers()->attach($user->id, ['redeemed_at' => now()]);

        // Notifikasi
        Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Voucher Berhasil Ditukar',
            'message' => 'Anda berhasil menukar ' . $voucher->points_required . ' points untuk voucher ' . $voucher->name . '. Kode: ' . $voucher->code,
        ]);

        return back()->with('success', 'Voucher berhasil ditukar! Kode voucher: ' . $voucher->code . ' (Berlaku hingga ' . ($voucher->expires_at ? $voucher->expires_at->format('d M Y') : 'tidak ada batas waktu') . ')');
    }
    public function availableVouchers()
    {
        $user = auth()->user();
        $redeemedVouchers = $user->redeemedVouchers()->where('is_active', true)->where('expires_at', '>', now())->get();
        return response()->json($redeemedVouchers);
    }

    public function printStruk($id)
    {
        try {
            $transaction = auth()->user()->transactions()->findOrFail($id);
            $rawItems = $transaction->getAttribute('items');
            $items = is_array($rawItems) ? $rawItems : json_decode($rawItems, true);

            // Deteksi OS
            if (PHP_OS_FAMILY === 'Windows') {
                // Untuk localhost Windows
                $connector = new WindowsPrintConnector("COM3");
            } else {
                // Untuk hosting (Linux/Unix), gunakan NetworkPrintConnector jika printer jaringan
                // Ganti '192.168.1.100' dengan IP printer hosting Anda
                $connector = new NetworkPrintConnector('192.168.1.100', 9100); // Port default ESC/POS
            }

            $printer = new Printer($connector);

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Dimashop\n");
            $printer->text("ID Transaksi: " . $transaction->unique_code . "\n");
            $printer->text("Tanggal: " . $transaction->created_at->format('d/m/Y H:i:s') . "\n");
            $printer->text("User: " . $transaction->user->name . "\n");
            $printer->text("Metode Pembayaran: " . ucfirst($transaction->payment_method ?? 'Dimascash') . "\n");
            $printer->text("----------------------------\n");

            // Items
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $totalHargaProduk = 0;
            foreach ($items as $item) {
                $label = '';
                if (isset($item['is_tebus_murah']) && $item['is_tebus_murah']) {
                    $label .= ' [Tebus Murah]';
                }
                if (!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_get_y_free' && !empty($item['free_items'])) {
                    $label .= ' [Gratis ' . $item['free_items'] . ' pcs]';
                }
                if (!empty($item['promo_type']) && $item['promo_type'] == 'buy_x_for_y') {
                    $label .= ' [Promo: ' . ($item['promo_desc'] ?? '') . ']';
                }

                $printer->text($item['name'] . $label . " (" . $item['quantity'] . "x)\n");
                $printer->text("Harga: Rp " . number_format($item['total'], 0, ',', '.') . "\n");

                $totalHargaProduk += $item['total'];
            }

            // Total Harga Produk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("----------------------------\n");
            $printer->text("Total: Rp " . number_format($transaction->total_price, 0, ',', '.') . "\n");
            $printer->text("Status: " . ucfirst($transaction->status) . "\n");
            $printer->text("Terima Kasih atas Pembelian Anda!\n");
            $printer->text("Selamat Berbelanja Kembali\n");

            $printer->cut();
            $printer->close();

            return redirect()->back()->with('success', 'Struk berhasil dicetak!');
        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak struk: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->query('q');
        $products = collect();
        if ($query) {
            $products = Product::with('flashSales')->where('name', 'LIKE', '%' . $query . '%')
                ->get()
                ->map(function ($product) {
                    $now = now();
                    $activeFlashSale = $product->flashSales
                        ->where('active', true)
                        ->where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now)
                        ->first();
                    if ($activeFlashSale) {
                        $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                        $product->discount_value = $activeFlashSale->discount_percent;
                        $product->discount_type = 'percent';
                    }
                    return $product;
                });
        } else {
            // Jika tidak ada query, tampilkan semua produk
            $products = Product::with('flashSales')->get()
                ->map(function ($product) {
                    $now = now();
                    $activeFlashSale = $product->flashSales
                        ->where('active', true)
                        ->where('start_time', '<=', $now)
                        ->where('end_time', '>=', $now)
                        ->first();
                    if ($activeFlashSale) {
                        $product->final_price = $product->selling_price * (1 - $activeFlashSale->discount_percent / 100);
                        $product->discount_value = $activeFlashSale->discount_percent;
                        $product->discount_type = 'percent';
                    }
                    return $product;
                });
        }
        return view('user.search', compact('products', 'query'));
    }

    public function parseShoppingCommand(Request $request)
    {
        $command = strtolower(trim($request->command));

        // Load semua produk sekali saja untuk efisiensi
        $products = Product::all();

        // === EXPANDED SYNONYMS & CATEGORIES ===
        // Kategori yang sangat lengkap dengan berbagai variasi bahasa sehari-hari
        $synonyms = [
            // Minuman - Teh
            'teh' => [
                'teh',
                'tea',
                'ngeteh',
                'minum teh',
                'ngopi teh',
                'mau teh',
                'pengen teh',
                'beli teh',
                'pesen teh',
                'order teh',
                'minuman teh'
            ],

            // Minuman - Kopi
            'kopi' => [
                'kopi',
                'coffee',
                'ngopi',
                'minum kopi',
                'mau kopi',
                'pengen kopi',
                'beli kopi',
                'pesen kopi',
                'order kopi',
                'minuman kopi',
                'kofe'
            ],

            // Minuman - Jus
            'jus' => [
                'jus',
                'juice',
                'minuman',
                'minum jus',
                'mau jus',
                'pengen jus',
                'beli jus',
                'pesen jus',
                'sari buah',
                'minuman buah',
                'fresh juice'
            ],

            // Minuman - Susu
            'susu' => [
                'susu',
                'milk',
                'minum susu',
                'mau susu',
                'pengen susu',
                'beli susu',
                'pesen susu',
                'minuman susu',
                'dairy'
            ],

            // Minuman - Air Mineral
            'air' => [
                'air',
                'aqua',
                'air mineral',
                'air putih',
                'minum air',
                'mau air',
                'pengen air',
                'beli air',
                'mineral water'
            ],

            // Minuman - Soda
            'soda' => [
                'soda',
                'soft drink',
                'minuman bersoda',
                'coca cola',
                'pepsi',
                'fanta',
                'sprite',
                'minuman berkarbonasi',
                'cola'
            ],

            // Minuman - Energi
            'energi' => [
                'energi',
                'energy drink',
                'minuman energi',
                'extra joss',
                'kratingdaeng',
                'redbull',
                'minuman penambah energi'
            ],

            // Makanan - Snack/Camilan
            'snack' => [
                'snack',
                'camilan',
                'makanan ringan',
                'cemilan',
                'makan snack',
                'mau snack',
                'pengen snack',
                'beli snack',
                'jajanan',
                'makanan kecil',
                'kudapan'
            ],

            // Makanan - Roti
            'roti' => [
                'roti',
                'bread',
                'makan roti',
                'mau roti',
                'pengen roti',
                'beli roti',
                'pesen roti',
                'sandwich',
                'burger'
            ],

            // Makanan - Mie/Pasta
            'mie' => [
                'mie',
                'mee',
                'mi',
                'indomie',
                'mie instan',
                'makan mie',
                'mau mie',
                'pengen mie',
                'beli mie',
                'noodle',
                'pasta'
            ],

            // Makanan - Biskuit
            'biskuit' => [
                'biskuit',
                'biskit',
                'cookies',
                'kue kering',
                'wafer',
                'crackers',
                'makan biskuit',
                'beli biskuit'
            ],

            // Makanan - Cokelat
            'cokelat' => [
                'cokelat',
                'coklat',
                'chocolate',
                'makan cokelat',
                'beli cokelat',
                'pengen cokelat',
                'choco',
                'kakao'
            ],

            // Makanan - Permen
            'permen' => [
                'permen',
                'candy',
                'manisan',
                'gulali',
                'lolipop',
                'beli permen',
                'makan permen'
            ],

            // Makanan - Keripik
            'keripik' => [
                'keripik',
                'chips',
                'kripik',
                'potato chips',
                'chipsy',
                'keripik kentang',
                'makan keripik',
                'beli keripik'
            ],

            // Makanan Berat
            'makanan' => [
                'makanan',
                'makan',
                'food',
                'lapar',
                'pengen makan',
                'mau makan',
                'beli makanan',
                'pesen makanan'
            ],

            // Produk Dairy Lainnya
            'yogurt' => [
                'yogurt',
                'yoghurt',
                'yakult',
                'minuman probiotik',
                'dairy drink',
                'minum yogurt'
            ],

            // Es Krim
            'es krim' => [
                'es krim',
                'ice cream',
                'eskrim',
                'es',
                'makan es krim',
                'beli es krim',
                'pengen es krim'
            ]
        ];

        // === CATEGORY MAPPINGS ===
        // Mapping kategori produk ke kategori utama (untuk fleksibilitas)
        $categoryMappings = [
            'snack' => ['makanan ringan', 'camilan', 'jajanan', 'kudapan', 'cemilan'],
            'teh' => ['minuman teh', 'tea'],
            'kopi' => ['minuman kopi', 'coffee'],
            'jus' => ['minuman buah', 'juice'],
            'susu' => ['dairy', 'minuman susu'],
            'air' => ['mineral water', 'aqua'],
            'soda' => ['soft drink', 'cola'],
            'energi' => ['energy drink'],
            'roti' => ['bread'],
            'mie' => ['noodle', 'pasta'],
            'biskuit' => ['cookies', 'kue kering'],
            'cokelat' => ['chocolate'],
            'permen' => ['candy'],
            'keripik' => ['chips'],
            'makanan' => ['food'],
            'yogurt' => ['dairy drink'],
            'es krim' => ['ice cream']
        ];

        // === DETECT INTENT ===
        // Jika command seperti pertanyaan ("ada gak", "apa ada"), return list produk, bukan tambah ke cart
        $isQuestion = preg_match('/(ada gak|ada tidak|apa ada|ada kah|ada ya|ada dong)/i', $command);

        // === PARSE QUANTITY FROM COMMAND ===
        $quantities = $this->extractQuantities($command);

        // === TOKENIZE & CLEAN COMMAND ===
        // Hapus kata-kata umum yang tidak berguna (stop words)
        $stopWords = [
            'saya',
            'mau',
            'beli',
            'pesen',
            'order',
            'dong',
            'ya',
            'deh',
            'nih',
            'pengen',
            'ingin',
            'dan',
            'atau',
            'sama',
            'plus',
            'juga',
            'ada',
            'gak',
            'kah'
        ];

        $keywords = explode(' ', $command);
        $keywords = array_filter($keywords, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 1;
        });

        // === MATCH PRODUCTS ===
        $matchedProducts = [];
        $matchScores = []; // Untuk prioritas matching

        foreach ($products as $product) {
            $productName = strtolower($product->name);
            $productCategory = strtolower($product->category ?? '');
            $score = 0;
            $matchedCategory = null;

            // 1. Cek direct keyword match di nama produk
            foreach ($keywords as $keyword) {
                if (strpos($productName, $keyword) !== false) {
                    $score += 10; // High score untuk direct match
                }
            }

            // 2. Cek sinonim dan kategori
            foreach ($synonyms as $category => $syns) {
                foreach ($keywords as $keyword) {
                    if (in_array($keyword, $syns)) {
                        // Cek apakah kategori ada di nama produk, kategori produk, atau mapping
                        $mappedCategories = $categoryMappings[$category] ?? [];
                        if (
                            strpos($productName, $category) !== false ||
                            $productCategory == $category ||
                            in_array($productCategory, $mappedCategories)
                        ) {
                            $score += 8;
                            $matchedCategory = $category;
                        }
                        // Cek sinonim lain dari kategori yang sama
                        foreach ($syns as $syn) {
                            if ($syn !== $keyword && strpos($productName, $syn) !== false) {
                                $score += 5;
                            }
                        }
                    }
                }
            }

            // 3. Cek frasa kompleks (contoh: "teh pucuk")
            $commandClean = str_replace($stopWords, '', $command);
            if (
                strpos($commandClean, $productName) !== false ||
                strpos($productName, $commandClean) !== false
            ) {
                $score += 15; // Highest score untuk exact phrase match
            }

            // Jika ada score, tambahkan ke matched products
            if ($score > 0) {
                $matchedProducts[] = $product;
                $matchScores[$product->id] = $score;
            }
        }

        // === REMOVE DUPLICATES & SORT BY RELEVANCE ===
        $matchedProducts = collect($matchedProducts)
            ->unique('id')
            ->sortByDesc(function ($product) use ($matchScores) {
                return $matchScores[$product->id] ?? 0;
            })
            ->values()
            ->all();

        // === HANDLE NO MATCHES ===
        if (empty($matchedProducts)) {
            // Coba cari produk serupa untuk saran
            $suggestions = $this->findSimilarProducts($products, $keywords, $synonyms);

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang cocok ditemukan.',
                'suggestions' => $suggestions,
                'added_products' => []
            ]);
        }

        // === IF QUESTION, RETURN LIST ===
        if ($isQuestion) {
            $productList = array_map(function ($p) {
                return ['id' => $p->id, 'name' => $p->name, 'price' => $p->selling_price];
            }, $matchedProducts);

            return response()->json([
                'success' => true,
                'message' => 'Berikut produk yang tersedia:',
                'products' => $productList,
                'added_products' => []
            ]);
        }

        // === HANDLE AMBIGUOUS MATCHES ===
        // Jika terlalu banyak produk cocok dengan score rendah, minta klarifikasi
        if (count($matchedProducts) > 5) {
            $topMatches = array_slice($matchedProducts, 0, 5);
            $productNames = array_map(function ($p) {
                return $p->name;
            }, $topMatches);

            return response()->json([
                'success' => false,
                'message' => 'Ditemukan beberapa produk yang cocok. Mana yang Anda maksud?',
                'suggestions' => $productNames,
                'products' => $topMatches,
                'added_products' => []
            ]);
        }

        // === BUILD RESULT WITH QUANTITIES ===
        $added = [];
        $defaultQty = $quantities['default'] ?? 1;

        foreach ($matchedProducts as $index => $product) {
            // Coba cari qty spesifik untuk produk ini
            $qty = $quantities['items'][$index] ?? $defaultQty;

            $added[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'qty' => $qty,
                'match_score' => $matchScores[$product->id] ?? 0
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'added_products' => $added
        ]);
    }

    private function extractQuantities($command)
    {
        $quantities = [
            'default' => 1,
            'items' => []
        ];

        // Mapping angka kata ke digit
        $numberWords = [
            'satu' => 1,
            'se' => 1,
            'sebiji' => 1,
            'sebuah' => 1,
            'dua' => 2,
            'tiga' => 3,
            'empat' => 4,
            'lima' => 5,
            'enam' => 6,
            'tujuh' => 7,
            'delapan' => 8,
            'sembilan' => 9,
            'sepuluh' => 10,
            'sebelas' => 11,
            'duabelas' => 12
        ];

        // Pattern untuk angka digit
        preg_match_all('/(\d+)\s*([a-z]+)?/i', $command, $digitMatches);

        // Pattern untuk angka kata
        $wordPattern = implode('|', array_keys($numberWords));
        preg_match_all('/(' . $wordPattern . ')\s+([a-z]+)/i', $command, $wordMatches);

        // Process digit matches
        if (!empty($digitMatches[1])) {
            foreach ($digitMatches[1] as $index => $qty) {
                $quantities['items'][$index] = (int) $qty;
                if ($index === 0) {
                    $quantities['default'] = (int) $qty;
                }
            }
        }

        // Process word matches
        if (!empty($wordMatches[1])) {
            foreach ($wordMatches[1] as $index => $word) {
                $qty = $numberWords[strtolower($word)] ?? 1;
                $quantities['items'][$index] = $qty;
                if ($index === 0 && !isset($quantities['items'][0])) {
                    $quantities['default'] = $qty;
                }
            }
        }

        return $quantities;
    }

    /**
     * Find similar products when no exact match found
     */
    private function findSimilarProducts($products, $keywords, $synonyms)
    {
        $suggestions = [];

        // Cari produk yang mengandung kategori dari keyword
        foreach ($products as $product) {
            $productName = strtolower($product->name);

            foreach ($synonyms as $category => $syns) {
                // Cek apakah ada kategori yang match
                if (strpos($productName, $category) !== false) {
                    $suggestions[] = $product->name;
                    break;
                }
            }

            if (count($suggestions) >= 5)
                break;
        }

        return array_unique($suggestions);
    }

}
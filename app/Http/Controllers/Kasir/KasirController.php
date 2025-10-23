<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User; // Tambah import User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector; // Untuk Windows

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query(); // Mulai tanpa filter kasir_id
    
        // Filter berdasarkan parameter 'filter'
        $filter = $request->get('filter', 'kasir'); // Default 'kasir'
        if ($filter === 'kasir') {
            $query->where('kasir_id', Auth::id()); // Hanya transaksi kasir
        }
        // Jika 'all', tidak ada filter kasir_id, tampilkan semua transaksi pembeli
    
        if ($request->filled('q')) {
            $query->where('unique_code', 'like', '%' . $request->q . '%');
        }
    
        $transactions = $query->latest()->paginate(10);
        return view('kasir.transaksi.index', compact('transactions', 'filter')); // Pass 'filter' ke view
    }
    public function scan(Request $request)
    {
        $code = $request->code;  // Dari QR/Barcode scan
        $transaction = Transaction::where('unique_code', $code)->first();

        if ($transaction && $transaction->status == 'pending') {
            $transaction->update([
                'status' => 'dibayar',
                'kasir_id' => Auth::id(),
            ]);
            // Kurangi stok produk di $transaction->items
            foreach (json_decode($transaction->items, true) as $item) {
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }
            return response()->json(['success' => true, 'message' => 'Transaksi diproses!']);
        }
        return response()->json(['success' => false, 'message' => 'Kode tidak valid.']);
    }

    public function syncOffline(Request $request)
    {
        // Simpan transaksi offline di localStorage, sync ke DB saat online
        // Contoh: Parse $request->offline_data (JSON), create Transaction
        foreach (json_decode($request->offline_data, true) as $data) {
            Transaction::create($data + ['kasir_id' => Auth::id()]);
        }
        return response()->json(['success' => true]);
    }

    public function scanPage()
    {
        $products = Product::with('flashSales')->get();
        // Tambahkan harga akhir untuk setiap produk (sudah termasuk flash sale/diskon)
        $products->transform(function ($product) {
            $product->price = $product->final_price; // Gunakan accessor sebagai property, bukan method
            return $product;
        });
        $tebusMurahList = \DB::table('tebus_murah')
            ->join('products', 'tebus_murah.product_id', '=', 'products.id')
            ->select(
                'tebus_murah.product_id',
                'tebus_murah.tebus_price',
                'tebus_murah.max_qty',
                'tebus_murah.min_order',
                'products.name',
                'products.image'
            )
            ->get();
        return view('kasir.scan.index', compact('products', 'tebusMurahList'));
    }
    public function getProductStock(Request $request)
    {
        $ids = $request->input('ids', []);
        $products = \App\Models\Product::whereIn('id', $ids)
            ->select('id', 'stock')
            ->get();
        return response()->json(['products' => $products]);
    }
    public function scanProduct(Request $request)
    {
        $code = $request->code;
        $product = Product::where('unique_code', $code)->first();

        if ($product) {
            // Debug: Log harga untuk cek
            \Log::info('Scan Product Debug', [
                'product_id' => $product->id,
                'price_db' => $product->price, // Harga asli DB
                'selling_price' => $product->selling_price, // Setelah margin
                'final_price' => $product->final_price, // Akhir (dengan flash sale/diskon)
                'flash_sale_active' => $product->flashSales()->active()->exists(), // Cek flash sale aktif
            ]);

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->final_price, // Pastikan ini digunakan
                    'stock' => $product->stock,
                    'image' => $product->image,
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.']);
    }

    // Tambah method untuk get member dari users berdasarkan po_code
    public function getMember(Request $request)
    {
        $memberId = $request->member_id;
        $member = User::where('po_code', $memberId)->first(); // Cari berdasarkan po_code

        if ($member) {
            return response()->json([
                'success' => true,
                'member' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'balance' => $member->dimascash_balance, // Gunakan kolom dimascash_balance
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Member tidak ditemukan.']);
    }

    // Update checkout untuk handle payment_method dan member_id dari po_code
    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.name' => 'required|string',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,dimascash',
            'member_id' => 'nullable|string|exists:users,po_code', // Validasi berdasarkan po_code di users
            'cash_amount' => 'nullable|numeric|min:0', // Tambah validasi untuk cash_amount
        ]);

        $cart = $request->cart;
        $totalPrice = 0;
        $items = [];
        $member = null;

        if ($request->member_id) {
            $member = User::where('po_code', $request->member_id)->first(); // Cari berdasarkan po_code
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member tidak ditemukan.']);
            }
        }

        // Hitung total dan validasi stok
        foreach ($cart as $item) {
            $product = Product::with('flashSales')->find($item['id']); // Load flashSales
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Produk ' . $item['name'] . ' tidak ditemukan.']);
            }
            if ($product->stock < $item['quantity']) {
                return response()->json(['success' => false, 'message' => 'Stok produk ' . $item['name'] . ' tidak cukup. Stok tersedia: ' . $product->stock]);
            }

            // Gunakan final_price untuk produk biasa (sudah termasuk flash sale/diskon)
            $finalPrice = isset($item['is_tebus_murah']) && $item['is_tebus_murah']
                ? $item['price']  // Tetap gunakan tebus_price dari cart
                : $product->final_price;  // Gunakan accessor sebagai property

            // Logika promo (mirip cart.blade.php)
            $promoType = $product->promo_type;
            $promoDesc = '';
            $freeItems = 0;
            $discountItem = 0;
            $itemTotal = $finalPrice * $item['quantity'];

            if ($product->promo_active && $promoType == 'buy_x_for_y') {
                $x = $product->promo_buy;
                $y = $product->promo_get;
                $fullSets = floor($item['quantity'] / $x);
                $remaining = $item['quantity'] % $x;
                $itemTotal = $fullSets * $y + $remaining * $finalPrice;
                $promoDesc = "Beli $x hanya Rp " . number_format($y, 0, ',', '.');
                $discountItem = ($finalPrice * $item['quantity']) - $itemTotal;
            } elseif ($product->promo_active && $promoType == 'buy_x_get_y_free') {
                $x = $product->promo_buy;
                $y = $product->promo_get;
                $fullSets = floor($item['quantity'] / $x);
                $freeItems = $fullSets * $y;
                $itemTotal = $finalPrice * $item['quantity']; // Harga tetap, tapi ada gratis
                $promoDesc = "Beli $x Gratis $y";
                $discountItem = $freeItems * $finalPrice;
            }

            $totalPrice += $itemTotal;
            $itemData = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $finalPrice,
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
                'image' => $product->image,
                'promo_type' => $promoType,
                'promo_desc' => $promoDesc,
                'free_items' => $freeItems,
                'discount' => $discountItem,
            ];
            // Simpan flag tebus murah jika ada
            if (isset($item['is_tebus_murah']) && $item['is_tebus_murah']) {
                $itemData['is_tebus_murah'] = true;
                $itemData['max_qty'] = $item['max_qty'] ?? null;
            }
            $items[] = $itemData;
        }

        // Jika dimascash, cek saldo
        if ($request->payment_method == 'dimascash') {
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member diperlukan untuk pembayaran DimasCash.']);
            }
            if ($member->dimascash_balance < $totalPrice) { // Gunakan dimascash_balance
                return response()->json(['success' => false, 'message' => 'Saldo DimasCash tidak cukup. Saldo: Rp ' . number_format($member->dimascash_balance, 0, ',', '.')]);
            }
            // Kurangi saldo
            $member->decrement('dimascash_balance', $totalPrice); // Kurangi dimascash_balance
        }

        // Kurangi stok produk
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $product->decrement('stock', $item['quantity']);
        }

        // Buat transaksi
        $transaction = Transaction::create([
            'user_id' => $member ? $member->id : null, // Jika ada member, set user_id
            'kasir_id' => Auth::id(),
            'member_id' => $request->member_id, // Simpan po_code sebagai member_id
            'status' => 'selesai',
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
            'cash_amount' => $request->cash_amount, // Simpan cash_amount jika ada
            'items' => json_encode($items),
        ]);

        // Tambah poin jika ada member, per 1000 rupiah = 1 poin
        if ($member) {
            $pointsEarned = floor($totalPrice / 1000);
            if ($pointsEarned > 0) {
                $member->increment('points', $pointsEarned);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil!',
            'transaction_id' => $transaction->id,
        ]);
    }

    public function printStruk($id)
    {
        $transaction = Transaction::findOrFail($id);
        $items = json_decode($transaction->items, true);

        // Koneksi ke printer (ganti 'COM3' dengan port printer kamu)
        $connector = new WindowsPrintConnector("COM3");
        $printer = new Printer($connector);

        // Header
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Dimashop\n");
        $printer->text("ID Transaksi: " . $transaction->unique_code . "\n");
        $printer->text("Tanggal: " . $transaction->created_at->format('d/m/Y H:i:s') . "\n");
        $printer->text("Kasir: " . ($transaction->kasir->name ?? 'Offline') . "\n");
        $printer->text("----------------------------\n");
        // Items
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $totalHargaProduk = 0;  // Tambah variabel untuk total harga produk (opsional, jika ingin tampilkan subtotal)
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
            $printer->text("Harga: Rp " . number_format($item['total'], 0, ',', '.') . "\n");  // Gunakan 'total' dari item (sudah termasuk promo)

            $totalHargaProduk += $item['total'];  // Hitung total harga produk
        }

        // Total Harga Produk (opsional, tambahkan jika ingin tampilkan subtotal sebelum diskon voucher)
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("----------------------------\n");
        $printer->text("Total: Rp " . number_format($transaction->total_price, 0, ',', '.') . "\n");

        // Metode Pembayaran
        $printer->text("Metode Pembayaran: " . ucfirst($transaction->payment_method) . "\n");

        // Voucher
        if ($transaction->discount_amount > 0) {
            $printer->text("Potongan Voucher (" . $transaction->voucher_code . "): -Rp " . number_format($transaction->discount_amount, 0, ',', '.') . "\n");
        }

        // Total
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("----------------------------\n");
        $printer->text("Total: Rp " . number_format($transaction->total_price, 0, ',', '.') . "\n");

        // Jika pembayaran cash, tampilkan jumlah bayar dan kembalian
        if ($transaction->payment_method == 'cash' && $transaction->cash_amount) {
            $change = $transaction->cash_amount - $transaction->total_price;
            $printer->text("Bayar: Rp " . number_format($transaction->cash_amount, 0, ',', '.') . "\n");
            if ($change > 0) {
                $printer->text("Kembalian: Rp " . number_format($change, 0, ',', '.') . "\n");
            }
        }

        $printer->text("Status: " . ucfirst($transaction->status) . "\n");
        $printer->text("Terima Kasih atas Pembelian Anda!\n");
        $printer->text("Selamat Berbelanja Kembali\n");

        // Cut paper
        $printer->cut();

        // Close
        $printer->close();

        return redirect()->back()->with('success', 'Struk berhasil dicetak!');
    }

    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $words = array_filter(explode(' ', trim($query)));

        $productsQuery = Product::with('flashSales')->where('stock', '>', 0);

        if (count($words) > 1) {
            foreach ($words as $word) {
                $productsQuery->where('name', 'like', '%' . $word . '%');
            }
        } else {
            $productsQuery->where('name', 'like', '%' . $query . '%');
        }

        $products = $productsQuery->limit(10)->get()
            ->map(function ($product) {
                $product->price = $product->final_price; // Set harga ke final_price
                return $product;
            });

        // Jika tidak ada hasil dari multi-word, coba fallback
        if ($products->isEmpty() && count($words) > 1) {
            $products = Product::with('flashSales')->where('name', 'like', '%' . $query . '%')
                ->where('stock', '>', 0)
                ->limit(10)
                ->get()  // <-- Perbaikan: Hapus select field agar accessor final_price bisa jalan
                ->map(function ($product) {
                    $product->price = $product->final_price; // Set harga ke final_price
                    return $product;
                });
        }

        return response()->json(['products' => $products]);
    }
    public function parseAgentCommand(Request $request)
    {
        $command = $request->input('command');
        $apiKey = 'yOEX4SV4gIEslvF9hubgNrZnW0IK88v6W4V9tDtA';

        $prompt = <<<EOT
    Ekstrak daftar produk, jumlah, dan aksi dari perintah kasir berikut. Aksi bisa "add" (tambah qty ke yang ada) atau "set" (set qty ke jumlah baru, kosongkan yang ada dulu). Jika disebutkan "tambah", "tambahkan", atau sejenisnya, gunakan "add". Jika "semua stok", "maksimal", "seluruh", atau sejenisnya, gunakan "set" dengan qty: -1. Jika tidak disebutkan aksi, asumsikan "add". Hasilkan dalam format JSON array: [{"name": "nama produk", "qty": jumlah, "action": "add" atau "set"}]. Hanya tampilkan array JSON saja tanpa penjelasan apapun. Perintah: "$command"
    EOT;

        $response = \Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json',
        ])->post('https://api.cohere.ai/v1/chat', [
                    'model' => 'command-a-03-2025',
                    'message' => $prompt,
                    'temperature' => 0.2,
                    'max_tokens' => 256,
                ]);

        $result = $response->json();
        \Log::info('Cohere raw response:', ['result' => $result]);

        $text = $result['text'] ?? $result['response'] ?? $result['generations'][0]['text'] ?? '';

        \Log::info('Cohere output:', ['text' => $text]);

        $json = null;
        if (preg_match('/\[[\s\S]*\]/', $text, $matches)) {
            $json = $matches[0];
        } else {
            $json = $text;
        }

        \Log::info('Cohere JSON candidate:', ['json' => $json]);

        $items = json_decode($json, true);

        \Log::info('Cohere decoded items:', ['items' => $items, 'json_error' => json_last_error_msg()]);

        if (!is_array($items)) {
            return response()->json([
                'items' => [],
                'debug' => [
                    'text' => $text,
                    'json' => $json,
                    'error' => json_last_error_msg()
                ]
            ]);
        }

        return response()->json(['items' => $items]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Restock;
use App\Models\ProfitSetting;
use App\Models\TopUpRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'daily');

        $query = Transaction::where('status', 'selesai');
        switch ($period) {
            case 'weekly':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case 'monthly':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            default:
                $query->whereDate('created_at', today());
        }

        $sales = $query->selectRaw('kasir_id, SUM(total_price) as total')
            ->groupBy('kasir_id')
            ->get();

        $salesWithNames = $sales->map(function ($sale) {
            $sale->kasir = User::find($sale->kasir_id);
            return $sale;
        });

        // Tambahkan query kasirSales untuk chart
        $kasirSales = Transaction::where('status', 'selesai')
            ->selectRaw('users.name as kasir_name, SUM(total_price) as total, COUNT(*) as count')
            ->join('users', 'transactions.kasir_id', '=', 'users.id')
            ->groupBy('kasir_id', 'users.name')
            ->get();

        $totalTransactions = Transaction::where('status', 'selesai')->count();
        $totalRevenue = Transaction::where('status', 'selesai')->sum('total_price');

        return view('admin.dashboard', compact(
            'salesWithNames',
            'totalTransactions',
            'totalRevenue',
            'period',
            'kasirSales'
        ));
    }
    public function exportSalesPdf(Request $request)
    {
        $period = $request->get('period', 'daily');
        $reportType = $request->get('report_type', 'sales');

        // Tentukan tanggal awal dan akhir berdasarkan periode
        $startDate = null;
        $endDate = now();
        switch ($period) {
            case 'daily':
                if ($request->get('start_date')) {
                    $startDate = \Carbon\Carbon::parse($request->get('start_date'))->startOfDay();
                } else {
                    $startDate = today();
                }
                if ($request->get('end_date')) {
                    $endDate = \Carbon\Carbon::parse($request->get('end_date'))->endOfDay();
                } else {
                    $endDate = now();
                }
                break;
            case 'weekly':
                $startWeek = $request->get('start_week');
                $endWeek = $request->get('end_week');
                if ($startWeek) {
                    $startDate = \Carbon\Carbon::parse($startWeek)->startOfWeek();
                } else {
                    $startDate = now()->startOfWeek()->subWeeks(1);
                }
                if ($endWeek) {
                    $endDate = \Carbon\Carbon::parse($endWeek)->endOfWeek();
                } else {
                    $endDate = now()->endOfWeek();
                }
                break;
            case 'monthly':
                $startMonth = $request->get('start_month');
                $endMonth = $request->get('end_month');
                if ($startMonth) {
                    $startDate = \Carbon\Carbon::parse($startMonth)->startOfMonth();
                } else {
                    $startDate = now()->startOfMonth()->subMonths(1);
                }
                if ($endMonth) {
                    $endDate = \Carbon\Carbon::parse($endMonth)->endOfMonth();
                } else {
                    $endDate = now()->endOfMonth();
                }
                break;
            case 'all':
                $startDate = 'All Time';
                $endDate = 'All Time';
                break;
            case 'custom':
                $startDate = $request->get('start_date_custom') ? \Carbon\Carbon::parse($request->get('start_date_custom'))->startOfDay() : today();
                $endDate = $request->get('end_date_custom') ? \Carbon\Carbon::parse($request->get('end_date_custom'))->endOfDay() : now();
                break;
            default:
                $startDate = today();
        }

        if ($reportType === 'transaction') {
            // Query transaksi untuk laporan transaksi
            $query = Transaction::query();
            if ($startDate !== 'All Time') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $transactions = $query->get();

            // Data untuk PDF laporan transaksi
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'transactions' => $transactions,
            ];

            // Generate PDF laporan transaksi
            $pdf = Pdf::loadView('admin.exports.transaction_report', $data);
            return $pdf->download('laporan_transaksi_' . $period . '.pdf');
        } elseif ($reportType === 'stock') {
            // Query products
            $products = Product::all();

            // Calculate stock data for each product
            $stockData = $products->map(function ($product) use ($startDate, $endDate) {
                // Barang masuk dalam periode
                $barangMasuk = Restock::where('product_id', $product->id)
                    ->when($startDate !== 'All Time', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                    ->sum('quantity');
            
                // Barang keluar dalam periode
                $barangKeluar = 0;
                $transactions = Transaction::where('status', 'selesai')
                    ->when($startDate !== 'All Time', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                    ->get();
                foreach ($transactions as $transaction) {
                    $items = json_decode($transaction->items, true);
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if ($item['product_id'] == $product->id) {
                                $barangKeluar += $item['quantity'] ?? 0;
                            }
                        }
                    }
                }
            
                // Stok awal: current stock - barang masuk + barang keluar (mundur dari current)
                $stokAwal = $product->stock - $barangMasuk + $barangKeluar;
            
                // Sisa stok: selalu current stock
                $sisaStok = $product->stock;
            
                return [
                    'kode_produk' => $product->unique_code,
                    'nama_produk' => $product->name,
                    'stok_awal' => $stokAwal,
                    'barang_masuk' => $barangMasuk,
                    'barang_keluar' => $barangKeluar,
                    'sisa_stok' => $sisaStok,
                ];
            });

            // Data untuk PDF laporan stok
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'stockData' => $stockData,
            ];

            // Generate PDF laporan stok
            $pdf = Pdf::loadView('admin.exports.stock_report', $data);
            return $pdf->download('laporan_stok_' . $period . '.pdf');
        } elseif ($reportType === 'user') {
            // Query users
            $users = User::all();

            // Calculate user data
            $userData = $users->map(function ($user) use ($startDate, $endDate) {
                    // Jumlah transaksi dalam periode (sebagai kasir yang melayani + sebagai pembeli)
                    $totalTransactions = 0;
                    if ($startDate === 'All Time') {
                        // Untuk 'All Time', hitung total transaksi sepanjang waktu
                        $totalTransactions = Transaction::where('kasir_id', $user->id)->count();
                        // Tambahkan transaksi sebagai pembeli (jika ada field user_id)
                        if (\Schema::hasColumn('transactions', 'user_id')) {
                            $totalTransactions += Transaction::where('user_id', $user->id)->count();
                        }
                    } else {
                        // Untuk periode tertentu, hitung transaksi dalam periode
                        $totalTransactions = Transaction::where('kasir_id', $user->id)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
                        // Tambahkan transaksi sebagai pembeli (jika ada field user_id)
                        if (\Schema::hasColumn('transactions', 'user_id')) {
                            $totalTransactions += Transaction::where('user_id', $user->id)
                                ->whereBetween('created_at', [$startDate, $endDate])
                                ->count();
                        }
                    }

                // Level membership berdasarkan XP
                $level = 'Rakyat Jelata';
                if ($user->xp >= 8500) {
                    $level = 'Raja';
                } elseif ($user->xp >= 3000) {
                    $level = 'Patih';
                } elseif ($user->xp >= 1500) {
                    $level = 'Bangsawan';
                }

                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('d/m/Y'),
                    'points' => $user->points,
                    'dimascash_balance' => $user->dimascash_balance,
                    'total_transactions' => $totalTransactions,
                    'level' => $level,
                ];
            });

            // Hitung jumlah pengguna baru dalam periode
            $newUsersCount = 0;
            if ($startDate === 'All Time') {
                $newUsersCount = User::count(); // Total semua user untuk 'All Time'
            } else {
                $newUsersCount = User::whereBetween('created_at', [$startDate, $endDate])->count();
            }
            
            // Total pengguna aktif (yang punya transaksi dalam periode atau pernah transaksi untuk 'All Time')
            $activeUsersCount = 0;
            if ($startDate === 'All Time') {
                $activeUsersCount = User::whereHas('transactions')->count(); // Yang pernah transaksi
            } else {
                $activeUsersCount = User::whereHas('transactions', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })->count();
            }

            // Data untuk PDF laporan pengguna
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'userData' => $userData,
                'newUsersCount' => $newUsersCount,
                'activeUsersCount' => $activeUsersCount,
            ];

            // Generate PDF laporan pengguna
            $pdf = Pdf::loadView('admin.exports.user_report', $data);
            return $pdf->download('laporan_pengguna_' . $period . '.pdf');
        } elseif ($reportType === 'profit') {
            // Query transaksi dalam periode
            $query = Transaction::where('status', 'selesai');
            if ($startDate !== 'All Time') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $transactions = $query->get();

            // Pendapatan total
            $totalRevenue = $transactions->sum('total_price');

            // HPP (harga pokok penjualan)
            $totalHPP = 0;
            $productDetails = [];  // Ganti dari collect() ke array
            foreach ($transactions as $transaction) {
                $items = json_decode($transaction->items, true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $productId = $item['product_id'] ?? null;
                        $quantity = $item['quantity'] ?? 0;
                        if ($productId) {
                            $product = Product::find($productId);
                            if ($product) {
                                $totalHPP += $product->price * $quantity;
                                // Collect product details
                                if (isset($productDetails[$productId])) {
                                    $productDetails[$productId]['quantity'] += $quantity;
                                } else {
                                    $productDetails[$productId] = [
                                        'name' => $product->name,
                                        'unique_code' => $product->unique_code,
                                        'price' => $product->price,
                                        'quantity' => $quantity,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Diskon / promo
            $totalDiscount = $transactions->sum('discount_amount');

            // Laba bersih
            $netProfit = $totalRevenue - $totalHPP - $totalDiscount;

            // Data untuk PDF laporan keuntungan
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'totalRevenue' => $totalRevenue,
                'totalHPP' => $totalHPP,
                'totalDiscount' => $totalDiscount,
                'netProfit' => $netProfit,
                'productDetails' => $productDetails,
            ];

            // Generate PDF laporan keuntungan
            $pdf = Pdf::loadView('admin.exports.profit_report', $data);
            return $pdf->download('laporan_keuntungan_' . $period . '.pdf');

        } elseif ($reportType === 'kasir_performance') {
            // Query performa kasir dalam periode
            $query = Transaction::where('status', 'selesai')
                ->selectRaw('kasir_id, users.name as kasir_name, COUNT(*) as total_transactions, SUM(total_price) as total_revenue')
                ->join('users', 'transactions.kasir_id', '=', 'users.id');
            if ($startDate !== 'All Time') {
                $query->whereBetween('transactions.created_at', [$startDate, $endDate]);
            }
            $kasirPerformance = $query->groupBy('kasir_id', 'users.name')
                ->orderBy('total_revenue', 'desc')
                ->get();

            // Data untuk PDF laporan performa kasir
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'kasirPerformance' => $kasirPerformance,
            ];

            // Generate PDF laporan performa kasir
            $pdf = Pdf::loadView('admin.exports.kasir_performance_report', $data);
            return $pdf->download('laporan_performa_kasir_' . $period . '.pdf');
        } elseif ($reportType === 'sales') {
            // Query transaksi tanpa 'details' relationship (karena tidak ada model TransactionDetail)
            $query = Transaction::where('status', 'selesai');
            if ($startDate !== 'All Time') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $transactions = $query->get();

            // Hitung total penjualan
            $totalSales = $transactions->sum('total_price');

            // Produk paling laris: decode 'items' JSON dan group by product_id
            $topProducts = collect();
            foreach ($transactions as $transaction) {
                $items = json_decode($transaction->items, true); // Decode JSON items
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $productId = $item['product_id'] ?? null;
                        $quantity = $item['quantity'] ?? 0;
                        if ($productId) {
                            $product = \App\Models\Product::find($productId); // Use fully qualified namespace
                            if ($product) {
                                if ($topProducts->has($productId)) {
                                    // Retrieve, modify, and reassign the nested array
                                    $existingItem = $topProducts->get($productId);
                                    $existingItem['quantity'] += $quantity;
                                    $topProducts->put($productId, $existingItem);
                                } else {
                                    $topProducts->put($productId, [
                                        'name' => $product->name,
                                        'quantity' => $quantity,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            $topProducts = $topProducts->sortByDesc('quantity')->take(10); // Top 10

            // Data untuk PDF
            $data = [
                'period' => $period,
                'startDate' => is_string($startDate) ? $startDate : $startDate->format('d/m/Y'),
                'endDate' => is_string($endDate) ? $endDate : $endDate->format('d/m/Y'),
                'transactions' => $transactions,
                'totalSales' => $totalSales,
                'topProducts' => $topProducts,
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.exports.sales_report', $data);
            return $pdf->download('laporan_penjualan_' . $period . '.pdf');
        } else {
            // Jika reportType tidak dikenali, bisa throw error atau redirect
            abort(400, 'Jenis laporan tidak valid.');
        }

    }

    public function updateProfit(Request $request)
    {
        $request->validate([
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        ProfitSetting::updateOrCreate(
            ['id' => 1], // Asumsikan satu setting global
            ['type' => $request->type, 'value' => $request->value]
        );

        return redirect()->back()->with('success', 'Keuntungan toko berhasil diupdate!');
    }
    public function profit()
    {
        $profit = ProfitSetting::first(); // Ambil setting saat ini
        return view('admin.profit', compact('profit'));
    }
    public function topUpDimascash(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = User::find($request->user_id);
        $user->increment('dimascash_balance', $request->amount);

        return response()->json(['success' => true, 'message' => 'Top up berhasil. Saldo baru: ' . $user->dimascash_balance_formatted]);
    }

    public function topupRequests()
    {
        $requests = TopUpRequest::with('user')->latest()->paginate(10);
        return view('admin.topup_requests', compact('requests'));
    }

    public function approveTopup($id)
    {
        $request = TopUpRequest::findOrFail($id);
        $request->update(['status' => 'approved']);
        $request->user->increment('dimascash_balance', $request->amount);
        return redirect()->back()->with('success', 'Top up approved.');
    }

    public function rejectTopup(Request $req, $id)
    {
        $request = TopUpRequest::findOrFail($id);
        $request->update(['status' => 'rejected', 'admin_note' => $req->note]);
        return redirect()->back()->with('success', 'Top up rejected.');
    }
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'code_type' => 'required|in:random,manual',
            'code' => 'required_if:code_type,manual|string|unique:vouchers',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
            'points_required' => 'nullable|integer|min:0', // Baru
            'is_redeemable_with_points' => 'boolean', // Baru
        ]);

        $code = $request->code_type == 'random' ? $this->generateUniqueCode() : $request->code;

        Voucher::create([
            'code' => $code,
            'name' => $request->name,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order' => $request->min_order,
            'expires_at' => $request->expires_at,
            'is_active' => $request->is_active ?? false,
            'points_required' => $request->points_required, // Baru
            'is_redeemable_with_points' => $request->is_redeemable_with_points ?? false, // Baru
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created.');
    }
    private function generateUniqueCode()
    {
        do {
            $code = 'VOUCHER-' . strtoupper(substr(md5(mt_rand()), 0, 8));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
            'points_required' => 'nullable|integer|min:0', // Baru
            'is_redeemable_with_points' => 'boolean', // Baru
        ]);

        $voucher->update($request->all());
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted.');
    }
}
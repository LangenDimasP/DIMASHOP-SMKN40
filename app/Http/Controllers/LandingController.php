<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
    // Ambil kategori unik dari produk (semua kategori yang ada di database, bahkan jika tidak ada produk)
    $categories = Product::select('category')->distinct()->whereNotNull('category')->get()->pluck('category');

    // Jika ingin hardcode kategori tambahan (misal jika ada kategori tanpa produk), tambah:
    // $categories = $categories->merge(['Kategori Tambahan'])->unique();

    // Ambil produk terlaris berdasarkan jumlah penjualan dari transaksi (parse JSON items)
    $sales = collect();
    $transactions = Transaction::all(); // Ambil semua transaksi (jika banyak, optimasi nanti)

    foreach ($transactions as $transaction) {
        $items = json_decode($transaction->items, true);
        if ($items) {
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'] ?? 0;
                if (!isset($sales[$productId])) {
                    $sales[$productId] = 0;
                }
                $sales[$productId] += $quantity;
            }
        }
    }

    // Sort berdasarkan total sold, ambil top 8, dan fetch produk
    $topProductIds = $sales->sortDesc()->keys()->take(8);
    $bestSellingProducts = Product::whereIn('id', $topProductIds)->get()->keyBy('id');
    $bestSellingProducts = $topProductIds->map(function ($id) use ($bestSellingProducts) {
        return $bestSellingProducts[$id] ?? null;
    })->filter();

    return view('welcome', compact('categories', 'bestSellingProducts'));
}
}
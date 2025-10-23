<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Promo;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        $total = 0;
        $items = [];
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = ($product->price * (1 - $product->discount / 100)) * $item['quantity'];
            $total += $subtotal;
            $items[] = $item + ['subtotal' => $subtotal, 'product_name' => $product->name];
        }

        // Promo code logic
        $promoCode = $request->promo_code;
        $promo = null;
        if ($promoCode) {
            $promo = Promo::where('code', $promoCode)->first();
            if ($promo && $promo->isValid()) {
                $total -= $total * ($promo->discount / 100);
                $promo->increment('used_count');
            }
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'payment_method' => $request->payment_method ?? 'cash',
            'total_price' => $total,
            'items' => json_encode($items),
            'status' => 'pending',
            'promo_code' => $promo?->code,
        ]);

        // Generate PO code untuk user
        Auth::user()->update(['po_code' => 'USER-' . Str::random(8)]);

        // Simulasi pembayaran sukses (integrasikan Midtrans/Stripe nanti)
        $transaction->update(['status' => 'dibayar']);

        return redirect()->route('user.dashboard')->with('success', 'Pesanan berhasil! Gunakan QR di bawah.');
    }
}
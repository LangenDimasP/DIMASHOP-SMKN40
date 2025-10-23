<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

public function indexPublic(Request $request)
{
    $products = Product::paginate(20);

    $activePromos = \App\Models\ProductPromo::where('active', true)->get();

    $now = now();
    $flashSales = \App\Models\FlashSale::where('active', true)
        ->where('end_time', '>=', $now)
        ->with('products')
        ->get();

    // Map produk yang sedang flash sale untuk set diskon
    $flashSaleProducts = [];
    foreach ($flashSales as $flashSale) {
        foreach ($flashSale->products as $product) {
            $flashSaleProducts[$product->id] = $flashSale;
        }
    }

    // Set harga diskon jika produk sedang flash sale
    foreach ($products as $product) {
        if (isset($flashSaleProducts[$product->id]) && $flashSaleProducts[$product->id]->start_time <= $now) {
            $flashSale = $flashSaleProducts[$product->id];
            $product->final_price = $product->selling_price * (1 - $flashSale->discount_percent / 100);
            $product->discount_value = $flashSale->discount_percent;
            $product->discount_type = 'percent';
        }
    }

    // Hitung total produk flash sale
    $totalFlashProducts = 0;
    foreach ($flashSales as $flashSale) {
        $totalFlashProducts += $flashSale->products->count();
    }

    $isUpcoming = false;
    $countdownTime = null;
    if ($flashSales->count() > 0) {
        $firstFlash = $flashSales->sortBy('start_time')->first();
        if ($firstFlash && $firstFlash->start_time > $now) {
            $isUpcoming = true;
            $countdownTime = $firstFlash->start_time;
        }
    }

    return view('user.products.index', compact(
        'products',
        'activePromos',
        'flashSales',
        'isUpcoming',
        'countdownTime',
        'totalFlashProducts'
    ));
}
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_value' => 'nullable|numeric|min:0|max:99999999.99',
            'discount_type' => 'nullable|in:percent,fixed',
            'category' => 'nullable|string',
            'images' => 'nullable|array', // Multiple images
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'unique_code' => 'nullable|string|unique:products,unique_code',
            'promo_type' => 'nullable|in:buy_x_get_y_free,buy_x_for_y',
            'promo_buy' => 'nullable|integer|min:1',
            'promo_get' => 'nullable|numeric|min:0',
            'promo_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'discount_value', 'discount_type', 'category', 'promo_type', 'promo_buy', 'promo_get', 'promo_active']);

        // Handle unique_code: jika diisi manual, gunakan; else generate otomatis
        $data['unique_code'] = $request->unique_code ?: $this->generateUniqueCode();

        // Handle multiple images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('products', 'public');
            }
        }
        $data['images'] = $images;

        // Tetap handle image utama jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Generate QR & Barcode (tetap)
        $qr = QrCode::size(200)->generate($product->unique_code);
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($product->unique_code, $generator::TYPE_CODE_128);

        Storage::put('public/qr/' . $product->unique_code . '.png', $qr);
        Storage::put('public/barcode/' . $product->unique_code . '.png', $barcode);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

private function generateUniqueCode()
{
    do {
        $code = 'PRODUK-' . strtoupper(substr(md5(uniqid()), 0, 8));
    } while (Product::where('unique_code', $code)->exists());

    return $code;
}

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

        public function update(Request $request, Product $product)
{
    try {
        $data = $request->only(['name', 'price', 'stock', 'discount_value', 'discount_type', 'description', 'category', 'promo_type', 'promo_buy', 'promo_get', 'promo_active', 'unique_code']);

        // Decode description jika encoded
        if (isset($data['description'])) {
            $data['description'] = urldecode($data['description']);
        }

        $rules = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $rules['name'] = 'required|string|max:255';
                    break;
                case 'price':
                    $rules['price'] = 'required|numeric|min:0';
                    break;
                case 'stock':
                    $rules['stock'] = 'required|integer|min:0';
                    break;
                case 'discount_value':
                    $rules['discount_value'] = 'nullable|numeric|min:0';  // Ubah ke nullable
                    if (isset($data['discount_type'])) {
                        if ($data['discount_type'] == 'percent') {
                            $rules['discount_value'] .= '|max:100';
                        } elseif ($data['discount_type'] == 'fixed') {
                            $rules['discount_value'] .= '|lt:' . ($data['price'] ?? $product->price);
                        }
                    }
                    break;
                case 'discount_type':
                    $rules['discount_type'] = 'nullable|in:fixed,percent';  // Ubah ke nullable
                    break;
                case 'description':
                    $rules['description'] = 'nullable|string';
                    break;
                case 'category':  // Tambah
                    $rules['category'] = 'nullable|string';
                    break;
                case 'unique_code':
                    $rules['unique_code'] = 'nullable|string|unique:products,unique_code,' . $product->id;
                    break;
                case 'promo_type':
                    $rules['promo_type'] = 'nullable|in:buy_x_get_y_free,buy_x_for_y';
                    break;
                case 'promo_buy':
                    $rules['promo_buy'] = 'nullable|integer|min:1';
                    break;
                case 'promo_get':
                    $rules['promo_get'] = 'nullable|numeric|min:0';
                    break;
                case 'promo_active':
                    $rules['promo_active'] = 'boolean';
                    break;
            }
        }
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpg,jpeg,png|max:2048';
        }
        // Tambah validation untuk multiple images
        if ($request->hasFile('images')) {
            $rules['images'] = 'array';
            $rules['images.*'] = 'image|mimes:jpg,jpeg,png|max:2048';
        }
        $request->validate($rules);

        // Simpan stok lama sebelum update
        $oldStock = $product->stock;

        $product->update($data);

        // Jika stok bertambah, catat sebagai restock
        $newStock = $product->stock;
        if ($newStock > $oldStock) {
            Restock::create([
                'product_id' => $product->id,
                'quantity' => $newStock - $oldStock,
            ]);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::delete('public/' . $product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
            $product->save();
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('products', 'public');
            }
            $product->images = $images;
            $product->save();
        }

        // Selalu return JSON untuk AJAX
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // Selalu return JSON error untuk AJAX
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Product $product)
    {
        // Hapus file QR/Barcode jika ada
        Storage::delete('public/qr/' . $product->unique_code . '.png');
        Storage::delete('public/barcode/' . $product->unique_code . '.png');

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }

public function showPublic($id)
{
    $product = Product::findOrFail($id);

    // Cek dan set diskon flash sale jika aktif
    $now = now();
    $flashSale = $product->flashSales()->where('active', true)->where('start_time', '<=', $now)->where('end_time', '>=', $now)->first();
    if ($flashSale) {
        $product->final_price = $product->selling_price * (1 - $flashSale->discount_percent / 100);
        $product->discount_value = $flashSale->discount_percent;
        $product->discount_type = 'percent';
    }

    // Produk terkait
    $relatedProducts = Product::where('category', $product->category)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    return view('user.products.show', compact('product', 'relatedProducts'));
}
}
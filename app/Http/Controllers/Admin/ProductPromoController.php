<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPromo;  // Ganti dari Promo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPromoController extends Controller  // Atau rename class ke ProductPromoController
{
    public function index()
    {
        $promos = ProductPromo::all();
        return view('admin.promos.index', compact('promos'));  // Pastikan view masih 'admin.promos.index'
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:buy_x_get_y_free,buy_x_for_y',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
            'code' => 'nullable|string|unique:product_promos,code',  // Ganti tabel dari promos ke product_promos
            'discount' => 'nullable|numeric|min:0|max:100',
            'valid_until' => 'nullable|date|after:today',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['type', 'active', 'code', 'discount', 'valid_until', 'usage_limit']);
        $data['used_count'] = 0;

        if (empty($data['code'])) {
            $data['code'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('product_promos', 'public');  // Ganti folder dari promos ke product_promos
        }

        ProductPromo::create($data);  // Ganti dari Promo::create

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil ditambahkan!');
    }

    public function show(ProductPromo $promo)  // Ganti parameter
    {
        return view('admin.promos.show', compact('promo'));
    }

    public function edit(ProductPromo $promo)  // Ganti parameter
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, ProductPromo $promo)  // Ganti parameter
    {
        $request->validate([
            'type' => 'required|in:buy_x_get_y_free,buy_x_for_y',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
            'code' => 'nullable|string|unique:product_promos,code,' . $promo->id,  // Ganti tabel
            'discount' => 'nullable|numeric|min:0|max:100',
            'valid_until' => 'nullable|date|after:today',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['type', 'active', 'code', 'discount', 'valid_until', 'usage_limit']);

        if ($request->hasFile('image')) {
            if ($promo->image) {
                Storage::delete('public/' . $promo->image);
            }
            $data['image'] = $request->file('image')->store('product_promos', 'public');  // Ganti folder
        }

        $promo->update($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil diupdate!');
    }

    public function destroy(ProductPromo $promo)  // Ganti parameter
    {
        if ($promo->image) {
            Storage::delete('public/' . $promo->image);
        }
        $promo->delete();

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil dihapus!');
    }
}
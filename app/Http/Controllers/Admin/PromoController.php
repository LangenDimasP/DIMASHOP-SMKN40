<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\ProductPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = ProductPromo::all();
        return view('admin.promos.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:buy_x_get_y_free,buy_x_for_y', // Untuk promo produk
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Gambar poster
            'active' => 'boolean', // Status aktif
            // Kolom lama untuk voucher (opsional, jika ingin gabung)
            'code' => 'nullable|string|unique:promos,code',
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
            $data['image'] = $request->file('image')->store('promos', 'public');
        }

        Promo::create($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        return view('admin.promos.show', compact('promo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promo $promo)
    {
        return view('admin.promos.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'type' => 'required|in:buy_x_get_y_free,buy_x_for_y',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'active' => 'boolean',
            'code' => 'nullable|string|unique:promos,code,' . $promo->id,
            'discount' => 'nullable|numeric|min:0|max:100',
            'valid_until' => 'nullable|date|after:today',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['type', 'active', 'code', 'discount', 'valid_until', 'usage_limit']);

        if ($request->hasFile('image')) {
            if ($promo->image) {
                Storage::delete('public/' . $promo->image);
            }
            $data['image'] = $request->file('image')->store('promos', 'public');
        }

        $promo->update($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        if ($promo->image) {
            Storage::delete('public/' . $promo->image);
        }
        $promo->delete();

        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil dihapus!');
    }
}
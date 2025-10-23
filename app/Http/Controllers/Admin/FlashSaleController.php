<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::with('products')->paginate(10);
        return view('admin.flash_sales.index', compact('flashSales'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.flash_sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'exists:products,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'active' => 'boolean',
        ]);

        $flashSale = FlashSale::create([
            'discount_percent' => $request->discount_percent,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'day_of_week' => $request->day_of_week,
            'active' => $request->active ? true : false,
        ]);

        $flashSale->products()->attach($request->product_id);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale berhasil dibuat!');
    }

    public function edit(FlashSale $flashSale)
    {
        $products = Product::all();
        return view('admin.flash_sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'exists:products,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'active' => 'boolean',
        ]);

        $flashSale->update([
            'discount_percent' => $request->discount_percent,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'day_of_week' => $request->day_of_week,
            'active' => $request->active ? true : false,
        ]);

        $flashSale->products()->sync($request->product_id);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale berhasil diupdate!');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->products()->detach();
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale berhasil dihapus!');
    }
}
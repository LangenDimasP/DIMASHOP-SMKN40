<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TebusMurah;
use App\Models\Product;
use Illuminate\Http\Request;

class TebusMurahController extends Controller
{
    public function index()
    {
        $tebusMurahList = TebusMurah::with('product')->get();
        return view('admin.tebus_murah.index', compact('tebusMurahList'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.tebus_murah.form', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tebus_price' => 'required|integer|min:1',
            'min_order' => 'required|integer|min:0',
            'max_qty' => 'required|integer|min:1',
            'active' => 'required|boolean',
        ]);
        TebusMurah::create($request->all());
        return redirect()->route('admin.tebus-murah.index')->with('success', 'Tebus Murah berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $tebusMurah = TebusMurah::findOrFail($id);
        $products = Product::all();
        return view('admin.tebus_murah.form', compact('tebusMurah', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tebus_price' => 'required|integer|min:1',
            'min_order' => 'required|integer|min:0',
            'max_qty' => 'required|integer|min:1',
            'active' => 'required|boolean',
        ]);
        $tebusMurah = TebusMurah::findOrFail($id);
        $tebusMurah->update($request->all());
        return redirect()->route('admin.tebus-murah.index')->with('success', 'Tebus Murah berhasil diupdate!');
    }

    public function destroy($id)
    {
        $tebusMurah = TebusMurah::findOrFail($id);
        $tebusMurah->delete();
        return redirect()->route('admin.tebus-murah.index')->with('success', 'Tebus Murah berhasil dihapus!');
    }
}
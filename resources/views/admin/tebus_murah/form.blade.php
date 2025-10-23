@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">{{ isset($tebusMurah) ? 'Edit' : 'Tambah' }} Tebus Murah</h1>
    <form action="{{ isset($tebusMurah) ? route('admin.tebus-murah.update', $tebusMurah->id) : route('admin.tebus-murah.store') }}" method="POST">
        @csrf
        @if(isset($tebusMurah))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block mb-1 font-medium">Produk</label>
            <select name="product_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ (old('product_id', $tebusMurah->product_id ?? '') == $product->id) ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Harga Tebus Murah</label>
            <input type="number" name="tebus_price" class="w-full border rounded px-3 py-2" min="1" required value="{{ old('tebus_price', $tebusMurah->tebus_price ?? '') }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Minimal Belanja (Rp)</label>
            <input type="number" name="min_order" class="w-full border rounded px-3 py-2" min="0" required value="{{ old('min_order', $tebusMurah->min_order ?? '') }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Maksimal Qty Tebus</label>
            <input type="number" name="max_qty" class="w-full border rounded px-3 py-2" min="1" required value="{{ old('max_qty', $tebusMurah->max_qty ?? 1) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium">Status</label>
            <select name="active" class="w-full border rounded px-3 py-2">
                <option value="1" {{ old('active', $tebusMurah->active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('active', $tebusMurah->active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded font-semibold">
                Simpan
            </button>
            <a href="{{ route('admin.tebus-murah.index') }}" class="px-4 py-2 rounded border">Batal</a>
        </div>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Promo</h1>
                <form method="POST" action="{{ route('admin.promos.update', $promo) }}" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Promo *</label>
                            <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="buy_x_get_y_free" {{ $promo->type == 'buy_x_get_y_free' ? 'selected' : '' }}>Buy X Get Y Free</option>
                                <option value="buy_x_for_y" {{ $promo->type == 'buy_x_for_y' ? 'selected' : '' }}>Buy X For Y Rupiah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Poster</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @if($promo->image)
                                <img src="{{ asset('storage/' . $promo->image) }}" alt="Current" class="w-32 h-32 object-cover mt-2">
                            @endif
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="active" value="1" id="active" {{ $promo->active ? 'checked' : '' }} class="w-4 h-4">
                            <label for="active" class="ml-2 text-sm">Aktif</label>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-3 rounded-lg">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
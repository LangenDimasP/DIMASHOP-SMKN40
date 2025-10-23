@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Tambah Promo</h1>
                <form method="POST" action="{{ route('admin.promos.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Promo *</label>
                            <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="buy_x_get_y_free">Buy X Get Y Free</option>
                                <option value="buy_x_for_y">Buy X For Y Rupiah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Poster *</label>
                            <input type="file" name="image" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="active" value="1" id="active" class="w-4 h-4">
                            <label for="active" class="ml-2 text-sm">Aktif</label>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-3 rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
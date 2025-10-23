@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h1 class="text-3xl font-bold text-cyan-500 mb-6">Tambah Flash Sale</h1>

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Produk -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Produk (pilih satu atau lebih)</label>
                <select name="product_id[]" multiple required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Tekan <kbd>Ctrl</kbd> (atau <kbd>Cmd</kbd> di Mac) untuk memilih lebih dari satu.</p>
            </div>

            <!-- Diskon (%) -->
            <div>
                <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                <input type="number" 
                       name="discount_percent" 
                       id="discount_percent" 
                       min="0" 
                       max="100" 
                       value="{{ old('discount_percent') }}" 
                       required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Waktu Mulai -->
            <div>
                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai</label>
                <input type="datetime-local" 
                       name="start_time" 
                       id="start_time" 
                       value="{{ old('start_time') }}" 
                       required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Waktu Akhir -->
            <div>
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Akhir</label>
                <input type="datetime-local" 
                       name="end_time" 
                       id="end_time" 
                       value="{{ old('end_time') }}" 
                       required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Hari (Opsional) -->
            <div>
                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Hari (Opsional)</label>
                <select name="day_of_week" id="day_of_week"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">Setiap Hari</option>
                    <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>Senin</option>
                    <option value="tuesday" {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                    <option value="wednesday" {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                    <option value="thursday" {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Kamis</option>
                    <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>Jumat</option>
                    <option value="saturday" {{ old('day_of_week') == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                    <option value="sunday" {{ old('day_of_week') == 'sunday' ? 'selected' : '' }}>Minggu</option>
                </select>
            </div>

            <!-- Aktif -->
            <div class="pt-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="active" 
                           value="1" 
                           {{ old('active', true) ? 'checked' : '' }} 
                           class="h-4 w-4 text-cyan-500 rounded focus:ring-cyan-400">
                    <span class="ml-2 text-gray-700">Aktif</span>
                </label>
            </div>

            <!-- Tombol -->
            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.flash-sales.index') }}" 
                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition">
                    Simpan Flash Sale
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="p-6 mt-4 bg-white rounded-xl shadow-lg max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-cyan-500 mb-4">Atur Keuntungan Toko</h1>
    
    <p class="text-gray-700 mb-6">
        Keuntungan ini akan diterapkan ke semua produk (<em>harga jual = harga asli + keuntungan</em>), lalu diskon dihitung dari harga jual.
    </p>

    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profit.update') }}" class="space-y-5">
        @csrf

        <!-- Tipe Keuntungan -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Keuntungan</label>
            <select name="type" id="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" required>
                <option value="percent" {{ ($profit->type ?? 'percent') == 'percent' ? 'selected' : '' }}>Persen (%)</option>
                <option value="fixed" {{ ($profit->type ?? 'percent') == 'fixed' ? 'selected' : '' }}>Fixed (Rp)</option>
            </select>
        </div>

        <!-- Nilai Keuntungan -->
        <div>
            <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Nilai Keuntungan</label>
            <input 
                type="number" 
                name="value" 
                id="value" 
                value="{{ $profit->value ?? 0 }}" 
                step="0.01" 
                min="0" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" 
                required
            >
            <p class="mt-1 text-sm text-gray-500">
                Contoh: <code class="bg-gray-100 px-1 rounded">10</code> untuk 10%, atau <code class="bg-gray-100 px-1 rounded">2000</code> untuk Rp 2.000 fixed.
            </p>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full md:w-auto bg-cyan-500 hover:bg-cyan-600 text-white font-medium px-5 py-2.5 rounded-lg shadow transition duration-200">
                Simpan Keuntungan
            </button>
        </div>
    </form>

    <!-- Contoh Perhitungan -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Contoh Perhitungan</h2>
        <ul class="space-y-2 text-gray-700 text-sm">
            <li>• Harga asli produk: <span class="font-mono">Rp 10.000</span></li>
            <li>• Keuntungan: 
                <span class="font-medium">{{ $profit->value ?? 0 }}</span> 
                {{ ($profit->type ?? 'percent') == 'percent' ? '%' : 'Rp' }}
            </li>
            <li>• Harga jual:
                @if(($profit->type ?? 'percent') == 'percent')
                    <span class="font-mono">Rp {{ number_format(10000 * (1 + (($profit->value ?? 0) / 100)), 0, ',', '.') }}</span> (jika persen)
                @else
                    <span class="font-mono">Rp {{ number_format(10000 + ($profit->value ?? 0), 0, ',', '.') }}</span> (jika fixed)
                @endif
            </li>
            <li>• Jika ada diskon 20%, harga akhir = Harga jual – 20%.</li>
        </ul>
    </div>
</div>
@endsection
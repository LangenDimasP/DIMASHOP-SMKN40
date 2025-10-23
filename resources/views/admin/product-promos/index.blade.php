@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">campaign</span>
                    Kelola Promo Produk
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Tambah, edit, dan hapus promo produk</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <a href="{{ route('admin.product-promos.create') }}"
                        class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center inline-block">
                        <span class="material-icons text-sm mr-2">add</span>
                        Tambah Promo Produk
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tipe Promo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Gambar</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($productPromos as $promo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">{{ ucfirst(str_replace('_', ' ', $promo->type)) }}</td>
                                    <td class="px-4 py-4">
                                        @if($promo->image)
                                            <img src="{{ asset('storage/' . $promo->image) }}" alt="Promo" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <span class="text-gray-400">No Image</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="px-2 py-1 text-xs rounded {{ $promo->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $promo->active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 flex gap-2">
                                        <a href="{{ route('admin.product-promos.edit', $promo) }}"
                                            class="text-blue-500 hover:text-blue-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.product-promos.destroy', $promo) }}" onsubmit="return confirm('Hapus promo ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada promo produk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
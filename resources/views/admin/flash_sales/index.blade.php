@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-cyan-500">Kelola Flash Sale</h1>
        <a href="{{ route('admin.flash-sales.create') }}" 
           class="bg-cyan-500 hover:bg-cyan-600 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200 ease-in-out">
            Tambah Flash Sale
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold">Produk</th>
                    <th class="px-5 py-3 text-left font-semibold">Diskon (%)</th>
                    <th class="px-5 py-3 text-left font-semibold">Waktu Mulai</th>
                    <th class="px-5 py-3 text-left font-semibold">Waktu Akhir</th>
                    <th class="px-5 py-3 text-left font-semibold">Hari</th>
                    <th class="px-5 py-3 text-left font-semibold">Status</th>
                    <th class="px-5 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($flashSales as $flashSale)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4">
                            @if($flashSale->products->isNotEmpty())
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                    @foreach($flashSale->products as $product)
                                        <li>{{ $product->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-medium">{{ $flashSale->discount_percent }}%</td>
                        <td class="px-5 py-4 text-gray-700">{{ $flashSale->start_time->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $flashSale->end_time->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $flashSale->day_of_week ? ucfirst($flashSale->day_of_week) : 'Setiap Hari' }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 text-xs rounded-full font-medium
                                @if($flashSale->active)
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-600
                                @endif">
                                {{ $flashSale->active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.flash-sales.edit', $flashSale) }}" 
                               class="text-cyan-600 hover:text-cyan-800 font-medium mr-3">Edit</a>
                            <form action="{{ route('admin.flash-sales.destroy', $flashSale) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="text-rose-600 hover:text-rose-800 font-medium"
                                        onclick="return confirm('Yakin ingin menghapus flash sale ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-gray-500">Belum ada flash sale.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Tebus Murah</h1>
        <a href="{{ route('admin.tebus-murah.create') }}" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded font-semibold">
            + Tambah Tebus Murah
        </a>
    </div>
    <div class="bg-white rounded shadow p-4">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="py-2">Produk</th>
                    <th>Harga Tebus</th>
                    <th>Min. Belanja</th>
                    <th>Maks. Qty</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tebusMurahList as $tm)
                <tr class="border-t">
                    <td class="py-2">{{ $tm->product->name ?? '-' }}</td>
                    <td>Rp {{ number_format($tm->tebus_price,0,',','.') }}</td>
                    <td>Rp {{ number_format($tm->min_order,0,',','.') }}</td>
                    <td>{{ $tm->max_qty }}</td>
                    <td>
                        <span class="px-2 py-1 rounded text-xs {{ $tm->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $tm->active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.tebus-murah.edit', $tm->id) }}" class="text-cyan-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('admin.tebus-murah.destroy', $tm->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">Belum ada data Tebus Murah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
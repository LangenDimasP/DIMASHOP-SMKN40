@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-cyan-500">Manage Vouchers</h1>
        <a href="{{ route('admin.vouchers.create') }}" 
           class="bg-cyan-500 hover:bg-cyan-600 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200 ease-in-out">
            Create Voucher
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-100 text-emerald-800 px-4 py-3 rounded-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full table-auto">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Code</th>
                    <th class="px-4 py-3 text-left font-semibold">Name</th>
                    <th class="px-4 py-3 text-left font-semibold">Type</th>
                    <th class="px-4 py-3 text-left font-semibold">Value</th>
                    <th class="px-4 py-3 text-left font-semibold">Min Order</th>
                    <th class="px-4 py-3 text-left font-semibold">Usage Limit</th>
                    <th class="px-4 py-3 text-left font-semibold">Used</th>
                    <th class="px-4 py-3 text-left font-semibold">Expires</th>
                    <th class="px-4 py-3 text-left font-semibold">Active</th>
                    <th class="px-4 py-3 text-left font-semibold">Points Req.</th>
                    <th class="px-4 py-3 text-left font-semibold">Redeem w/ Points</th>
                    <th class="px-4 py-3 text-left font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-gray-900">{{ $voucher->code }}</td>
                        <td class="px-4 py-3">{{ $voucher->name }}</td>
                        <td class="px-4 py-3">
                            <span class="capitalize">{{ $voucher->discount_type }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($voucher->discount_type === 'percent')
                                {{ $voucher->discount_value }}%
                            @else
                                Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            {{ $voucher->min_order ? 'Rp ' . number_format($voucher->min_order, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $voucher->usage_limit ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $voucher->usage_count }}</td>
                        <td class="px-4 py-3">
                            {{ $voucher->expires_at ? $voucher->expires_at->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $voucher->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $voucher->is_active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $voucher->points_required ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $voucher->is_redeemable_with_points ? 'bg-cyan-100 text-cyan-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $voucher->is_redeemable_with_points ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" 
                               class="text-cyan-600 hover:text-cyan-800 font-medium mr-3">Edit</a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="text-rose-600 hover:text-rose-800 font-medium"
                                        onclick="return confirm('Are you sure you want to delete this voucher?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center text-gray-500">No vouchers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $vouchers->links() }}
    </div>
</div>
@endsection
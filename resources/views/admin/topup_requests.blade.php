@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-cyan-500 mb-6">Top Up Requests</h1>

    @forelse($requests as $req)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-5">
            <div class="flex flex-col md:flex-row md:items-start gap-5">
                <!-- Bukti Transfer -->
                <div class="flex-shrink-0">
                    @if($req->proof_image)
                        <img src="{{ asset('storage/' . $req->proof_image) }}" 
                             alt="Bukti Transfer" 
                             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                            No Image
                        </div>
                    @endif
                </div>

                <!-- Info Request -->
                <div class="flex-grow">
                    <p class="text-gray-700"><span class="font-medium">User:</span> {{ $req->user->name }}</p>
                    <p class="text-gray-700"><span class="font-medium">Jumlah:</span> Rp {{ number_format($req->amount, 0, ',', '.') }}</p>
                    <p class="mt-1">
                        <span class="font-medium">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($req->status === 'approved') bg-green-100 text-green-800
                            @elseif($req->status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($req->status) }}
                        </span>
                    </p>

                    @if($req->note)
                        <p class="mt-2 text-sm text-gray-600"><span class="font-medium">Catatan:</span> {{ $req->note }}</p>
                    @endif

                    <!-- Action Buttons (only for pending) -->
                    @if($req->status === 'pending')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <!-- Approve -->
                            <form action="{{ route('admin.approveTopup', $req->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow transition duration-200">
                                    Approve
                                </button>
                            </form>

                            <!-- Reject -->
                            <form action="{{ route('admin.rejectTopup', $req->id) }}" method="POST" class="inline-block">
                                @csrf
                                <div class="flex flex-col sm:flex-row sm:items-end gap-2">
                                    <textarea name="note"
                                              placeholder="Alasan penolakan (opsional)"
                                              class="w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                                              rows="1"></textarea>
                                    <button type="submit"
                                            class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg shadow transition duration-200 whitespace-nowrap">
                                        Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500">Tidak ada permintaan top up.</p>
        </div>
    @endforelse

    <div class="mt-6">
        {{ $requests->links() }}
    </div>
</div>
@endsection
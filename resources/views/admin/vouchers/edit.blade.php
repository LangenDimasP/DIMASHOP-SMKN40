@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h1 class="text-3xl font-bold text-cyan-500 mb-6">Edit Voucher</h1>

        <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" id="voucher-form" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Code Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Code Type</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="code_type" value="random" class="h-4 w-4 text-cyan-500 focus:ring-cyan-400">
                        <span class="ml-2">Generate Random Code</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="code_type" value="manual" checked class="h-4 w-4 text-cyan-500 focus:ring-cyan-400">
                        <span class="ml-2">Manual Code</span>
                    </label>
                </div>
            </div>

            <!-- Code -->
            <div>
                <label for="code-input" class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                <input type="text" name="code" id="code-input" value="{{ $voucher->code }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ $voucher->name }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                          rows="3">{{ $voucher->description }}</textarea>
            </div>

            <!-- Discount Type -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                <select name="discount_type" id="discount_type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="percent" {{ $voucher->discount_type == 'percent' ? 'selected' : '' }}>Percent</option>
                    <option value="fixed" {{ $voucher->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                </select>
            </div>

            <!-- Discount Value -->
            <div>
                <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ $voucher->discount_value }}" step="0.01" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Min Order -->
            <div>
                <label for="min_order" class="block text-sm font-medium text-gray-700 mb-2">Min Order (optional)</label>
                <input type="number" name="min_order" id="min_order" value="{{ $voucher->min_order ?? '' }}" step="0.01"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                       placeholder="e.g. 50000">
            </div>

            <!-- Usage Limit -->
            <div>
                <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">Usage Limit (optional)</label>
                <input type="number" name="usage_limit" id="usage_limit" value="{{ $voucher->usage_limit ?? '' }}" min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                       placeholder="Leave empty for unlimited">
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">Expires At (optional)</label>
                <input type="datetime-local" name="expires_at" id="expires_at"
                       value="{{ $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <!-- Active -->
            <div class="pt-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $voucher->is_active ? 'checked' : '' }} class="h-4 w-4 text-cyan-500 rounded focus:ring-cyan-400">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>

            <!-- Points Required -->
            <div>
                <label for="points_required" class="block text-sm font-medium text-gray-700 mb-2">
                    Points Required (for point redemption, optional)
                </label>
                <input type="number" name="points_required" id="points_required" value="{{ $voucher->points_required ?? '' }}" min="0"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                       placeholder="Leave empty if not redeemable with points">
            </div>

            <!-- Redeemable with Points -->
            <div class="pt-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_redeemable_with_points" value="1" {{ $voucher->is_redeemable_with_points ? 'checked' : '' }} class="h-4 w-4 text-cyan-500 rounded focus:ring-cyan-400">
                    <span class="ml-2 text-gray-700">Can be redeemed with points</span>
                </label>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.vouchers.index') }}" 
                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition">
                    Update Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function generateRandomCode() {
        return 'VOUCHER-' + Math.random().toString(36).substring(2, 11).toUpperCase();
    }

    document.querySelectorAll('input[name="code_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const codeInput = document.getElementById('code-input');
            if (this.value === 'random') {
                codeInput.disabled = true;
                codeInput.value = generateRandomCode();
            } else {
                codeInput.disabled = false;
                codeInput.value = '{{ $voucher->code }}';
            }
        });
    });
</script>
@endsection
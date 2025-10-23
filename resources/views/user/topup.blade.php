@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">account_balance_wallet</span>
                    Top Up Dimascash
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Isi saldo Dimascash Anda</p>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
                    <span class="material-icons text-green-500 mr-3 flex-shrink-0">check_circle</span>
                    <div class="flex-1">
                        <p class="font-semibold text-green-800">Berhasil!</p>
                        <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Current Balance Card -->
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-sm p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-opacity-90 text-sm mb-1">Saldo Saat Ini</p>
                        <p class="text-3xl font-bold">{{ auth()->user()->dimascash_balance_formatted }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="material-icons text-white text-4xl">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            <!-- Top Up Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="material-icons text-cyan-500 mr-2">add_card</span>
                        Form Top Up
                    </h2>
                </div>

                <form action="{{ route('user.submitTopup') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
                    @csrf
                    
                    <!-- Amount Input -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Top Up (Rp) *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input 
                                type="number" 
                                name="amount" 
                                id="amount"
                                min="10000"
                                step="1000"
                                placeholder="10000"
                                required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Minimum top up Rp 10.000</p>
                    </div>

                    <!-- Proof Upload -->
                    <div>
                        <label for="proof_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Pembayaran (Foto) *
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-cyan-500 transition-colors">
                            <input 
                                type="file" 
                                name="proof_image" 
                                id="proof_image"
                                accept="image/*"
                                required
                                class="hidden"
                                onchange="previewImage(this)"
                            >
                            <label for="proof_image" class="cursor-pointer">
                                <div id="upload-placeholder">
                                    <span class="material-icons text-gray-400 text-5xl">cloud_upload</span>
                                    <p class="text-gray-600 mt-2 font-medium">Klik untuk upload bukti transfer</p>
                                    <p class="text-gray-400 text-sm mt-1">PNG, JPG, JPEG (Max. 2MB)</p>
                                </div>
                                <div id="image-preview" class="hidden">
                                    <img id="preview-img" src="" alt="Preview" class="max-w-full max-h-64 mx-auto rounded-lg border border-gray-200">
                                    <p class="text-cyan-600 mt-3 text-sm font-medium flex items-center justify-center">
                                        <span class="material-icons text-sm mr-1">check_circle</span>
                                        Gambar terpilih - Klik untuk ganti
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button 
                            type="submit"
                            class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <span class="material-icons text-sm mr-2">send</span>
                            Kirim Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const placeholder = document.getElementById('upload-placeholder');
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Auto-hide success alert after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.bg-green-50');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
@endsection
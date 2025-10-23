@extends('layouts.app')

@section('content')
    <!-- Google Material Icons (jika belum ada di layout) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm mb-6 max-w-full overflow-hidden">
                <a href="{{ route('products') }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[80px] sm:max-w-[120px]">Produk</a>
                <span class="text-gray-400">></span>
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-cyan-500 transition-colors truncate max-w-[100px] sm:max-w-[160px]">Halaman Sebelumnya</a>
                <span class="text-gray-400">></span>
                <span class="text-gray-900 truncate max-w-[120px] sm:max-w-[200px]">Profil Saya</span>
            </nav>
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                    <span class="material-icons profile-material-icons text-cyan-500 mr-2 text-3xl sm:text-4xl">account_circle</span>
                    Profil Saya
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Informasi akun dan member Anda</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Profile Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Account Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <span class="material-icons text-cyan-500 mr-2">person</span>
                                Informasi Akun
                            </h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <!-- Name -->
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons text-cyan-500 text-sm">badge</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Nama Lengkap</p>
                                    <p class="text-base font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons text-cyan-500 text-sm">email</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-base font-semibold text-gray-900">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <!-- PO Code -->
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons text-cyan-500 text-sm">confirmation_number</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Kode Member (PO)</p>
                                    <p class="text-base font-semibold text-gray-900 font-mono">{{ auth()->user()->po_code }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
<!-- Member Level Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            <span class="material-icons profile-material-icons text-yellow-500 mr-2">stars</span>
            Tingkatan Member
        </h2>
    </div>
    <div class="p-4 sm:p-6">
        @php
            $xp = auth()->user()->xp ?? 0;
            $levels = [
                1 => ['required' => 0, 'name' => 'Rakyat Jelata', 'icon' => '/images/Rakyat_Jelata.png'],
                2 => ['required' => 1500, 'name' => 'Bangsawan', 'icon' => '/images/Patih.png'],
                3 => ['required' => 3000, 'name' => 'Patih', 'icon' => '/images/Bangsawan.png'],
                4 => ['required' => 8500, 'name' => 'Raja', 'icon' => '/images/Raja.png'],
            ];
            
            // Temukan level saat ini
            $currentLevel = 1;
            foreach ($levels as $lvl => $data) {
                if ($xp >= $data['required']) {
                    $currentLevel = $lvl;
                }
            }
            
            $levelData = $levels[$currentLevel];
            $nextLevel = $currentLevel < 4 ? $currentLevel + 1 : null;
            $nextLevelXp = $nextLevel ? $levels[$nextLevel]['required'] : null;
            $xpNeeded = $nextLevelXp ? $nextLevelXp - $xp : 0;
            
            // Hitung progress dalam level saat ini
            $progress = 0;
            if ($nextLevelXp) {
                $xpInCurrentLevel = $xp - $levelData['required'];
                $xpRequiredForCurrentLevel = $nextLevelXp - $levelData['required'];
                $progress = ($xpInCurrentLevel / $xpRequiredForCurrentLevel) * 100;
            } elseif ($currentLevel === 4) {
                $progress = 100;
            }
            
            // Hitung lebar progress bar keseluruhan
            if ($currentLevel == 1) {
                $totalProgressBarWidth = ($progress / 100) * (100 / 3);
            } elseif ($currentLevel == 2) {
                $totalProgressBarWidth = (100 / 3) + (($progress / 100) * (100 / 3));
            } elseif ($currentLevel == 3) {
                $totalProgressBarWidth = (2 * 100 / 3) + (($progress / 100) * (100 / 3));
            } else {
                $totalProgressBarWidth = 100;
            }
        @endphp

        <!-- Current Level Info with Progress Text -->
        <div class="text-center mb-6 sm:mb-8 px-2">
            @if($nextLevelXp)
                <p class="text-sm sm:text-base font-medium text-gray-700">
                    Tambah <span class="text-cyan-600 font-bold">{{ number_format($xpNeeded) }} XP</span> lagi jadi <span class="font-bold">{{ $levels[$nextLevel]['name'] }}</span>
                </p>
            @else
                <p class="text-sm sm:text-base font-medium text-gray-700">
                    🎉 <span class="font-bold">Anda sudah mencapai level tertinggi!</span> 🎉
                </p>
            @endif
        </div>

        <!-- Horizontal Progress Bar with Level Icons -->
        <div class="relative mb-8 sm:mb-12 px-1 sm:px-4">
            <!-- Progress Line Background -->
            <div class="absolute top-6 sm:top-8 left-0 right-0 h-1.5 sm:h-2 bg-gray-200 rounded-full" 
                 style="left: calc(25% / 2); right: calc(25% / 2);">
                <!-- Progress Line Fill -->
                <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full transition-all duration-500" 
                     style="width: {{ number_format($totalProgressBarWidth, 2) }}%">
                </div>
            </div>

            <!-- Level Nodes - Equally Spaced -->
            <div class="relative flex justify-between items-start">
                @foreach($levels as $lvl => $data)
                    @php
                        $isPassed = $lvl < $currentLevel;
                        $isCurrent = $lvl === $currentLevel;
                        $isLocked = $lvl > $currentLevel;
                    @endphp
                    
                    <div class="flex flex-col items-center" style="width: 25%;">
                        <!-- Icon Circle -->
                        <div class="relative z-10 w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mb-2 sm:mb-3 transition-all duration-300 bg-white cursor-pointer hover:scale-105
                                    {{ $isPassed ? 'ring-2 sm:ring-4 ring-green-400 shadow-lg' : '' }}
                                    {{ $isCurrent ? 'ring-2 sm:ring-4 ring-yellow-400 shadow-xl' : '' }}
                                    {{ $isLocked ? 'ring-2 sm:ring-4 ring-gray-300' : '' }}"
                             onclick="showLevelModal({{ $lvl }})">
                            
                            @if($isPassed)
                                <img src="{{ $data['icon'] }}" alt="{{ $data['name'] }}" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full object-cover opacity-50 absolute">
                                <span class="material-icons profile-material-icons text-green-500 text-xl sm:text-3xl relative z-10">check_circle</span>
                            @elseif($isCurrent)
                                <img src="{{ $data['icon'] }}" alt="{{ $data['name'] }}" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full object-cover">
                            @else
                                <img src="{{ $data['icon'] }}" alt="{{ $data['name'] }}" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full object-cover opacity-50 absolute">
                                <span class="material-icons profile-material-icons text-gray-400 text-lg sm:text-2xl relative z-10">lock</span>
                            @endif
                        </div>

                        <!-- Level Label -->
                        <div class="text-center max-w-full px-1">
                            @if($isCurrent)
                                <span class="inline-block px-1.5 sm:px-3 py-0.5 sm:py-1 bg-yellow-100 text-yellow-800 text-[10px] sm:text-xs font-bold rounded-full mb-1 sm:mb-2">
                                    Grade
                                </span>
                            @endif
                            
                            <p class="font-bold text-[10px] sm:text-sm leading-tight {{ $isCurrent ? 'text-yellow-600' : ($isPassed ? 'text-green-600' : 'text-gray-400') }}">
                                {{ $data['name'] }}
                            </p>
                            <p class="text-[9px] sm:text-xs text-gray-500 mt-0.5 sm:mt-1">
                                {{ number_format($data['required']) }} XP
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Current XP Display -->
        <div class="text-center pt-4 sm:pt-6 border-t border-gray-200">
            <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($xp) }} XP</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Total XP Anda</p>
        </div>
    </div>
</div>

                    <!-- Balance & Points -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Dimascash Balance -->
                        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-sm p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-white opacity-80">account_balance_wallet</span>
                                <a href="{{ route('user.topup') }}"
                                    class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded-full transition-colors">
                                    Top Up
                                </a>
                            </div>
                            <p class="text-sm opacity-90 mb-1">Saldo Dimascash</p>
                            <p class="text-2xl font-bold">{{ auth()->user()->dimascash_balance_formatted }}</p>
                        </div>

                        <!-- Member Points -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-yellow-500">stars</span>
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-semibold">Member</span>
                            </div>
                            <p class="text-sm text-gray-500 mb-1">Member Points</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format(auth()->user()->points, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <span class="material-icons text-cyan-500 mr-2">receipt_long</span>
                                    Riwayat Transaksi Terakhir
                                </h2>
                                <a href="{{ route('user.transactions') }}"
                                    class="text-sm text-cyan-500 hover:text-cyan-600 font-medium flex items-center">
                                    Lihat Semua
                                    <span class="material-icons text-sm ml-1">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if(auth()->user()->transactions->count() > 0)
                                <div class="space-y-3">
                                    @foreach(auth()->user()->transactions->take(5) as $trans)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                                                    <span class="material-icons text-cyan-500 text-sm">shopping_bag</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $trans->unique_code }}</p>
                                                    <p class="text-xs text-gray-500">{{ $trans->created_at->format('d M Y, H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $statusConfig = [
                                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                                        'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                                    ];
                                                    $status = $statusConfig[$trans->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                                @endphp
                                                <span class="px-2 py-1 {{ $status['bg'] }} {{ $status['text'] }} text-xs font-semibold rounded-full">
                                                    {{ ucfirst($trans->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <span class="material-icons text-gray-300 text-5xl">receipt_long</span>
                                    <p class="text-gray-500 mt-3 text-sm">Belum ada transaksi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- QR Code & Barcode Section -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- QR Code Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <span class="material-icons text-cyan-500 mr-2">qr_code</span>
                                QR Code Member
                            </h2>
                        </div>
                        <div class="p-6 flex flex-col items-center">
                            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4">
                                {!! QrCode::size(200)->generate(auth()->user()->po_code) !!}
                            </div>
                            <p class="text-sm text-gray-600 text-center mb-3">
                                Tunjukkan QR Code ini ke kasir untuk melakukan transaksi
                            </p>
                            <button onclick="downloadQRCode()"
                                class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <span class="material-icons text-sm mr-2">download</span>
                                Download QR Code
                            </button>
                        </div>
                    </div>

                    <!-- Barcode Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <span class="material-icons text-cyan-500 mr-2">barcode</span>
                                Barcode Member
                            </h2>
                        </div>
                        <div class="p-6 flex flex-col items-center">
                            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 mb-4">
                                <img id="barcode-image"
                                    src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode(auth()->user()->po_code, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}"
                                    alt="Barcode" class="max-w-full h-auto">
                            </div>
                            <p class="text-sm font-mono text-gray-900 font-semibold mb-3">{{ auth()->user()->po_code }}</p>
                            <button onclick="downloadBarcode()"
                                class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <span class="material-icons text-sm mr-2">download</span>
                                Download Barcode
                            </button>
                        </div>
                    </div>

                    <!-- Member Info Card -->
                    <div class="bg-cyan-50 rounded-lg border border-cyan-200 p-4">
                        <div class="flex items-start gap-3">
                            <span class="material-icons text-cyan-500 flex-shrink-0">info</span>
                            <div>
                                <p class="text-sm font-semibold text-cyan-900 mb-1">Informasi Member</p>
                                <p class="text-xs text-cyan-700 leading-relaxed">
                                    Gunakan QR Code atau Barcode ini untuk melakukan transaksi di kasir. Simpan kode member Anda dengan aman.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Level Benefits Modal -->
    <div id="level-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeLevelModal()">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white" id="modal-title">Benefits Level</h3>
                    <button onclick="closeLevelModal()" class="text-white hover:text-gray-200 transition-colors">
                        <span class="material-icons">close</span>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6">
                <div class="flex items-center justify-center mb-6">
                    <img id="modal-icon" src="" alt="Level Icon" class="w-24 h-24 rounded-full border-4 border-yellow-400 shadow-lg">
                </div>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-4">
                    <div class="flex items-start">
                        <span class="material-icons text-yellow-600 mr-3">stars</span>
                        <div>
                            <p class="font-semibold text-yellow-900 mb-1">Keuntungan Level Ini:</p>
                            <p class="text-sm text-yellow-800" id="modal-benefit"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                    <div class="flex items-start">
                        <span class="material-icons text-gray-500 mr-3 text-sm">info</span>
                        <p class="text-xs text-gray-600" id="modal-description"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLevelModal(level) {
            const benefits = {
                1: 'Pendapatan points normal (1x)',
                2: 'Pendapatan points 1.5x',
                3: 'Pendapatan points 2.5x',
                4: 'Pendapatan points 5x'
            };

            const levelNames = {
                1: 'Rakyat Jelata',
                2: 'Bangsawan',
                3: 'Patih',
                4: 'Raja'
            };

            const levelIcons = {
                1: '/images/Rakyat_Jelata.png',
                2: '/images/Patih.png',
                3: '/images/Bangsawan.png',
                4: '/images/Raja.png'
            };

            const descriptions = {
                1: 'Level awal untuk semua member baru. Mulai kumpulkan XP dengan berbelanja!',
                2: 'Tingkatkan earnings Anda dengan mendapatkan 1.5x lebih banyak points di setiap transaksi.',
                3: 'Member premium dengan benefits lebih besar. Dapatkan 2.5x points!',
                4: 'Level tertinggi dengan keuntungan maksimal! Nikmati 5x points di setiap transaksi.'
            };

            document.getElementById('modal-title').textContent = `Level ${level}: ${levelNames[level]}`;
            document.getElementById('modal-icon').src = levelIcons[level];
            document.getElementById('modal-benefit').textContent = benefits[level];
            document.getElementById('modal-description').textContent = descriptions[level];
            document.getElementById('level-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLevelModal() {
            document.getElementById('level-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeLevelModal();
            }
        });

        function downloadQRCode() {
            const svg = document.querySelector('svg');
            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function () {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                const link = document.createElement('a');
                link.download = 'qrcode-{{ auth()->user()->po_code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };

            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        }

        function downloadBarcode() {
            const img = document.getElementById('barcode-image');
            const link = document.createElement('a');
            link.download = 'barcode-{{ auth()->user()->po_code }}.png';
            link.href = img.src;
            link.click();
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50';
            notification.innerHTML = `
                <span class="material-icons text-sm">check_circle</span>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(100%)';
                notification.style.transition = 'all 0.3s';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-section,
            .print-section * {
                visibility: visible;
            }

            .print-section {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
            /* Optimasi untuk layar sangat kecil */
    @media (max-width: 375px) {
    .profile-material-icons {
        font-size: 16px !important;
    }
}
    
    /* Prevent text overflow pada mobile */
    @media (max-width: 640px) {
        .text-center p {
            word-break: break-word;
        }
    }
    </style>
@endsection
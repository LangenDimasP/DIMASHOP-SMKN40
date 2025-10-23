@extends('layouts.app')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-cyan-500 mb-6">Dashboard Admin</h1>

        <!-- Action Buttons -->
        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.profit') }}" class="bg-cyan-500 hover:bg-cyan-600 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200 ease-in-out">
                Atur Keuntungan Toko
            </a>
            <button id="exportDataBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg shadow-md transition duration-200 ease-in-out">
                Ekspor Data
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold opacity-90">Total Pendapatan</h3>
                <p class="text-2xl font-bold mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-rose-500 to-rose-600 text-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold opacity-90">Total Transaksi</h3>
                <p class="text-2xl font-bold mt-1">{{ $totalTransactions }}</p>
            </div>
        </div>

        <!-- Export Modal -->
        <div id="exportModal" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-95 opacity-0 animate-fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-5">Pilih Periode Laporan</h3>

                <form action="{{ route('admin.export-sales-pdf') }}" method="GET" id="exportForm">
                    <!-- Report Type -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Laporan:</label>
                        <select name="report_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="sales">Laporan Penjualan</option>
                            <option value="transaction">Laporan Transaksi / Pembayaran</option>
                            <option value="stock">Laporan Stok Barang</option>
                            <option value="user">Laporan Pengguna / Member</option>
                            <option value="profit">Laporan Keuntungan / Laba-Rugi</option>
                            <option value="kasir_performance">Laporan Performa Kasir</option>
                        </select>
                    </div>

                    <!-- Period Selection -->
                    <fieldset class="mb-5">
                        <legend class="text-sm font-medium text-gray-700 mb-3">Periode:</legend>
                        <div class="space-y-2">
                            @foreach(['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'all' => 'All Time', 'custom' => 'Custom'] as $value => $label)
                                <label class="flex items-center">
                                    <input type="radio" name="period" value="{{ $value }}" {{ $value === 'daily' ? 'checked' : '' }} class="h-4 w-4 text-cyan-500 focus:ring-cyan-400">
                                    <span class="ml-2 text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <!-- Date Inputs -->
                    <div id="dailyDates" class="mb-5 hidden">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div id="weeklyDates" class="mb-5 hidden">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minggu Mulai</label>
                                <input type="week" name="start_week" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minggu Akhir</label>
                                <input type="week" name="end_week" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div id="monthlyDates" class="mb-5 hidden">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan Mulai</label>
                                <input type="month" name="start_month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan Akhir</label>
                                <input type="month" name="end_month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <div id="customDates" class="mb-5 hidden">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date_custom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                <input type="date" name="end_date_custom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" id="closeModal" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition duration-200">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition duration-200">
                            Ekspor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            const modal = document.getElementById('exportModal');
            const exportDataBtn = document.getElementById('exportDataBtn');
            const closeModal = document.getElementById('closeModal');

            const dailyDates = document.getElementById('dailyDates');
            const weeklyDates = document.getElementById('weeklyDates');
            const monthlyDates = document.getElementById('monthlyDates');
            const customDates = document.getElementById('customDates');

            // Show/hide date fields based on selected period
            document.querySelectorAll('input[name="period"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    dailyDates.classList.add('hidden');
                    weeklyDates.classList.add('hidden');
                    monthlyDates.classList.add('hidden');
                    customDates.classList.add('hidden');

                    if (radio.value === 'daily') dailyDates.classList.remove('hidden');
                    else if (radio.value === 'weekly') weeklyDates.classList.remove('hidden');
                    else if (radio.value === 'monthly') monthlyDates.classList.remove('hidden');
                    else if (radio.value === 'custom') customDates.classList.remove('hidden');
                });
            });

            // Modal controls
            exportDataBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                // Optional: add animation class
                setTimeout(() => {
                    const content = modal.querySelector('.animate-fade-in');
                    if (content) content.classList.remove('scale-95', 'opacity-0');
                    content?.classList.add('scale-100', 'opacity-100');
                }, 10);
            });

            closeModal.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        </script>

        <!-- Optional: Add fade-in animation via inline style or Tailwind plugin -->
        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
            .animate-fade-in {
                animation: fadeIn 0.25s ease-out forwards;
            }
        </style>
    </div>
@endsection
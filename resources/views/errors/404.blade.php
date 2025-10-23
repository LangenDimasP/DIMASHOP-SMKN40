@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        <div class="mb-6">
            <img src="{{ asset('images/404_page_not_found.png') }}" alt="404 Page Not Found"
                 class="mx-auto w-14 h-14 mb-4 select-none pointer-events-none"
                 draggable="false"
                 oncontextmenu="return false;">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">404</h1>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-600 mb-6">
                Maaf, halaman yang Anda cari tidak tersedia.
            </p>
        </div>
        <div class="space-y-2">
            <a href="{{ route('products') }}"
               class="inline-block px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-medium transition-colors">
                Jelajahi Produk
            </a>
            <br>
            <a href="/"
               class="inline-block text-cyan-500 hover:text-cyan-600 font-medium transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
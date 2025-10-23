@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-xl font-bold text-cyan-500">Dashboard Pelanggan</h1>
    <p>Kode PO: {{ auth()->user()->po_code }}</p>
    
    {{-- QR Code --}}
    {!! QrCode::size(200)->generate(auth()->user()->po_code) !!}
    
    {{-- Barcode --}}
    {!! (new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode(auth()->user()->po_code, $writer = 'png') !!}
    
    <h2 class="mt-4">Riwayat PO</h2>
    @foreach(auth()->user()->transactions as $trans)  <!-- Relasi di model User: hasMany(Transaction::class) -->
    <p>{{ $trans->unique_code }} - {{ $trans->status }}</p>
    @endforeach
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-2xl">
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
        <div class="text-green-600 text-6xl mb-4">✓</div>
        <h1 class="text-2xl font-bold text-green-800 mb-2">Pesanan Berhasil!</h1>
        <p class="text-gray-600 mb-6">Pesanan Anda telah diterima dan sedang diproses</p>
    </div>

    <div class="bg-white border rounded-lg p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Detail Pesanan</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600">Nomor Pesanan</p>
                <p class="font-semibold">#{{ $pesanan->id }}</p>
            </div>
            <div>
                <p class="text-gray-600">Nomor Meja</p>
                <p class="font-semibold">{{ $pesanan->nomor_meja }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status Pesanan</p>
                <p class="font-semibold capitalize">{{ $pesanan->status_pesanan }}</p>
            </div>
            <div>
                <p class="text-gray-600">Total Pembayaran</p>
                <p class="font-semibold text-green-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
            </div>
        </div>

        @if($pesanan->catatan)
        <div class="mb-6">
            <p class="text-gray-600">Catatan</p>
            <p class="font-semibold">{{ $pesanan->catatan }}</p>
        </div>
        @endif

        <h3 class="text-lg font-semibold mb-3">Items Pesanan</h3>
        <div class="space-y-3">
            @foreach($pesanan->detail as $detail)
            <div class="flex justify-between items-center border-b pb-3">
                <div>
                    <p class="font-semibold">{{ $detail->menu->nama_menu }}</p>
                    <p class="text-sm text-gray-600">{{ $detail->menu->deskripsi }}</p>
                </div>
                <div class="text-right">
                    <p>{{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</p>
                    <p class="font-semibold">Rp {{ number_format($detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold">Total</span>
                <span class="text-lg font-semibold text-green-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="text-center mt-6">
        <p class="text-gray-600 mb-4">Silakan menunggu, pesanan Anda akan segera diantar ke meja {{ $pesanan->nomor_meja }}</p>
        <a href="{{ route('user.order.create', ['table' => $pesanan->nomor_meja]) }}" 
           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 inline-block">
            Pesan Lagi
        </a>
    </div>
</div>
@endsection
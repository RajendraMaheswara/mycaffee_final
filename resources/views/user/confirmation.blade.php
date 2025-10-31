<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Jagongan Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        :root {
            --color-cream: #F5F1ED;
            --color-brown-light: #C4A574;
            --color-brown: #8B6B47;
            --color-brown-dark: #5C4033;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #5C4033 0%, #8B6B47 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body>
    
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <!-- Success Icon -->
            <div class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center bg-green-100">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-serif font-bold text-gray-800 mb-2">Pesanan Berhasil!</h1>
            <p class="text-gray-600 mb-8">Terima kasih telah memesan di Jagongan Coffee</p>

            <!-- Order Details -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6 text-left">
                <div class="flex justify-between items-center mb-4 pb-4 border-b">
                    <div>
                        <p class="text-sm text-gray-600">Nomor Pesanan</p>
                        <p class="text-2xl font-bold" style="color: #5C4033">#{{ $pesanan->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Nomor Meja</p>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold mt-1" style="background-color: #5C4033">
                            {{ $pesanan->nomor_meja }}
                        </div>
                    </div>
                </div>

                @if($pesanan->catatan)
                <div class="mb-4 pb-4 border-b">
                    <p class="text-sm text-gray-600 mb-1">📝 Catatan Pesanan</p>
                    <p class="text-gray-800 font-medium">{{ $pesanan->catatan }}</p>
                </div>
                @endif

                <div class="space-y-3 mb-4">
                    @foreach($pesanan->detail as $detail)
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $detail->menu->nama_menu }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="font-semibold" style="color: #5C4033">
                            Rp {{ number_format($detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 space-y-2">
                    @php
                        $subtotal = $pesanan->total_harga / 1.1;
                        $tax = $pesanan->total_harga - $subtotal;
                    @endphp
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Pajak (10%):</span>
                        <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-800 pt-2 border-t">
                        <span>Total:</span>
                        <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left">
                <p class="text-sm text-blue-700">
                    <strong>Status:</strong> {{ ucfirst($pesanan->status_pesanan) }} - 
                    Pesanan Anda sedang diproses. Mohon tunggu, pesanan akan segera diantar ke meja {{ $pesanan->nomor_meja }}.
                </p>
            </div>

            <div class="flex gap-3 justify-center">
                <a href="{{ route('user.order.create', ['table' => $pesanan->nomor_meja]) }}" 
                   class="inline-block px-8 py-3 rounded-lg font-semibold text-white transition hover:opacity-90"
                   style="background-color: #5C4033">
                    🛒 Pesan Lagi
                </a>
                <a href="{{ url('/') }}" 
                   class="inline-block px-8 py-3 rounded-lg font-semibold transition hover:opacity-90"
                   style="background-color: #8B6B47; color: white">
                    🏠 Kembali ke Home
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-white text-sm">
            <p>&copy; {{ date('Y') }} Jagongan Coffee. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
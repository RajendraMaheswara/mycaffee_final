<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Jagongan Coffee (Meja {{ $nomor_meja }})</title>
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
            background-color: var(--color-cream);
        }
        
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
        
        html {
            scroll-behavior: smooth;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-brown);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-brown-dark);
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="fixed top-0 w-full backdrop-blur-sm z-50 shadow-sm" style="background-color: rgba(245, 241, 237, 0.95)">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #5C4033; color: #F5F1ED">
                        <span class="text-2xl font-bold">J</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Jagongan Coffee</h1>
                        <p class="text-xs" style="color: #8B6B47">Meja {{ $nomor_meja }}</p>
                    </div>
                </div>
                <button onclick="clearCart()" class="px-4 py-2 rounded-lg transition text-sm hover:opacity-90" style="background-color: #8B6B47; color: #F5F1ED">
                    Clear Cart
                </button>
            </div>
        </div>
    </nav>

    <div class="pt-28 pb-12 px-6">
        <div class="max-w-7xl mx-auto">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">
                
                <!-- Menu Section -->
                <div class="lg:col-span-2">
                    <h2 class="text-3xl font-serif text-gray-800 mb-6">Menu Kami</h2>
                    
                    <form method="POST" action="{{ route('user.order.store') }}" id="orderForm">
                        @csrf
                        <input type="hidden" name="nomor_meja" value="{{ $nomor_meja }}">
                        <input type="hidden" name="items" id="items-input" value="[]">
                        
                        <!-- Catatan Pesanan -->
                        <div class="mb-6 bg-white rounded-xl shadow-lg p-4">
                            <label class="block mb-2 font-semibold text-gray-800">
                                📝 Catatan Pesanan (opsional)
                            </label>
                            <textarea name="catatan" 
                                      class="border border-gray-300 p-3 w-full rounded-lg focus:ring-2 focus:ring-offset-2 outline-none" 
                                      style="focus:ring-color: #8B6B47"
                                      placeholder="Contoh: Kopi kurang manis, es banyak..." 
                                      rows="2">{{ old('catatan') }}</textarea>
                        </div>
                        
                        <div class="grid sm:grid-cols-2 gap-4 mb-6">
                            @foreach($menus as $menu)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                                <div class="aspect-square bg-gray-200 relative">
                                    @if($menu->gambar)
                                        <img src="{{ asset('storage/' . $menu->gambar) }}" 
                                             alt="{{ $menu->nama_menu }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">☕</div>
                                    @endif
                                    <span class="absolute top-2 right-2 px-3 py-1 rounded-full text-xs font-medium text-white" 
                                          style="background-color: #5C4033">
                                        {{ ucfirst($menu->kategori ?? 'Menu') }}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800 mb-1">{{ $menu->nama_menu }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $menu->deskripsi }}</p>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-bold" style="color: #5C4033">
                                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                        </span>
                                        <span class="text-sm text-gray-600">Stok: {{ $menu->stok }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" 
                                                onclick="decreaseQty({{ $menu->id }})" 
                                                class="decrease-btn w-8 h-8 rounded-full flex items-center justify-center text-white hover:opacity-90 transition" 
                                                style="background-color: #8B6B47"
                                                data-id="{{ $menu->id }}">
                                            -
                                        </button>
                                        <span class="quantity-display w-16 text-center font-semibold" 
                                              data-id="{{ $menu->id }}"
                                              id="qty_{{ $menu->id }}">0</span>
                                        <button type="button" 
                                                onclick="increaseQty({{ $menu->id }}, {{ $menu->stok }})" 
                                                class="increase-btn w-8 h-8 rounded-full flex items-center justify-center text-white hover:opacity-90 transition" 
                                                style="background-color: #5C4033"
                                                data-id="{{ $menu->id }}">
                                            +
                                        </button>
                                    </div>
                                    <input type="hidden" id="price_{{ $menu->id }}" value="{{ $menu->harga }}">
                                    <input type="hidden" id="name_{{ $menu->id }}" value="{{ $menu->nama_menu }}">
                                    <input type="hidden" id="stock_{{ $menu->id }}" value="{{ $menu->stok }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>

                <!-- Cart Section -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-2xl shadow-xl p-6">
                            <h2 class="text-2xl font-serif text-gray-800 mb-4">🛒 Keranjang</h2>
                            
                            <div id="cartItems" class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                                <p class="text-gray-500 text-center py-8">Keranjang kosong</p>
                            </div>

                            <div class="border-t pt-4 space-y-2 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Pajak (10%):</span>
                                    <span id="tax">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-xl font-bold text-gray-800 pt-2 border-t">
                                    <span>Total:</span>
                                    <span id="total">Rp 0</span>
                                </div>
                            </div>

                            <button type="submit" 
                                    form="orderForm" 
                                    id="submitBtn" 
                                    disabled
                                    class="w-full py-4 rounded-lg font-semibold text-white transition opacity-50 hover:opacity-90"
                                    style="background-color: #5C4033">
                                🛒 Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cart = {};

        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function increaseQty(id, maxStock) {
            const currentQty = cart[id] ? cart[id].qty : 0;
            if (currentQty < maxStock) {
                const name = document.getElementById('name_' + id).value;
                const price = parseInt(document.getElementById('price_' + id).value);
                
                if (!cart[id]) {
                    cart[id] = {
                        id: id,
                        name: name,
                        price: price,
                        qty: 0
                    };
                }
                cart[id].qty++;
                updateQuantityDisplay(id);
                updateCart();
            } else {
                alert('Stok tidak mencukupi');
            }
        }

        function decreaseQty(id) {
            if (cart[id] && cart[id].qty > 0) {
                cart[id].qty--;
                if (cart[id].qty === 0) {
                    delete cart[id];
                }
                updateQuantityDisplay(id);
                updateCart();
            }
        }

        function updateQuantityDisplay(id) {
            const display = document.getElementById('qty_' + id);
            if (display) {
                display.textContent = cart[id] ? cart[id].qty : 0;
            }
        }

        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            let items = [];
            let subtotal = 0;

            for (const id in cart) {
                if (cart[id].qty > 0) {
                    const itemTotal = cart[id].price * cart[id].qty;
                    items.push({
                        name: cart[id].name,
                        qty: cart[id].qty,
                        price: cart[id].price,
                        total: itemTotal,
                        id: id
                    });
                    subtotal += itemTotal;
                }
            }

            // Update cart display
            if (items.length === 0) {
                cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Keranjang kosong</p>';
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').classList.add('opacity-50');
                document.getElementById('submitBtn').innerHTML = '🛒 Pesan Sekarang';
            } else {
                cartItems.innerHTML = items.map(item => `
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 text-sm">${item.name}</p>
                            <p class="text-xs text-gray-600">${item.qty} x ${formatRupiah(item.price)}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <p class="font-semibold" style="color: #5C4033">${formatRupiah(item.total)}</p>
                            <button type="button" onclick="removeItem(${item.id})" class="text-red-600 hover:text-red-800 text-sm ml-2">
                                ✕
                            </button>
                        </div>
                    </div>
                `).join('');
                
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').classList.remove('opacity-50');
                document.getElementById('submitBtn').innerHTML = '🛒 Pesan Sekarang (' + items.length + ' item)';
            }

            // Update totals
            const tax = subtotal * 0.1;
            const total = subtotal + tax;

            document.getElementById('subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('tax').textContent = formatRupiah(tax);
            document.getElementById('total').textContent = formatRupiah(total);

            // Update hidden input for form submission
            const itemsArray = items.map(item => ({
                id_menu: parseInt(item.id),
                jumlah: item.qty
            }));
            document.getElementById('items-input').value = JSON.stringify(itemsArray);
        }

        function removeItem(id) {
            delete cart[id];
            updateQuantityDisplay(id);
            updateCart();
        }

        function clearCart() {
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                for (const id in cart) {
                    delete cart[id];
                    updateQuantityDisplay(id);
                }
                updateCart();
            }
        }

        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal satu menu');
            }
        });

        // Initialize
        updateCart();
    </script>
</body>
</html>
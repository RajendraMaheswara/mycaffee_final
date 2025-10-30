@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Buat Pesanan - Meja {{ $nomor_meja }}</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-2 mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.order.store') }}">
        @csrf
        <input type="hidden" name="nomor_meja" value="{{ $nomor_meja }}">
        
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Catatan Pesanan (opsional)</label>
            <textarea name="catatan" class="border p-2 w-full rounded" placeholder="Contoh: Kopi kurang manis, es banyak..." rows="3">{{ old('catatan') }}</textarea>
        </div>

        <h2 class="text-xl font-semibold mb-4">Menu Tersedia</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($menus as $menu)
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <h3 class="font-bold text-lg text-gray-800">{{ $menu->nama_menu }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $menu->deskripsi }}</p>
                    <div class="mt-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-green-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">Stok: {{ $menu->stok }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" class="decrease-btn bg-gray-200 text-gray-700 px-3 py-1 rounded" data-id="{{ $menu->id }}">-</button>
                            <span class="quantity-display" data-id="{{ $menu->id }}">0</span>
                            <button type="button" class="increase-btn bg-gray-200 text-gray-700 px-3 py-1 rounded" data-id="{{ $menu->id }}">+</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Keranjang Pesanan -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-3">Keranjang Pesanan</h3>
            <div id="cart-items" class="space-y-2">
                <p class="text-gray-500 text-center py-4">Belum ada menu yang dipilih</p>
            </div>
            <div id="cart-total" class="mt-4 pt-4 border-t hidden">
                <p class="text-lg font-semibold">Total: Rp <span id="total-amount">0</span></p>
            </div>
        </div>

        <input type="hidden" name="items" id="items-input" value="[]">

        <div class="flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800">Kembali ke Home</a>
            <button type="submit" id="submit-btn" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                Pesan Sekarang
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = {};
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const totalAmount = document.getElementById('total-amount');
    const itemsInput = document.getElementById('items-input');
    const submitBtn = document.getElementById('submit-btn');

    // Update quantity display
    function updateQuantityDisplay(menuId) {
        const display = document.querySelector(`.quantity-display[data-id="${menuId}"]`);
        if (display) {
            display.textContent = cart[menuId] ? cart[menuId].qty : 0;
        }
    }

    // Update cart display
    function updateCart() {
        cartItems.innerHTML = '';
        let total = 0;

        if (Object.keys(cart).length === 0) {
            cartItems.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada menu yang dipilih</p>';
            cartTotal.classList.add('hidden');
            submitBtn.disabled = true;
        } else {
            for (const menuId in cart) {
                const item = cart[menuId];
                const subtotal = item.price * item.qty;
                total += subtotal;

                const itemElement = document.createElement('div');
                itemElement.className = 'flex justify-between items-center bg-white p-3 rounded border';
                itemElement.innerHTML = `
                    <div>
                        <p class="font-semibold">${item.name}</p>
                        <p class="text-sm text-gray-600">Rp ${item.price.toLocaleString()} x ${item.qty}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="font-semibold">Rp ${subtotal.toLocaleString()}</p>
                        <button type="button" class="remove-item text-red-600 hover:text-red-800 ml-2" data-id="${menuId}">
                            Hapus
                        </button>
                    </div>
                `;
                cartItems.appendChild(itemElement);
            }
            totalAmount.textContent = total.toLocaleString();
            cartTotal.classList.remove('hidden');
            submitBtn.disabled = false;
        }

        // Update hidden input
        const itemsArray = Object.values(cart).map(item => ({
            id_menu: parseInt(item.id),
            jumlah: item.qty
        }));
        itemsInput.value = JSON.stringify(itemsArray);
    }

    // Add event listeners for quantity buttons
    document.querySelectorAll('.increase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const menuId = this.dataset.id;
            const menuElement = this.closest('.bg-white');
            const menuName = menuElement.querySelector('h3').textContent;
            const menuPrice = parseInt(menuElement.querySelector('.text-green-600').textContent.replace(/[^\d]/g, ''));
            const stock = parseInt(menuElement.querySelector('.text-gray-500').textContent.replace('Stok: ', ''));

            if (!cart[menuId]) {
                cart[menuId] = {
                    id: menuId,
                    name: menuName,
                    price: menuPrice,
                    qty: 0
                };
            }

            if (cart[menuId].qty < stock) {
                cart[menuId].qty++;
                updateQuantityDisplay(menuId);
                updateCart();
            } else {
                alert('Stok tidak mencukupi');
            }
        });
    });

    document.querySelectorAll('.decrease-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const menuId = this.dataset.id;
            
            if (cart[menuId] && cart[menuId].qty > 0) {
                cart[menuId].qty--;
                if (cart[menuId].qty === 0) {
                    delete cart[menuId];
                }
                updateQuantityDisplay(menuId);
                updateCart();
            }
        });
    });

    // Remove item from cart
    cartItems.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const menuId = e.target.dataset.id;
            delete cart[menuId];
            updateQuantityDisplay(menuId);
            updateCart();
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu menu');
        }
    });
});
</script>
@endsection
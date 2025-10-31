<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Item Pesanan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .btn { padding: 10px 15px; text-decoration: none; color: white; border-radius: 5px; border: none; cursor: pointer; font-size: 1em; margin: 5px 0; }
        .btn-back { background-color: #6c757d; }
        .btn-primary { background-color: #007bff; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group select, .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .message { padding: 10px; border-radius: 5px; margin: 10px 0; display: none; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="container">
        <a href="{{ route('kasir.pesanan.detail', ['id' => $id_pesanan]) }}" class="btn btn-back">&laquo; Kembali ke Detail</a>

        <h1>Tambah Item untuk Pesanan #{{ $id_pesanan }}</h1>

        <div id="message-container" class="message"></div>
        <p id="loading">Memuat data...</p>

        <form id="form-tambah-item" style="display: none;">

            <div class="form-group">
                <label for="id_menu">Nama Item Menu</label>
                <select id="id_menu" name="id_menu" required>
                    <option value="">Pilih Menu...</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah">Kuantitas</label>
                <input type="number" id="jumlah" name="jumlah" min="1" value="1" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Item</button>
        </form>
    </div>

    <script>
        const pesananId = {{ $id_pesanan }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const baseUrl = "{{ url('/') }}";

        const formTambahItem = document.getElementById('form-tambah-item');
        const loadingEl = document.getElementById('loading');
        const messageEl = document.getElementById('message-container');
        const menuSelect = document.getElementById('id_menu');

        document.addEventListener('DOMContentLoaded', loadMenu);
        formTambahItem.addEventListener('submit', handleAddItem);

        async function loadMenu() {
            try {
                const response = await fetch(`${baseUrl}/api/kasir/menu`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) { throw new Error('Gagal memuat daftar menu.'); }

                const result = await response.json();

                if (result.success && result.data) {

                    // ▼▼▼ DI SINILAH PERBAIKANNYA ▼▼▼
                    result.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;

                        // FIX: Gunakan 'item.nama_menu' (dari seeder) BUKAN 'item.nama'
                        option.textContent = `${item.nama_menu} (${formatRupiah(item.harga)})`;

                        menuSelect.appendChild(option);
                    });
                    // ▲▲▲ DI SINILAH PERBAIKANNYA ▲▲▲

                    formTambahItem.style.display = 'block';
                    loadingEl.style.display = 'none';
                }

            } catch (error) {
                showMessage(error.message, 'error');
                loadingEl.textContent = 'Gagal memuat menu. Coba refresh.';
            }
        }

        async function handleAddItem(event) {
            event.preventDefault();
            showLoading(true, 'Menyimpan item...');

            const formData = new FormData(formTambahItem);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`${baseUrl}/api/kasir/pesanan/${pesananId}/add-item`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showMessage('Item berhasil ditambahkan!', 'success');
                    formTambahItem.reset();
                    menuSelect.focus();
                    showLoading(false);
                } else {
                    let errorMsg = result.message || 'Gagal menambah item.';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).join(', ');
                    }
                    throw new Error(errorMsg);
                }
            } catch (error) {
                showLoading(false);
                showMessage(error.message, 'error');
            }
        }

        // --- Fungsi Helper ---
        function showLoading(show, text = 'Memuat...') {
            loadingEl.textContent = text;
            loadingEl.style.display = show ? 'block' : 'none';
        }

        function showMessage(message, type = 'success') {
            messageEl.textContent = message;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            setTimeout(() => { messageEl.style.display = 'none'; }, 3000);
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }
    </script>
</body>
</html>

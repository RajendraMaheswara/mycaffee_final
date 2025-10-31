<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pesanan</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1, h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row td { font-weight: bold; font-size: 1.1em; }
        .btn { padding: 10px 15px; text-decoration: none; color: white; border-radius: 5px; border: none; cursor: pointer; font-size: 1em; margin: 5px; }
        .btn-back { background-color: #6c757d; }
        .btn-success { background-color: #28a745; }
        .btn-info { background-color: #17a2b8; }
        .btn-primary { background-color: #007bff; }
        .section { margin-top: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        #update-status-form { max-width: 400px; }
        .message { padding: 10px; border-radius: 5px; margin: 10px 0; display: none; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; background-color: #fff; }
            .container { box-shadow: none; padding: 0; }
            h1 { font-size: 1.5em; text-align: center; }
            h2 { font-size: 1.2em; border-bottom: 1px dashed #000; }
            table { width: 100%; font-size: 0.9em; }
            th, td { border: none; border-bottom: 1px dashed #ccc; padding: 5px; }
            .total-row td { font-size: 1em; }
            #info-pesanan { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('kasir.dashboard') }}" class="btn btn-back no-print">&laquo; Kembali ke Dashboard</a>

        <h1>Detail Pesanan #<span id="pesanan-id">{{ $id_pesanan }}</span></h1>

        <div id="message-container" class="message no-print"></div>
        <p id="loading" class="no-print">Memuat data...</p>

        <div class="section" id="info-pesanan">
            <h2>Informasi Pesanan</h2>
            <p><strong>Nomor Meja:</strong> <span id="nomor-meja">...</span></p>
            <p><strong>Status Pesanan:</strong> <span id="current-status-pesanan">...</span></p>
            <p><strong>Status Pembayaran:</strong> <span id="current-status-pembayaran">...</span></p>
            <p><strong>Kasir (ID):</strong> <span id="kasir-id">...</span></p>
        </div>

        <div class="section" id="items-section">
            <h2>Item Pesanan</h2>
            <a href="{{ route('kasir.pesanan.tambah_item', ['id' => $id_pesanan]) }}" class="btn btn-primary no-print">
                + Tambah Item
            </a>

            <table id="items-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Kuantitas</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="items-tbody"></tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Total Keseluruhan:</td>
                        <td id="total-harga">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="section no-print" id="update-status-form">
            <h2>Update Status</h2>
            <form id="form-update-status">
                <div class="form-group">
                    <label for="status_pesanan">Status Pesanan</label>
                    <select id="status_pesanan" name="status_pesanan">
                        <option value="diproses">Diproses</option>
                        <option value="diantar">Diantar</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status_pembayaran">Status Pembayaran</label>
                    <select id="status_pembayaran" name="status_pembayaran">
                        <option value="belum dibayar">Belum Dibayar</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update Status</button>
            </form>
        </div>

        <div class="section no-print" style="text-align: center; margin-top: 30px;">
            <button id="btn-cetak" class="btn btn-info">Cetak Struk Pembelian</button>
        </div>
    </div>

    <script>
        const pesananId = document.getElementById('pesanan-id').textContent;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const baseUrl = "{{ url('/') }}";

        const loadingEl = document.getElementById('loading');
        const messageEl = document.getElementById('message-container');
        const nomorMejaEl = document.getElementById('nomor-meja');
        const currentStatusPesananEl = document.getElementById('current-status-pesanan');
        const currentStatusPembayaranEl = document.getElementById('current-status-pembayaran');
        const kasirIdEl = document.getElementById('kasir-id');
        const itemsTbody = document.getElementById('items-tbody');
        const totalHargaEl = document.getElementById('total-harga');
        const statusPesananSelect = document.getElementById('status_pesanan');
        const statusPembayaranSelect = document.getElementById('status_pembayaran');
        const formUpdateStatus = document.getElementById('form-update-status');
        const btnCetak = document.getElementById('btn-cetak');

        async function loadPesananDetails() {
            showLoading(true, 'Memuat data...');
            try {
                // API-mu memanggil 'items.menu' (sudah benar)
                const response = await fetch(`${baseUrl}/api/kasir/pesanan/${pesananId}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) { throw new Error('Gagal mengambil data pesanan.'); }
                const result = await response.json();
                if (result.success) {
                    const pesanan = result.data;
                    nomorMejaEl.textContent = pesanan.nomor_meja || '-';
                    currentStatusPesananEl.textContent = pesanan.status_pesanan;
                    currentStatusPembayaranEl.textContent = pesanan.status_pembayaran;
                    kasirIdEl.textContent = pesanan.id_pengguna ? pesanan.id_pengguna : 'Belum Selesai';
                    statusPesananSelect.value = pesanan.status_pesanan;
                    statusPembayaranSelect.value = pesanan.status_pembayaran;

                    itemsTbody.innerHTML = '';

                    // ▼▼▼ DI SINILAH PERBAIKANNYA ▼▼▼
                    pesanan.items.forEach(item => {
                        const tr = document.createElement('tr');

                        // FIX: item.menu.nama_menu (untuk nama)
                        // FIX: item.jumlah (untuk kuantitas)
                        // FIX: item.harga_satuan (untuk harga)

                        tr.innerHTML = `
                            <td>${item.menu.nama_menu}</td>
                            <td>${item.jumlah}</td>
                            <td>${formatRupiah(item.harga_satuan)}</td>
                            <td>${formatRupiah(item.harga_satuan * item.jumlah)}</td>
                        `;
                        itemsTbody.appendChild(tr);
                    });
                    // ▲▲▲ DI SINILAH PERBAIKANNYA ▲▲▲

                    totalHargaEl.textContent = formatRupiah(pesanan.total_harga);
                    showLoading(false);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showLoading(false);
                showMessage(error.message, 'error');
            }
        }

        async function handleUpdateStatus(event) {
            event.preventDefault();
            showLoading(true, 'Mengupdate status...');
            const data = {
                status_pesanan: statusPesananSelect.value,
                status_pembayaran: statusPembayaranSelect.value
            };
            try {
                const response = await fetch(`${baseUrl}/api/kasir/pesanan/${pesananId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showMessage('Status berhasil diupdate!', 'success');
                    await loadPesananDetails();
                } else {
                    throw new Error(result.message || 'Gagal mengupdate status.');
                }
            } catch (error) {
                showLoading(false);
                showMessage(error.message, 'error');
            }
        }

        function handleCetak() { window.print(); }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

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

        document.addEventListener('DOMContentLoaded', loadPesananDetails);
        formUpdateStatus.addEventListener('submit', handleUpdateStatus);
        btnCetak.addEventListener('click', handleCetak);
    </script>
</body>
</html>

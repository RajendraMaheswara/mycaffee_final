<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kasir</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .logout-form { display: inline; }
        .btn { padding: 5px 10px; text-decoration: none; background-color: #007bff; color: white; border-radius: 4px; border: none; cursor: pointer; }
        .btn-detail { background-color: #17a2b8; }
        #loading { font-weight: bold; }
    </style>
</head>
<body>

    <h1>Selamat datang di Dashboard Kasir</h1>

    @if(Auth::check())
        <p>Halo, {{ Auth::user()->name ?? Auth::user()->nama ?? 'User' }} ({{ Auth::user()->role ?? Auth::user()->level ?? 'Kasir' }})</p>
    @endif

    <form action="{{ route('logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="btn">Logout</button>
    </form>

    <hr style="margin-top: 20px;">

    <h2>Daftar Pesanan Aktif</h2>
    <table id="tabel-pesanan">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nomor Meja</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tbody-pesanan">
            </tbody>
    </table>
    <p id="loading">Memuat data pesanan...</p>
    <p id="error-message" style="color: red;"></p>

    <script>
        const baseUrl = "{{ url('/') }}";
        document.addEventListener('DOMContentLoaded', function() {
            fetchPesanan();
        });

        // ▼▼▼ FUNGSI "handleBuatPesanan" SUDAH DIHAPUS DARI SINI ▼▼▼

        async function fetchPesanan() {
            const tbody = document.getElementById('tbody-pesanan');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('error-message');

            try {
                const response = await fetch(`${baseUrl}/api/kasir/pesanan`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
                const result = await response.json();
                tbody.innerHTML = '';
                errorMessage.innerHTML = '';

                if (result.success && result.data && result.data.data && result.data.data.length > 0) {
                    loading.style.display = 'none';
                    result.data.data.forEach(pesanan => {
                        const tr = document.createElement('tr');
                        const totalHargaRp = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(pesanan.total_harga);

                        tr.innerHTML = `
                            <td>${pesanan.id}</td>
                            <td>${pesanan.customer_name || '-'}</td>
                            <td>${pesanan.nomor_meja || '-'}</td>
                            <td>${pesanan.status_pesanan}</td>
                            <td>${pesanan.status_pembayaran}</td>
                            <td>${totalHargaRp}</td>
                            <td>
                                <a href="${baseUrl}/kasir/pesanan/${pesanan.id}" class="btn btn-detail">Lihat Detail</a>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    loading.innerText = 'Tidak ada data pesanan aktif.';
                }
            } catch (error) {
                console.error('Error fetching pesanan:', error);
                loading.style.display = 'none';
                errorMessage.innerText = 'Gagal memuat data. Coba refresh halaman.';
            }
        }
    </script>
</body>
</html>

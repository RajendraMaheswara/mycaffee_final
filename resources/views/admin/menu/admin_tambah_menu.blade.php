<!DOCTYPE html>
<html>
<head>
    <title>Tambah Menu</title>
</head>
<body>
    <h2>Tambah Menu Baru</h2>

    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p>Nama Menu: <input type="text" name="nama_menu"></p>
        <p>Deskripsi: <textarea name="deskripsi"></textarea></p>
        <p>Harga: <input type="number" name="harga"></p>
        <p>Stok: <input type="number" name="stok"></p>
        <p>Kategori:
            <select name="kategori">
                <option value="kopi">Kopi</option>
                <option value="snack">Snack</option>
                <option value="makanan">Makanan</option>
            </select>
        </p>
        <p>Gambar: <input type="file" name="gambar"></p>
        <button type="submit">Simpan</button>
    </form>

    <p><a href="{{ route('admin.menu.index') }}">Kembali</a></p>
</body>
</html>

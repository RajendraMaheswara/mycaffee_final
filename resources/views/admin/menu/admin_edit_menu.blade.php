<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu</title>
</head>
<body>
    <h2>Edit Menu</h2>

    <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p>Nama Menu: <input type="text" name="nama_menu" value="{{ $menu->nama_menu }}"></p>
        <p>Deskripsi: <textarea name="deskripsi">{{ $menu->deskripsi }}</textarea></p>
        <p>Harga: <input type="number" name="harga" value="{{ $menu->harga }}"></p>
        <p>Stok: <input type="number" name="stok" value="{{ $menu->stok }}"></p>
        <p>Kategori:
            <select name="kategori">
                <option value="kopi" {{ $menu->kategori == 'kopi' ? 'selected' : '' }}>Kopi</option>
                <option value="snack" {{ $menu->kategori == 'snack' ? 'selected' : '' }}>Snack</option>
                <option value="makanan" {{ $menu->kategori == 'makanan' ? 'selected' : '' }}>Makanan</option>
            </select>
        </p>
        <p>
            Gambar:
            @if($menu->gambar)
                <img src="{{ asset('storage/'.$menu->gambar) }}" width="80"><br>
            @endif
            <input type="file" name="gambar">
        </p>
        <button type="submit">Update</button>
    </form>

    <p><a href="{{ route('admin.menu.index') }}">Kembali</a></p>
</body>
</html>

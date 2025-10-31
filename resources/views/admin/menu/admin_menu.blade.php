<!DOCTYPE html>
<html>
<head>
    <title>Admin - Menu</title>
</head>
<body>
    <h2>Daftar Menu</h2>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <a href="{{ route('admin.menu.create') }}">+ Tambah Menu</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Nama Menu</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
        @foreach($menus as $menu)
        <tr>
            <td>{{ $menu->id_menu }}</td>
            <td>{{ $menu->nama_menu }}</td>
            <td>{{ ucfirst($menu->kategori) }}</td>
            <td>Rp{{ number_format($menu->harga,0,',','.') }}</td>
            <td>{{ $menu->stok }}</td>
            <td>
                @if($menu->gambar)
                    <img src="{{ asset('storage/'.$menu->gambar) }}" width="60">
                @endif
            </td>
            <td>
                <a href="{{ route('admin.menu.edit', $menu->id) }}">Edit</a> |
                <a href="{{ route('admin.menu.destroy', $menu->id) }}" onclick="return confirm('Yakin hapus?')">Hapus</a
                >
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>

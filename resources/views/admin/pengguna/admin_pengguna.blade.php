<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pengguna</title>
</head>
<body>
    <h2>Daftar Pengguna</h2>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <a href="{{ route('admin.pengguna.create') }}">+ Tambah Pengguna</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Peran</th>
            <th>Aksi</th>
        </tr>
        @foreach($pengguna as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->username }}</td>
            <td>{{ $p->nama_lengkap }}</td>
            <td>{{ ucfirst($p->peran) }}</td>
            <td>
                <a href="{{ route('admin.pengguna.edit', $p->id) }}">Edit</a> |
                <a href="{{ route('admin.pengguna.destroy', $p->id) }}" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>

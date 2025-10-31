<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pengguna</title>
</head>
<body>
    <h2>Tambah Pengguna Baru</h2>

    <form action="{{ route('admin.pengguna.store') }}" method="POST">
        @csrf
        <p>Username: <input type="text" name="username"></p>
        <p>Password: <input type="password" name="password"></p>
        <p>Nama Lengkap: <input type="text" name="nama_lengkap"></p>
        <p>Peran:
            <select name="peran">
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
            </select>
        </p>
        <button type="submit">Simpan</button>
    </form>

    <p><a href="{{ route('admin.pengguna.index') }}">Kembali</a></p>
</body>
</html>

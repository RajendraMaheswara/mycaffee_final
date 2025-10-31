<!DOCTYPE html>
<html>
<head>
    <title>Edit Pengguna</title>
</head>
<body>
    <h2>Edit Pengguna</h2>

    <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST">
        @csrf
        <p>Username: <input type="text" name="username" value="{{ $pengguna->username }}"></p>
        <p>Password (kosongkan jika tidak diubah): <input type="password" name="password"></p>
        <p>Nama Lengkap: <input type="text" name="nama_lengkap" value="{{ $pengguna->nama_lengkap }}"></p>
        <p>Peran:
            <select name="peran">
                <option value="admin" {{ $pengguna->peran == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="kasir" {{ $pengguna->peran == 'kasir' ? 'selected' : '' }}>Kasir</option>
            </select>
        </p>
        <button type="submit">Update</button>
    </form>

    <p><a href="{{ route('admin.pengguna.index') }}">Kembali</a></p>
</body>
</html>

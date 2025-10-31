<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }
        h1 {
            color: #333;
        }
        .menu-container {
            display: flex;
            gap: 30px;
            margin-top: 40px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            width: 220px;
            transition: 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        a {
            text-decoration: none;
            color: white;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            border-radius: 6px;
            background: #007bff;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Selamat Datang, Admin!</h1>
    <p>Pilih menu di bawah untuk mengelola data:</p>

    <div class="menu-container">
        <div class="card">
            <h3>Manajemen Menu</h3>
            <p>Kelola daftar menu kopi, makanan, dan snack.</p>
            <a href="{{ route('admin.menu.index') }}" class="btn">Kelola Menu</a>
        </div>

        <div class="card">
            <h3>Manajemen Pengguna</h3>
            <p>Kelola data akun admin dan kasir.</p>
            <a href="{{ route('admin.pengguna.index') }}" class="btn">Kelola Pengguna</a>
        </div>
    </div>

</body>
</html>

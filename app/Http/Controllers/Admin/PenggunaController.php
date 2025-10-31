<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    // tampilkan semua pengguna
    public function index()
    {
        $pengguna = Pengguna::latest()->get();
        return view('admin.pengguna.admin_pengguna', compact('pengguna'));
    }

    // tampilkan form tambah
    public function create()
    {
        return view('admin.pengguna.admin_tambah_pengguna');
    }

    // simpan pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:pengguna,username',
            'password' => 'required|min:4',
            'nama_lengkap' => 'nullable|string',
            'peran' => 'required|in:admin,kasir',
        ]);

        Pengguna::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'peran' => $request->peran,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // tampilkan form edit
    public function edit($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        return view('admin.pengguna.admin_edit_pengguna', compact('pengguna'));
    }

    // update pengguna
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:pengguna,username,' . $pengguna->id,
            'password' => 'nullable|min:4',
            'nama_lengkap' => 'nullable|string',
            'peran' => 'required|in:admin,kasir',
        ]);

        $pengguna->update([
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'peran' => $request->peran,
            'password' => $request->password
                ? Hash::make($request->password)
                : $pengguna->password,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    // hapus pengguna
    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}

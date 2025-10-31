<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        return view('admin.admin_menu', compact('menus'));
    }

    public function create()
    {
        return view('admin.admin_tambah_menu');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gambar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_menu'  => 'required|string|max:100',
            'deskripsi'  => 'nullable|string',
            'harga'      => 'required|numeric|min:0',
            'stok'       => 'required|integer|min:0',
            'kategori'   => 'required|in:Snack,Makanan,Kopi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $gambar = $request->file('gambar');
        $gambar->storeAs('menu', $gambar->hashName());

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'kategori'  => $request->kategori,
            'gambar'    => $gambar->hashName(),
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('admin.admin_edit_menu', compact('menu'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'gambar'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_menu'  => 'required|string|max:100',
            'deskripsi'  => 'nullable|string',
            'harga'      => 'required|numeric|min:0',
            'stok'       => 'required|integer|min:0',
            'kategori'   => 'required|in:Snack,Makanan,Kopi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('gambar')) {
            Storage::delete('menu/' . basename($menu->gambar));
            $gambar = $request->file('gambar');
            $gambar->storeAs('menu', $gambar->hashName());
            $menu->gambar = $gambar->hashName();
        }

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'kategori'  => $request->kategori,
            'gambar'    => $menu->gambar,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        Storage::delete('menu/' . basename($menu->gambar));
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}

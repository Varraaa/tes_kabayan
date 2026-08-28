<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::latest()->paginate(10);
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'          => 'required|string|unique:barang,sku|max:50',
            'nama_barang'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'satuan'       => 'required|string|max:50',
            'harga_pokok'  => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'status_aktif' => 'required|boolean',
        ]);

        Barang::create($validated);
        return redirect()->route('barang.index')->with('success', 'Barang baru berhasil didaftarkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'sku'          => 'required|string|max:50|unique:barang,sku,' . $barang->id,
            'nama_barang'  => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'satuan'       => 'required|string|max:50',
            'harga_pokok'  => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'status_aktif' => 'required|boolean',
        ]);

        $barang->update($validated);
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
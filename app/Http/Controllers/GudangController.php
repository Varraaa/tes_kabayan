<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    
    public function index()
    {
        $gudang = Gudang::latest()->paginate(10);
        return view('gudang.index', compact('gudang'));
    }

    public function create()
    {
        return view('gudang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gudang'  => 'required|string|max:255',
            'alamat'       => 'nullable|string',
            'status_aktif' => 'required|boolean',
        ]);

        Gudang::create($validated);
        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil ditambahkan');
    }

    
    public function edit(Gudang $gudang)
    {
        return view('gudang.edit', compact('gudang'));
    }

    
    public function update(Request $request, Gudang $gudang)
    {
        $validated = $request->validate([
            'nama_gudang'  => 'required|string|max:255',
            'alamat'       => 'nullable|string',
            'status_aktif' => 'required|boolean',
        ]);

        $gudang->update($validated);
        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil diperbarui');
    }

    
    public function destroy(Gudang $gudang)
    {
        $gudang->delete();
        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus');
    }
}

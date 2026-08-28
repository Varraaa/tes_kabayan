<?php

namespace App\Http\Controllers;

use App\Models\{Barang, Gudang, Pelanggan, Transaksi};
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $transaksi = Transaksi::with(['user', 'gudangAsal', 'gudangTujuan', 'pelanggan'])
            ->when($request->filled('jenis'), fn($q) => $q->where('jenis', $request->jenis))
            ->latest()->paginate(15);
        return view('transaksi.index', compact('transaksi'));
    }

    public function createMasuk()
    {
        return view('transaksi.masuk', [
            'gudang' => Gudang::where('status_aktif', 1)->get(),
            'barang' => Barang::where('status_aktif', 1)->get()
        ]);
    }

    public function createJual()
    {
        return view('transaksi.jual', [
            'gudang'    => Gudang::where('status_aktif', 1)->get(),
            'barang'    => Barang::where('status_aktif', 1)->get(),
            'pelanggan' => Pelanggan::all()
        ]);
    }

    public function createTransfer()
    {
        return view('transaksi.transfer', [
            'gudang' => Gudang::where('status_aktif', 1)->get(),
            'barang' => Barang::where('status_aktif', 1)->get()
        ]);
    }

    public function storeMasuk(Request $request)
    {
        $data = $request->validate([
            'gudang_id' => 'required|exists:gudang,id',
            'tanggal'   => 'required|date',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.harga'     => 'required|numeric|min:0',
        ]);

        TransaksiService::simpanMasuk($data + ['catatan' => $request->catatan]);
        return redirect()->route('transaksi.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function storeJual(Request $request)
    {
        $data = $request->validate([
            'gudang_id'         => 'required|exists:gudang,id',
            'pelanggan_id'      => 'nullable|exists:pelanggan,id',
            'tanggal'           => 'required|date',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
        ]);

        // Cek stok
        foreach ($data['items'] as $item) {
            $stok = TransaksiService::getStok($data['gudang_id'], $item['barang_id']);
            if ($stok < $item['jumlah']) {
                $b = Barang::find($item['barang_id']);
                return back()->withErrors(["Stok {$b->nama_barang} tidak cukup (Tersedia: {$stok})."])->withInput();
            }
        }

        TransaksiService::simpanJual($data + ['catatan' => $request->catatan]);
        return redirect()->route('transaksi.index')->with('success', 'Transaksi kasir berhasil.');
    }

    public function storeTransfer(Request $request)
    {
        $data = $request->validate([
            'gudang_asal_id'    => 'required|exists:gudang,id|different:gudang_tujuan_id',
            'gudang_tujuan_id'  => 'required|exists:gudang,id',
            'tanggal'           => 'required|date',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
        ]);

        foreach ($data['items'] as $item) {
            $stok = TransaksiService::getStok($data['gudang_asal_id'], $item['barang_id']);
            if ($stok < $item['jumlah']) {
                $b = Barang::find($item['barang_id']);
                return back()->withErrors(["Stok {$b->nama_barang} di gudang asal kurang (Tersedia: {$stok})."])->withInput();
            }
        }

        TransaksiService::simpanTransfer($data + ['catatan' => $request->catatan]);
        return redirect()->route('transaksi.index')->with('success', 'Transfer antar gudang berhasil.');
    }

    public function batalkan(Transaksi $transaksi)
    {
        if ($transaksi->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan.');
        }

        TransaksiService::batalkanTrx($transaksi);
        return back()->with('success', 'Transaksi dibatalkan dan sisa stok dikembalikan.');
    }
}
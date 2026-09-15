<?php

namespace App\Http\Controllers;

use App\Models\{Barang, Gudang, Pelanggan, Transaksi};
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $transaksi = Transaksi::with(['user', 'gudangAsal', 'gudangTujuan', 'pelanggan', 'details.barang'])
            ->when($request->filled('jenis'), fn($q) => $q->where('jenis', $request->jenis))
            ->latest()
            ->paginate(15);

        return view('transaksi.index', compact('transaksi'));
    }

    public function createMasuk()
    {
        $gudang = Gudang::where('status_aktif', 1)->get();
        $barang = Barang::where('status_aktif', 1)->get();

        return view('transaksi.masuk', compact('gudang', 'barang'));
    }

    public function createJual()
    {
        $gudang = Gudang::where('status_aktif', 1)->get();
        $barang = Barang::where('status_aktif', 1)->get();
        $pelanggan = Pelanggan::all();

        // Ambil stok awal untuk gudang pertama bila ada
        $stokAwal = [];
        if ($gudang->isNotEmpty()) {
            $stokAwal = TransaksiService::getStokByGudang($gudang->first()->id);
        }

        return view('transaksi.jual', compact('gudang', 'barang', 'pelanggan', 'stokAwal'));
    }

    public function getStokRealtime(Request $request)
    {
        $request->validate([
            'gudang_id' => 'required|exists:gudang,id',
        ]);

        $stok = TransaksiService::getStokByGudang($request->gudang_id);
        return response()->json($stok);
    }

    public function createTransfer()
    {
        $gudang = Gudang::where('status_aktif', 1)->get();
        $barang = Barang::where('status_aktif', 1)->get();

        return view('transaksi.transfer', compact('gudang', 'barang'));
    }

    public function storeMasuk(Request $request)
    {
        $data = $request->validate([
            'gudang_id'         => 'required|exists:gudang,id',
            'tanggal'           => 'required|date',
            'items'             => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.harga'     => 'required|numeric|min:0',
        ]);

        $trx = TransaksiService::simpanMasuk($data + ['catatan' => $request->catatan]);
        return redirect()->route('transaksi.index')
            ->with('success', "Barang masuk berhasil dicatat. (Ref: {$trx->no_referensi})");
    }

    public function storeJual(Request $request)
    {
        $data = $request->validate([
            'gudang_id'         => 'required|exists:gudang,id',
            'pelanggan_id'      => 'nullable|exists:pelanggan,id',
            'tanggal'           => 'required|date',
            'items'             => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.harga'     => 'nullable|numeric|min:0',
            'nominal_bayar'     => 'nullable|numeric|min:0',
            'kembalian'         => 'nullable|numeric',
        ]);

        // Cek stok real-time
        foreach ($data['items'] as $item) {
            $stok = TransaksiService::getStok($data['gudang_id'], $item['barang_id']);
            if ($stok < $item['jumlah']) {
                $b = Barang::find($item['barang_id']);
                return back()
                    ->withErrors(["Stok '{$b->nama_barang}' tidak mencukupi di gudang yang dipilih (Tersedia: {$stok}, diminta: {$item['jumlah']})."])
                    ->withInput();
            }
        }

        // Simpan info pembayaran di catatan jika ada
        $catatan = $request->catatan;
        if ($request->filled('nominal_bayar')) {
            $bayar = number_format((float)$request->nominal_bayar, 0, ',', '.');
            $kembali = number_format((float)($request->kembalian ?? 0), 0, ',', '.');
            $catatanBayar = "Pembayaran Tunai: Rp {$bayar} | Kembalian: Rp {$kembali}";
            $catatan = $catatan ? ($catatan . " | " . $catatanBayar) : $catatanBayar;
        }

        $trx = TransaksiService::simpanJual($data + ['catatan' => $catatan]);

        return redirect()->route('transaksi.index')
            ->with('success', "Transaksi kasir {$trx->no_referensi} berhasil disimpan.")
            ->with('last_trx_id', $trx->id)
            ->with('last_trx_ref', $trx->no_referensi);
    }

    public function storeTransfer(Request $request)
    {
        $data = $request->validate([
            'gudang_asal_id'    => 'required|exists:gudang,id|different:gudang_tujuan_id',
            'gudang_tujuan_id'  => 'required|exists:gudang,id',
            'tanggal'           => 'required|date',
            'items'             => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah'    => 'required|integer|min:1',
        ]);

        foreach ($data['items'] as $item) {
            $stok = TransaksiService::getStok($data['gudang_asal_id'], $item['barang_id']);
            if ($stok < $item['jumlah']) {
                $b = Barang::find($item['barang_id']);
                return back()
                    ->withErrors(["Stok '{$b->nama_barang}' di gudang asal tidak mencukupi (Tersedia: {$stok}, diminta: {$item['jumlah']})."])
                    ->withInput();
            }
        }

        $trx = TransaksiService::simpanTransfer($data + ['catatan' => $request->catatan]);
        return redirect()->route('transaksi.index')
            ->with('success', "Transfer antar gudang {$trx->no_referensi} berhasil diproses.");
    }

    public function batalkan(Transaksi $transaksi)
    {
        if ($transaksi->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        TransaksiService::batalkanTrx($transaksi);
        return back()->with('success', "Transaksi {$transaksi->no_referensi} berhasil dibatalkan dan seluruh stok telah dikembalikan ke kondisi semula.");
    }
}
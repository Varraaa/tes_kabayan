<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatStok::with(['barang', 'gudang', 'transaksi'])->latest('id');

        if ($request->filled('gudang_id')) {  
            $query->where('gudang_id', $request->gudang_id);
        }
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('created_at', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        }

        $riwayat = $query->paginate(20);
        $gudang = Gudang::all();
        $barang = Barang::all();

        return view('laporan.index', compact('riwayat', 'gudang', 'barang'));
    }

    public function exportCsv(Request $request)
    {
        $query = RiwayatStok::with(['barang', 'gudang', 'transaksi'])->latest('id');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_id', $request->gudang_id);
        }
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('created_at', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        }

        $records = $query->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'No Referensi', 'Gudang', 'SKU', 'Nama Barang', 'Tipe', 'Jumlah', 'Sisa Stok', 'Keterangan']);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->created_at->format('Y-m-d H:i'),
                    $row->transaksi ? $row->transaksi->no_referensi : '-',
                    $row->gudang->nama_gudang ?? '-',
                    $row->barang->sku ?? '-',
                    $row->barang->nama_barang ?? '-',
                    strtoupper($row->tipe),
                    $row->jumlah,
                    $row->sisa_stok,
                    $row->keterangan,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_mutasi_stok_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}

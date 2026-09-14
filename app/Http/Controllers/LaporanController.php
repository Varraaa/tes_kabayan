<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = RiwayatStok::with(['barang', 'gudang', 'transaksi'])->latest('id');

        if ($request->filled('gudang_id')) {  
            $query->where('gudang_id', $request->gudang_id);
        }
        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }
        if ($request->filled('tipe') && in_array($request->tipe, ['masuk', 'keluar'])) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('created_at', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('created_at', '>=', $request->tgl_mulai . ' 00:00:00');
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('created_at', '<=', $request->tgl_selesai . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('barang', function ($qb) use ($search) {
                      $qb->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transaksi', function ($qt) use ($search) {
                      $qt->where('no_referensi', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $baseQuery = $this->buildQuery($request);

        // Hitung metrik agregat di level database untuk seluruh data yang terfilter
        $stats = [
            'total_masuk'   => (clone $baseQuery)->where('tipe', 'masuk')->sum('jumlah'),
            'total_keluar'  => (clone $baseQuery)->where('tipe', 'keluar')->sum('jumlah'),
            'total_records' => (clone $baseQuery)->count(),
        ];
        $stats['net_mutasi'] = $stats['total_masuk'] - $stats['total_keluar'];

        $riwayat = $baseQuery->paginate(20);
        $gudang = Gudang::all();
        $barang = Barang::all();

        return view('laporan.index', compact('riwayat', 'gudang', 'barang', 'stats'));
    }

    public function exportCsv(Request $request)
    {
        $query = $this->buildQuery($request);
        $records = $query->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel menampilkan aksen dan karakter dengan rapi
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Waktu Mutasi', 'No Referensi', 'Gudang', 'SKU', 'Nama Barang', 'Tipe Mutasi', 'Jumlah Perubahan', 'Sisa Stok Akhir', 'Keterangan']);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-',
                    $row->transaksi ? $row->transaksi->no_referensi : '-',
                    $row->gudang->nama_gudang ?? '-',
                    $row->barang->sku ?? '-',
                    $row->barang->nama_barang ?? '-',
                    strtoupper($row->tipe),
                    ($row->tipe === 'masuk' ? '+' : '-') . $row->jumlah,
                    $row->sisa_stok,
                    $row->keterangan,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_mutasi_stok_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\RiwayatStok;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    // ==========================================
    // 1. LAPORAN MUTASI & KARTU STOK
    // ==========================================

    private function buildMutasiQuery(Request $request)
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
        $baseQuery = $this->buildMutasiQuery($request);

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
        $query = $this->buildMutasiQuery($request);
        $records = $query->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel menampilkan aksen dan karakter dengan rapi
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Waktu Mutasi (WIB)', 'No Referensi', 'Gudang', 'SKU', 'Nama Barang', 'Tipe Mutasi', 'Jumlah Perubahan', 'Sisa Stok Akhir', 'Keterangan']);

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

    // ==========================================
    // 2. LAPORAN PENJUALAN (SALES REPORT)
    // ==========================================

    private function buildPenjualanQuery(Request $request)
    {
        $query = Transaksi::with(['user', 'pelanggan', 'gudangAsal', 'details.barang'])
            ->where('jenis', 'jual')
            ->latest('tanggal')
            ->latest('id');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_asal_id', $request->gudang_id);
        }

        if ($request->filled('pelanggan_id')) {
            $query->where('pelanggan_id', $request->pelanggan_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status') && in_array($request->status, ['selesai', 'dibatalkan'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai . ' 00:00:00', $request->tgl_selesai . ' 23:59:59']);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai . ' 00:00:00');
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('tanggal', '<=', $request->tgl_selesai . ' 23:59:59');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('telepon', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.barang', function ($qb) use ($search) {
                      $qb->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function penjualan(Request $request)
    {
        $baseQuery = $this->buildPenjualanQuery($request);

        // Agregasi metrik finansial & volume penjualan
        $selesaiQuery = (clone $baseQuery)->where('status', 'selesai');
        $totalOmset = (float) $selesaiQuery->sum('total_bayar');
        $totalSelesai = $selesaiQuery->count();
        $totalDibatalkan = (clone $baseQuery)->where('status', 'dibatalkan')->count();
        $totalTransaksi = (clone $baseQuery)->count();

        // Hitung total item yang terjual pada transaksi berstatus selesai
        $trxIds = (clone $selesaiQuery)->pluck('id');
        $totalItemTerjual = TransaksiDetail::whereIn('transaksi_id', $trxIds)->sum('jumlah');

        $rataRataTransaksi = $totalSelesai > 0 ? round($totalOmset / $totalSelesai) : 0;

        $stats = [
            'total_omset'         => $totalOmset,
            'total_transaksi'     => $totalTransaksi,
            'total_selesai'       => $totalSelesai,
            'total_dibatalkan'    => $totalDibatalkan,
            'total_item_terjual'  => $totalItemTerjual,
            'rata_rata_transaksi' => $rataRataTransaksi,
        ];

        $transaksi = $baseQuery->paginate(20);
        $gudang = Gudang::all();
        $pelanggan = Pelanggan::all();
        $users = User::all();

        return view('laporan.penjualan', compact('transaksi', 'gudang', 'pelanggan', 'users', 'stats'));
    }

    public function exportPenjualanCsv(Request $request)
    {
        $query = $this->buildPenjualanQuery($request);
        $records = $query->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Waktu Transaksi (WIB)',
                'No Referensi',
                'Gudang Kasir',
                'Kasir / Petugas',
                'Pelanggan',
                'Status',
                'Total Nominal (Rp)',
                'Rincian Produk & Kuantitas'
            ]);

            foreach ($records as $t) {
                $itemDetails = [];
                foreach ($t->details as $d) {
                    $nama = $d->barang->nama_barang ?? 'Item';
                    $itemDetails[] = "{$nama} ({$d->jumlah}x)";
                }

                fputcsv($handle, [
                    $t->tanggal ? date('Y-m-d H:i:s', strtotime($t->tanggal)) : ($t->created_at ? $t->created_at->format('Y-m-d H:i:s') : '-'),
                    $t->no_referensi,
                    $t->gudangAsal->nama_gudang ?? '-',
                    $t->user->name ?? '-',
                    $t->pelanggan->nama ?? 'Umum',
                    strtoupper($t->status),
                    $t->total_bayar,
                    implode('; ', $itemDetails),
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_penjualan_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\RiwayatStok;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Halaman Utama Laporan Terpadu (Laporan Stok, Laporan Barang Masuk, Laporan Penjualan)
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'stok');
        if (!in_array($tab, ['stok', 'masuk', 'penjualan'])) {
            $tab = 'stok';
        }

        $gudang = Gudang::where('status_aktif', 1)->get();
        $barang = Barang::where('status_aktif', 1)->get();

        if ($tab === 'stok') {
            $data = $this->getLaporanStokData($request);
        } elseif ($tab === 'masuk') {
            $data = $this->getLaporanMasukData($request);
        } else {
            $data = $this->getLaporanPenjualanData($request);
        }

        return view('laporan.index', array_merge($data, [
            'tab'    => $tab,
            'gudang' => $gudang,
            'barang' => $barang,
        ]));
    }

    /**
     * 1. Data Laporan Stok Fisik & Nilai Aset
     */
    private function getLaporanStokData(Request $request)
    {
        $barangQuery = Barang::where('status_aktif', 1);

        if ($request->filled('barang_id')) {
            $barangQuery->where('id', $request->barang_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $barangQuery->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $barangList = $barangQuery->get();

        // Subquery posisi sisa stok terakhir
        $subQuery = RiwayatStok::query()
            ->selectRaw('MAX(id) as max_id')
            ->groupBy('gudang_id', 'barang_id');

        if ($request->filled('gudang_id')) {
            $subQuery->where('gudang_id', $request->gudang_id);
        }
        if ($request->filled('tgl_selesai')) {
            $subQuery->where('created_at', '<=', $request->tgl_selesai . ' 23:59:59');
        }

        $latestIds = $subQuery->pluck('max_id');

        $stokDetails = RiwayatStok::with(['gudang', 'barang'])
            ->whereIn('id', $latestIds)
            ->get();

        $selectedGudangModel = $request->filled('gudang_id') ? Gudang::find($request->gudang_id) : null;

        $stokItems = [];
        $totalFisik = 0;
        $totalNilaiAset = 0;

        foreach ($barangList as $b) {
            if ($request->filled('gudang_id')) {
                $row = $stokDetails->firstWhere('barang_id', $b->id);
                $sisa = $row ? (int) $row->sisa_stok : 0;
                $nilai = $sisa * (float) $b->harga_pokok;

                $stokItems[] = (object) [
                    'barang'      => $b,
                    'gudang_nama' => $selectedGudangModel ? $selectedGudangModel->nama_gudang : '-',
                    'sisa_stok'   => $sisa,
                    'harga_pokok' => (float) $b->harga_pokok,
                    'harga_jual'  => (float) $b->harga_jual,
                    'nilai_aset'  => $nilai,
                ];

                $totalFisik += $sisa;
                $totalNilaiAset += $nilai;
            } else {
                $rows = $stokDetails->where('barang_id', $b->id);
                $sisa = (int) $rows->sum('sisa_stok');
                $nilai = $sisa * (float) $b->harga_pokok;

                $stokItems[] = (object) [
                    'barang'      => $b,
                    'gudang_nama' => 'Semua Gudang (Konsolidasi)',
                    'sisa_stok'   => $sisa,
                    'harga_pokok' => (float) $b->harga_pokok,
                    'harga_jual'  => (float) $b->harga_jual,
                    'nilai_aset'  => $nilai,
                ];

                $totalFisik += $sisa;
                $totalNilaiAset += $nilai;
            }
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $col = collect($stokItems);

        $stokPaginated = new LengthAwarePaginator(
            $col->forPage($page, $perPage)->values(),
            $col->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return [
            'stokList'       => $stokPaginated,
            'totalJenis'     => count($barangList),
            'totalFisik'     => $totalFisik,
            'totalNilaiAset' => $totalNilaiAset,
        ];
    }

    /**
     * 2. Data Laporan Barang Masuk (Penerimaan / Restock)
     */
    private function getLaporanMasukData(Request $request)
    {
        $query = Transaksi::with(['gudangTujuan', 'details.barang', 'user'])
            ->where('jenis', 'masuk')
            ->where('status', 'selesai')
            ->latest('tanggal');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_tujuan_id', $request->gudang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_selesai]);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai);
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('tanggal', '<=', $request->tgl_selesai);
        }

        if ($request->filled('barang_id')) {
            $query->whereHas('details', function ($qd) use ($request) {
                $qd->where('barang_id', $request->barang_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('details.barang', function ($qb) use ($search) {
                      $qb->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $allMatching = (clone $query)->get();
        $totalTrx = $allMatching->count();
        $totalNominal = (float) $allMatching->sum('total_bayar');
        $totalUnit = 0;

        foreach ($allMatching as $trx) {
            foreach ($trx->details as $d) {
                if (!$request->filled('barang_id') || $d->barang_id == $request->barang_id) {
                    $totalUnit += $d->jumlah;
                }
            }
        }

        $masukList = $query->paginate(15);

        return [
            'masukList'    => $masukList,
            'totalTrx'     => $totalTrx,
            'totalNominal' => $totalNominal,
            'totalUnit'    => $totalUnit,
        ];
    }

    /**
     * 3. Data Laporan Penjualan (Pesanan & Omset)
     */
    private function getLaporanPenjualanData(Request $request)
    {
        $query = Transaksi::with(['gudangAsal', 'pelanggan', 'details.barang', 'user'])
            ->where('jenis', 'jual')
            ->latest('tanggal');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_asal_id', $request->gudang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_selesai]);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai);
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('tanggal', '<=', $request->tgl_selesai);
        }

        if ($request->filled('barang_id')) {
            $query->whereHas('details', function ($qd) use ($request) {
                $qd->where('barang_id', $request->barang_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_hp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.barang', function ($qb) use ($search) {
                      $qb->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $allMatching = (clone $query)->get();
        $selesaiList = $allMatching->where('status', 'selesai');
        $totalTrx = $selesaiList->count();
        $totalOmset = (float) $selesaiList->sum('total_bayar');
        $totalUnit = 0;

        foreach ($selesaiList as $trx) {
            foreach ($trx->details as $d) {
                if (!$request->filled('barang_id') || $d->barang_id == $request->barang_id) {
                    $totalUnit += $d->jumlah;
                }
            }
        }

        $penjualanList = $query->paginate(15);

        return [
            'penjualanList' => $penjualanList,
            'totalTrx'      => $totalTrx,
            'totalOmset'    => $totalOmset,
            'totalUnit'     => $totalUnit,
        ];
    }

    /**
     * Export CSV Dinamis Sesuai Tab Laporan & Filter Aktif
     */
    public function exportCsv(Request $request)
    {
        $tab = $request->query('tab', 'stok');

        if ($tab === 'stok') {
            return $this->exportStokCsv($request);
        } elseif ($tab === 'masuk') {
            return $this->exportMasukCsv($request);
        } else {
            return $this->exportPenjualanCsv($request);
        }
    }

    private function exportStokCsv(Request $request)
    {
        $data = $this->getLaporanStokData($request);
        $stokItems = $data['stokList']->items();

        $response = new StreamedResponse(function () use ($stokItems) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'SKU', 'Nama Barang', 'Kategori', 'Satuan', 'Gudang', 'Sisa Stok', 'Harga Pokok (Rp)', 'Harga Jual (Rp)', 'Total Nilai Aset (Rp)']);

            foreach ($stokItems as $idx => $item) {
                fputcsv($handle, [
                    $idx + 1,
                    $item->barang->sku ?? '-',
                    $item->barang->nama_barang ?? '-',
                    $item->barang->kategori ?? '-',
                    $item->barang->satuan ?? 'Pcs',
                    $item->gudang_nama,
                    $item->sisa_stok,
                    $item->harga_pokok,
                    $item->harga_jual,
                    $item->nilai_aset,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_stok_' . date('Ymd_His') . '.csv"');
        return $response;
    }

    private function exportMasukCsv(Request $request)
    {
        $query = Transaksi::with(['gudangTujuan', 'details.barang', 'user'])
            ->where('jenis', 'masuk')
            ->where('status', 'selesai')
            ->latest('tanggal');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_tujuan_id', $request->gudang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_selesai]);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai);
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('tanggal', '<=', $request->tgl_selesai);
        }
        if ($request->filled('barang_id')) {
            $query->whereHas('details', function ($qd) use ($request) {
                $qd->where('barang_id', $request->barang_id);
            });
        }

        $records = $query->get();

        $response = new StreamedResponse(function () use ($records, $request) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Waktu (WIB)', 'No Referensi', 'Gudang Tujuan', 'Petugas', 'SKU', 'Nama Barang', 'Jumlah Masuk', 'Satuan', 'Harga Beli (Rp)', 'Subtotal (Rp)', 'Keterangan']);

            foreach ($records as $trx) {
                foreach ($trx->details as $d) {
                    if ($request->filled('barang_id') && $d->barang_id != $request->barang_id) {
                        continue;
                    }
                    fputcsv($handle, [
                        $trx->tanggal ? date('Y-m-d', strtotime($trx->tanggal)) : '-',
                        $trx->created_at ? $trx->created_at->format('H:i:s') : '-',
                        $trx->no_referensi,
                        $trx->gudangTujuan->nama_gudang ?? '-',
                        $trx->user->name ?? '-',
                        $d->barang->sku ?? '-',
                        $d->barang->nama_barang ?? '-',
                        $d->jumlah,
                        $d->barang->satuan ?? 'Pcs',
                        $d->harga_satuan,
                        $d->subtotal,
                        $trx->catatan ?? '-',
                    ]);
                }
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_barang_masuk_' . date('Ymd_His') . '.csv"');
        return $response;
    }

    private function exportPenjualanCsv(Request $request)
    {
        $query = Transaksi::with(['gudangAsal', 'pelanggan', 'details.barang', 'user'])
            ->where('jenis', 'jual')
            ->latest('tanggal');

        if ($request->filled('gudang_id')) {
            $query->where('gudang_asal_id', $request->gudang_id);
        }
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_selesai]);
        } elseif ($request->filled('tgl_mulai')) {
            $query->where('tanggal', '>=', $request->tgl_mulai);
        } elseif ($request->filled('tgl_selesai')) {
            $query->where('tanggal', '<=', $request->tgl_selesai);
        }
        if ($request->filled('barang_id')) {
            $query->whereHas('details', function ($qd) use ($request) {
                $qd->where('barang_id', $request->barang_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_hp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.barang', function ($qb) use ($search) {
                      $qb->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $records = $query->get();

        $response = new StreamedResponse(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Waktu (WIB)', 'No Referensi', 'Gudang Asal', 'Pelanggan', 'Kasir', 'Rincian Produk', 'Total Tagihan (Rp)', 'Status', 'Catatan']);

            foreach ($records as $trx) {
                $detailStr = $trx->details->map(function ($d) {
                    return ($d->barang->nama_barang ?? '-') . ' (' . $d->jumlah . ')';
                })->implode('; ');

                fputcsv($handle, [
                    $trx->tanggal ? date('Y-m-d', strtotime($trx->tanggal)) : '-',
                    $trx->created_at ? $trx->created_at->format('H:i:s') : '-',
                    $trx->no_referensi,
                    $trx->gudangAsal->nama_gudang ?? '-',
                    $trx->pelanggan->nama ?? 'Umum',
                    $trx->user->name ?? '-',
                    $detailStr,
                    $trx->total_bayar,
                    strtoupper($trx->status),
                    $trx->catatan ?? '-',
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_penjualan_' . date('Ymd_His') . '.csv"');
        return $response;
    }
}

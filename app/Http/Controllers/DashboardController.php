<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\RiwayatStok;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mengambil data dashboard yang terfokus HARI INI (real-time).
     */
    private function getDashboardData(?string $selectedGudangId)
    {
        $today = Carbon::today();

        // 1. Data Master
        $totalBarang = Barang::count();
        $totalGudang = Gudang::count();
        $totalPelanggan = Pelanggan::count();

        // 2. Base Query Transaksi KHUSUS HARI INI
        $baseHariIni = Transaksi::whereDate('tanggal', $today);

        if ($selectedGudangId) {
            $baseHariIni->where(function ($q) use ($selectedGudangId) {
                $q->where('gudang_asal_id', $selectedGudangId)
                  ->orWhere('gudang_tujuan_id', $selectedGudangId);
            });
        }

        // Penjualan Hari Ini (hanya transaksi 'jual' dengan status 'selesai')
        $penjualanHariIniQuery = (clone $baseHariIni)
            ->where('jenis', 'jual')
            ->where('status', 'selesai');

        if ($selectedGudangId) {
            $penjualanHariIniQuery->where('gudang_asal_id', $selectedGudangId);
        }

        $penjualanHariIni = (float) $penjualanHariIniQuery->sum('total_bayar');

        // Total transaksi hari ini & rincian per tipe
        $transaksiHariIniCount = (clone $baseHariIni)->count();
        $penjualanHariIniCount = (clone $baseHariIni)->where('jenis', 'jual')->where('status', 'selesai')->count();
        $masukHariIniCount     = (clone $baseHariIni)->where('jenis', 'masuk')->where('status', 'selesai')->count();
        $transferHariIniCount  = (clone $baseHariIni)->where('jenis', 'transfer')->where('status', 'selesai')->count();

        // 3. Barang dengan Stok Terendah
        $subQuery = RiwayatStok::selectRaw('MAX(id) as max_id')
            ->groupBy('gudang_id', 'barang_id');
        if ($selectedGudangId) {
            $subQuery->where('gudang_id', $selectedGudangId);
        }
        $latestIds = $subQuery->pluck('max_id');

        $latestStocks = RiwayatStok::whereIn('id', $latestIds)
            ->selectRaw('barang_id, SUM(sisa_stok) as total_sisa_stok')
            ->groupBy('barang_id')
            ->pluck('total_sisa_stok', 'barang_id');

        $barangStokTerendah = Barang::where('status_aktif', 1)->get()->map(function ($b) use ($latestStocks) {
            return (object) [
                'id'          => $b->id,
                'sku'         => $b->sku,
                'nama_barang' => $b->nama_barang,
                'kategori'    => $b->kategori,
                'satuan'      => $b->satuan,
                'stok'        => (int) ($latestStocks[$b->id] ?? 0),
            ];
        })->sortBy('stok')->values()->take(8);

        // Riwayat Transaksi Hari Ini (Terbaru hari ini)
        $transaksiTerbaru = (clone $baseHariIni)
            ->with(['user', 'pelanggan', 'gudangAsal', 'gudangTujuan', 'details.barang'])
            ->latest('id')
            ->limit(25)
            ->get();

        return [
            'totalBarang'           => $totalBarang,
            'totalGudang'           => $totalGudang,
            'totalPelanggan'        => $totalPelanggan,
            'penjualanHariIni'      => $penjualanHariIni,
            'transaksiHariIniCount' => $transaksiHariIniCount,
            'penjualanHariIniCount' => $penjualanHariIniCount,
            'masukHariIniCount'     => $masukHariIniCount,
            'transferHariIniCount'  => $transferHariIniCount,
            'barangStokTerendah'    => $barangStokTerendah,
            'transaksiTerbaru'      => $transaksiTerbaru,
        ];
    }

    public function index(Request $request)
    {
        $gudangList = Gudang::where('status_aktif', 1)->get();
        $selectedGudangId = $request->query('gudang_id');

        $data = $this->getDashboardData($selectedGudangId);

        $selectedGudangName = 'Semua Gudang (Konsolidasi)';
        if ($selectedGudangId) {
            $selectedGudang = $gudangList->firstWhere('id', $selectedGudangId);
            if ($selectedGudang) {
                $selectedGudangName = $selectedGudang->nama_gudang;
            }
        }

        return view('dashboard', array_merge($data, [
            'gudangList'         => $gudangList,
            'selectedGudangId'   => $selectedGudangId,
            'selectedGudangName' => $selectedGudangName,
        ]));
    }

    /**
     * Endpoint API JSON untuk pembaruan data real-time via polling.
     */
    public function realtimeData(Request $request)
    {
        $selectedGudangId = $request->query('gudang_id');
        $data = $this->getDashboardData($selectedGudangId);

        $transaksiFormatted = $data['transaksiTerbaru']->map(function ($trx) {
            $gudangInfo = '-';
            if ($trx->jenis === 'jual') {
                $gudangInfo = 'Dari: ' . ($trx->gudangAsal->nama_gudang ?? '-');
            } elseif ($trx->jenis === 'masuk') {
                $gudangInfo = 'Ke: ' . ($trx->gudangTujuan->nama_gudang ?? '-');
            } else {
                $gudangInfo = ($trx->gudangAsal->nama_gudang ?? '-') . ' → ' . ($trx->gudangTujuan->nama_gudang ?? '-');
            }

            return [
                'id'                    => $trx->id,
                'no_referensi'          => $trx->no_referensi,
                'jam'                   => $trx->created_at ? $trx->created_at->format('H:i:s') : date('H:i:s', strtotime($trx->tanggal)),
                'jenis'                 => $trx->jenis,
                'gudang_info'           => $gudangInfo,
                'pihak'                 => $trx->pelanggan->nama ?? ($trx->user->name ?? '-'),
                'total_bayar'           => (float) $trx->total_bayar,
                'total_bayar_formatted' => $trx->total_bayar > 0 ? 'Rp ' . number_format($trx->total_bayar, 0, ',', '.') : '-',
                'status'                => $trx->status,
            ];
        });

        return response()->json([
            'status'                      => 'success',
            'timestamp'                   => now()->format('H:i:s'),
            'penjualan_hari_ini'          => $data['penjualanHariIni'],
            'penjualan_hari_ini_formatted'=> 'Rp ' . number_format($data['penjualanHariIni'], 0, ',', '.'),
            'transaksi_hari_ini_count'    => $data['transaksiHariIniCount'],
            'penjualan_hari_ini_count'    => $data['penjualanHariIniCount'],
            'masuk_hari_ini_count'        => $data['masukHariIniCount'],
            'transfer_hari_ini_count'     => $data['transferHariIniCount'],
            'transaksi_terbaru'           => $transaksiFormatted,
        ]);
    }
}

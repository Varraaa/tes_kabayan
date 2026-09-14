<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ringkasan Data Master
        $totalBarang = Barang::count();
        $totalGudang = Gudang::count();
        $totalPelanggan = Pelanggan::count();

        // 2. Data Penjualan & Transaksi Hari Ini
        $penjualanHariIni = Transaksi::where('jenis', 'jual')
            ->where('status', 'selesai')
            ->whereDate('tanggal', today())
            ->sum('total_bayar');

        $transaksiHariIniCount = Transaksi::whereDate('tanggal', today())->count();

        // 3. Daftar Barang dengan Stok Terendah (Akumulasi akurat dari seluruh gudang aktif)
        $gudangs = Gudang::where('status_aktif', 1)->get();
        $stokTerendah = Barang::where('status_aktif', 1)
            ->get()
            ->map(function ($b) use ($gudangs) {
                $totalStok = 0;
                foreach ($gudangs as $g) {
                    $totalStok += \App\Services\TransaksiService::getStok($g->id, $b->id);
                }
                return (object)[
                    'id'          => $b->id,
                    'sku'         => $b->sku,
                    'nama_barang' => $b->nama_barang,
                    'kategori'    => $b->kategori,
                    'satuan'      => $b->satuan,
                    'sisa_stok'   => $totalStok,
                ];
            })
            ->sortBy('sisa_stok')
            ->take(5)
            ->values();

        // 4. Daftar Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['user', 'pelanggan', 'gudangAsal', 'gudangTujuan', 'details.barang'])
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBarang',
            'totalGudang',
            'totalPelanggan',
            'penjualanHariIni',
            'transaksiHariIniCount',
            'stokTerendah',
            'transaksiTerbaru'
        ));
    }
}


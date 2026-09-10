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

        // 3. Daftar Barang dengan Stok Terendah
        $stokTerendah = DB::table('barang')
            ->leftJoin('riwayat_stok', function ($join) {
                $join->on('barang.id', '=', 'riwayat_stok.barang_id')
                    ->whereRaw('riwayat_stok.id = (SELECT MAX(id) FROM riwayat_stok WHERE riwayat_stok.barang_id = barang.id)');
            })
            ->select('barang.id', 'barang.nama_barang', 'barang.sku', 'barang.satuan', 'barang.kategori', DB::raw('COALESCE(riwayat_stok.sisa_stok, 0) as sisa_stok'))
            ->orderBy('sisa_stok', 'asc')
            ->limit(5)
            ->get();

        // 4. Daftar Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['user', 'pelanggan', 'gudangAsal', 'gudangTujuan'])
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


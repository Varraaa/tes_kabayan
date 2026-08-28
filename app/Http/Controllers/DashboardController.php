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
        $totalBarang = Barang::count();
        $totalGudang = Gudang::count();
        $totalPelanggan = Pelanggan::count();

        $penjualanHariIni = Transaksi::where('jenis', 'jual')
            ->where('status', 'selesai')
            ->whereDate('tanggal', today())
            ->sum('total_bayar');

        $stokTerendah = DB::table('barang')
            ->leftJoin('riwayat_stok', function ($join) {
                $join->on('barang.id', '=', 'riwayat_stok.barang_id')
                    ->whereRaw('riwayat_stok.id = (SELECT MAX(id) FROM riwayat_stok WHERE riwayat_stok.barang_id = barang.id)');
            })
            ->select('barang.nama_barang', 'barang.sku', 'barang.satuan', DB::raw('COALESCE(riwayat_stok.sisa_stok, 0) as sisa_stok'))
            ->orderBy('sisa_stok', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('totalBarang', 'totalGudang', 'totalPelanggan', 'penjualanHariIni', 'stokTerendah'));
    }
}

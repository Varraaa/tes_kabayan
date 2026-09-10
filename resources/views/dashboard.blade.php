@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- 1. Ringkasan Kartu Statistik (Simple KPI) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Barang -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Barang</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalBarang }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data master produk terdaftar</div>
        </div>

        <!-- Total Gudang -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Gudang</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalGudang }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gudang aktif operasional</div>
        </div>

        <!-- Total Pelanggan -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pelanggan</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPelanggan }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pelanggan terdata</div>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Penjualan Hari Ini</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ $transaksiHariIniCount }} transaksi pada {{ date('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- 2. Tombol Aksi Cepat Sederhana -->
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Menu Cepat:</div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('transaksi.jual') }}"
                class="px-3 py-2 text-xs font-medium text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                + Transaksi Penjualan (Kasir)
            </a>
            <a href="{{ route('transaksi.masuk') }}"
                class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                + Catat Barang Masuk
            </a>
            <a href="{{ route('transaksi.transfer') }}"
                class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                + Transfer Antar Gudang
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('barang.create') }}"
                class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                + Tambah Barang
            </a>
            @endif
        </div>
    </div>

    <!-- 3. Tabel Data Transaksi Terbaru & Stok Terendah -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Transaksi Terakhir -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-400">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300 uppercase">
                        <tr>
                            <th scope="col" class="px-3 py-2.5">No Referensi</th>
                            <th scope="col" class="px-3 py-2.5">Jenis</th>
                            <th scope="col" class="px-3 py-2.5 text-right">Total</th>
                            <th scope="col" class="px-3 py-2.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transaksiTerbaru as $trx)
                        <tr>
                            <td class="px-3 py-2.5 font-medium text-gray-900 dark:text-white">
                                {{ $trx->no_referensi }}
                                <div class="text-[11px] text-gray-400">{{ date('d/m/Y', strtotime($trx->tanggal)) }}</div>
                            </td>
                            <td class="px-3 py-2.5">
                                @if($trx->jenis === 'jual')
                                    <span class="bg-blue-100 text-blue-800 text-[11px] font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Jual</span>
                                @elseif($trx->jenis === 'masuk')
                                    <span class="bg-green-100 text-green-800 text-[11px] font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Masuk</span>
                                @else
                                    <span class="bg-purple-100 text-purple-800 text-[11px] font-medium px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Transfer</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right font-medium text-gray-900 dark:text-white">
                                @if($trx->total_bayar > 0)
                                    Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @if($trx->status === 'selesai')
                                    <span class="text-green-700 font-medium dark:text-green-400">Selesai</span>
                                @else
                                    <span class="text-red-700 font-medium dark:text-red-400">Batal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-400">
                                Belum ada transaksi tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Terendah -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Barang dengan Stok Terendah</h3>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('barang.index') }}" class="text-xs text-blue-600 hover:underline dark:text-blue-400">
                    Master Barang
                </a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300 uppercase">
                        <tr>
                            <th scope="col" class="px-3 py-2.5">SKU</th>
                            <th scope="col" class="px-3 py-2.5">Nama Barang</th>
                            <th scope="col" class="px-3 py-2.5">Kategori</th>
                            <th scope="col" class="px-3 py-2.5 text-right">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($stokTerendah as $stok)
                        <tr>
                            <td class="px-3 py-2.5 font-mono text-gray-700 dark:text-gray-300">
                                {{ $stok->sku }}
                            </td>
                            <td class="px-3 py-2.5 font-medium text-gray-900 dark:text-white">
                                {{ $stok->nama_barang }}
                            </td>
                            <td class="px-3 py-2.5 text-gray-500 dark:text-gray-400">
                                {{ $stok->kategori }}
                            </td>
                            <td class="px-3 py-2.5 text-right font-bold {{ $stok->sisa_stok <= 10 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $stok->sisa_stok }} {{ $stok->satuan }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-400">
                                Tidak ada data stok barang.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
@extends('layouts.app')

@section('title', 'Laporan')
@section('header', 'Laporan')

@section('content')
<div class="space-y-5">

    <!-- 1. Header & Aksi Export CSV -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Laporan</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pusat analisis persediaan stok gudang, penerimaan barang masuk, dan laporan penjualan.
            </p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('laporan.export', array_merge(request()->query(), ['tab' => $tab])) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- 2. Navigasi Tab Laporan (Pills) -->
    <div class="flex flex-wrap gap-2 print:hidden">
        <a href="{{ route('laporan.index', array_merge(request()->query(), ['tab' => 'stok'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition {{ $tab === 'stok' ? 'bg-blue-600 text-white shadow-sm font-semibold' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Laporan Stok
        </a>

        <a href="{{ route('laporan.index', array_merge(request()->query(), ['tab' => 'masuk'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition {{ $tab === 'masuk' ? 'bg-blue-600 text-white shadow-sm font-semibold' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
            Laporan Barang Masuk
        </a>

        <a href="{{ route('laporan.index', array_merge(request()->query(), ['tab' => 'penjualan'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg transition {{ $tab === 'penjualan' ? 'bg-blue-600 text-white shadow-sm font-semibold' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Laporan Penjualan
        </a>
    </div>

    <!-- 3. Ringkasan Metrik / KPI Sesuai Tab Aktif -->
    @if($tab === 'stok')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Jenis Barang</span>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 font-mono">
                    {{ number_format($totalJenis, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Barang aktif terdata</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Stok Fisik</span>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">
                    {{ number_format($totalFisik, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Akumulasi sisa unit di gudang</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Estimasi Nilai Aset Stok</span>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">
                    Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Kalkulasi: Sisa Stok × Harga Pokok</span>
            </div>
        </div>
    @elseif($tab === 'masuk')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Penerimaan Masuk</span>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 font-mono">
                    {{ number_format($totalTrx, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Transaksi restock / penerimaan</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Unit Masuk</span>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">
                    +{{ number_format($totalUnit, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Barang masuk pada periode filter</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Biaya Pembelian</span>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">
                    Rp {{ number_format($totalNominal, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Akumulasi nominal transaksi penerimaan</span>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Transaksi Penjualan</span>
                <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 font-mono">
                    {{ number_format($totalTrx, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Pesanan penjualan selesai</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Unit Terjual</span>
                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-0.5 font-mono">
                    {{ number_format($totalUnit, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Unit produk terkirim / terjual</span>
            </div>

            <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Omset Penjualan</span>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-gray-400">Akumulasi pendapatan bersih (selesai)</span>
            </div>
        </div>
    @endif

    <!-- 4. Form Filter (Gudang, Barang, Tanggal, dan Pencarian) -->
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 print:hidden">
        <form method="GET" action="{{ route('laporan.index') }}" class="space-y-3">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Filter Gudang -->
                <div>
                    <label for="gudang_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Filter Gudang
                    </label>
                    <select id="gudang_id" name="gudang_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Gudang</option>
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}" {{ request('gudang_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gudang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Barang -->
                <div>
                    <label for="barang_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Filter Barang
                    </label>
                    <select id="barang_id" name="barang_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Barang</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_barang }} ({{ $b->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Filter Tanggal
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <span class="text-xs text-gray-400">-</span>
                        <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Baris Pencarian & Tombol Aksi -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-1">
                <div class="w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ $tab === 'penjualan' ? 'Cari no. ref, pelanggan, atau produk...' : 'Cari no. ref, SKU, atau nama barang...' }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if(request()->anyFilled(['gudang_id', 'barang_id', 'tgl_mulai', 'tgl_selesai', 'search']))
                        <a href="{{ route('laporan.index', ['tab' => $tab]) }}" 
                           class="text-xs text-red-600 hover:text-red-700 font-medium px-2 py-1.5 dark:text-red-400">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 5. Tampilan Data Sesuai Tab Aktif -->
    @if($tab === 'stok')
        {{-- ==================== TAB 1: LAPORAN STOK ==================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">SKU</th>
                            <th scope="col" class="px-4 py-3">Nama Barang</th>
                            <th scope="col" class="px-4 py-3">Kategori</th>
                            <th scope="col" class="px-4 py-3">Lokasi Gudang</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Sisa Stok</th>
                            <th scope="col" class="px-4 py-3 text-center">Status</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Harga Pokok</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Nilai Aset</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($stokList as $idx => $item)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3 text-center text-gray-400">
                                    {{ $stokList->firstItem() + $idx }}
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $item->barang->sku }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $item->barang->nama_barang }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $item->barang->kategori ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $item->gudang_nama }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                                    {{ number_format($item->sisa_stok, 0, ',', '.') }} {{ $item->barang->satuan ?? 'pcs' }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if ($item->sisa_stok <= 0)
                                        <span class="bg-red-100 text-red-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            Habis
                                        </span>
                                    @elseif ($item->sisa_stok <= ($item->barang->stok_minimum ?? 5))
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-amber-900 dark:text-amber-300">
                                            Kritis
                                        </span>
                                    @else
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    Rp {{ number_format($item->harga_pokok, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    Rp {{ number_format($item->nilai_aset, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada data stok barang yang cocok.</p>
                                    <p class="text-xs mt-1">Silakan sesuaikan filter gudang atau pencarian barang di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stokList->hasPages())
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    {{ $stokList->withQueryString()->links() }}
                </div>
            @endif
        </div>

    @elseif($tab === 'masuk')
        {{-- ==================== TAB 2: LAPORAN BARANG MASUK ==================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Tanggal & Waktu</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">No. Referensi</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Gudang Penerima</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Petugas</th>
                            <th scope="col" class="px-4 py-3">Rincian Barang & Jumlah</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Total Pembelian</th>
                            <th scope="col" class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($masukList as $trx)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition align-top">
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600 dark:text-gray-400">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ date('d/m/Y', strtotime($trx->tanggal)) }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $trx->created_at ? $trx->created_at->format('H:i') : '-' }} WIB
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $trx->no_referensi }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $trx->gudangTujuan->nama_gudang ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $trx->user->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach($trx->details as $d)
                                            <div class="flex items-center justify-between text-[11px] gap-2 {{ request('barang_id') == $d->barang_id ? 'bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 rounded font-medium' : '' }}">
                                                <span class="text-gray-900 dark:text-white">
                                                    {{ $d->barang->nama_barang ?? 'Barang Dihapus' }}
                                                    @if(optional($d->barang)->sku)
                                                        <span class="text-gray-400 font-mono text-[10px]">({{ $d->barang->sku }})</span>
                                                    @endif
                                                </span>
                                                <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                                    +{{ number_format($d->jumlah, 0, ',', '.') }} {{ $d->barang->satuan ?? 'pcs' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-[11px] max-w-xs truncate" title="{{ $trx->catatan }}">
                                    {{ $trx->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada data penerimaan barang masuk.</p>
                                    <p class="text-xs mt-1">Silakan sesuaikan filter tanggal atau gudang di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($masukList->hasPages())
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    {{ $masukList->withQueryString()->links() }}
                </div>
            @endif
        </div>

    @else
        {{-- ==================== TAB 3: LAPORAN PENJUALAN ==================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Tanggal & Waktu</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">No. Referensi</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Pelanggan</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Gudang Asal</th>
                            <th scope="col" class="px-4 py-3">Produk & Qty Terjual</th>
                            <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Total Omset</th>
                            <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                            <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($penjualanList as $trx)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition align-top">
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600 dark:text-gray-400">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ date('d/m/Y', strtotime($trx->tanggal)) }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $trx->created_at ? $trx->created_at->format('H:i') : '-' }} WIB
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ $trx->no_referensi }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-900 dark:text-white block">
                                        {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}
                                    </span>
                                    @if(optional($trx->pelanggan)->no_hp)
                                        <span class="text-[10px] text-gray-400 block font-mono">
                                            {{ $trx->pelanggan->no_hp }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ $trx->gudangAsal->nama_gudang ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach($trx->details as $d)
                                            <div class="flex items-center justify-between text-[11px] gap-2 {{ request('barang_id') == $d->barang_id ? 'bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 rounded font-medium' : '' }}">
                                                <span class="text-gray-900 dark:text-white">
                                                    {{ $d->barang->nama_barang ?? 'Barang Dihapus' }}
                                                    @if(optional($d->barang)->sku)
                                                        <span class="text-gray-400 font-mono text-[10px]">({{ $d->barang->sku }})</span>
                                                    @endif
                                                </span>
                                                <span class="font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                                    {{ number_format($d->jumlah, 0, ',', '.') }} {{ $d->barang->satuan ?? 'pcs' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold whitespace-nowrap {{ $trx->status === 'selesai' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 line-through' }}">
                                    Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if ($trx->status === 'selesai')
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <button type="button" data-modal-target="modal-detail-{{ $trx->id }}"
                                            data-modal-toggle="modal-detail-{{ $trx->id }}"
                                            class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded text-xs px-2.5 py-1 inline-flex items-center gap-1 focus:outline-none">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </button>

                                    <!-- Modal Detail Transaksi Penjualan -->
                                    <div id="modal-detail-{{ $trx->id }}" tabindex="-1" aria-hidden="true"
                                         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative w-full max-w-2xl max-h-full text-left">
                                            <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <!-- Modal Header -->
                                                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                                                    <div>
                                                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                            <span>{{ $trx->no_referensi }}</span>
                                                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">
                                                                PENJUALAN
                                                            </span>
                                                        </h3>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                            Tanggal: {{ date('d F Y', strtotime($trx->tanggal)) }} &bull; Kasir: {{ $trx->user->name ?? '-' }}
                                                        </p>
                                                    </div>
                                                    <button type="button"
                                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                                            data-modal-hide="modal-detail-{{ $trx->id }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="p-4 space-y-4 text-xs">
                                                    <div class="grid grid-cols-2 gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                        <div>
                                                            <span class="text-gray-500 dark:text-gray-400 block text-[11px]">Pelanggan:</span>
                                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                                {{ $trx->pelanggan->nama ?? 'Pelanggan Umum' }}
                                                            </span>
                                                            @if(optional($trx->pelanggan)->no_hp)
                                                                <span class="block text-gray-500 font-mono text-[11px]">{{ $trx->pelanggan->no_hp }}</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-500 dark:text-gray-400 block text-[11px]">Gudang Asal:</span>
                                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                                {{ $trx->gudangAsal->nama_gudang ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                                                <tr>
                                                                    <th class="px-3 py-2">Barang</th>
                                                                    <th class="px-3 py-2 text-right">Harga</th>
                                                                    <th class="px-3 py-2 text-center">Qty</th>
                                                                    <th class="px-3 py-2 text-right">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                                @foreach ($trx->details as $d)
                                                                    <tr>
                                                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                                                            {{ $d->barang->nama_barang ?? '-' }}
                                                                            <span class="text-[10px] text-gray-400 font-mono block">SKU: {{ $d->barang->sku ?? '-' }}</span>
                                                                        </td>
                                                                        <td class="px-3 py-2 text-right font-mono text-gray-600 dark:text-gray-300">
                                                                            Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-center font-mono font-semibold">
                                                                            {{ $d->jumlah }}
                                                                        </td>
                                                                        <td class="px-3 py-2 text-right font-mono font-bold text-gray-900 dark:text-white">
                                                                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="space-y-1.5 border-t border-gray-200 dark:border-gray-700 pt-3 text-xs">
                                                        <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white">
                                                            <span>Total Pembayaran:</span>
                                                            <span class="font-mono text-emerald-600 dark:text-emerald-400">
                                                                Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    @if ($trx->catatan)
                                                        <div class="text-[11px] text-gray-500 bg-gray-50 dark:bg-gray-700/50 p-2.5 rounded border border-gray-200 dark:border-gray-600">
                                                            <strong>Catatan:</strong> {{ $trx->catatan }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Modal Footer -->
                                                <div class="flex items-center justify-end p-3 border-t border-gray-200 rounded-b dark:border-gray-700">
                                                    <button type="button"
                                                            data-modal-hide="modal-detail-{{ $trx->id }}"
                                                            class="text-white bg-gray-700 hover:bg-gray-800 font-medium rounded-lg text-xs px-4 py-2 transition">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                    <p class="text-sm font-medium">Tidak ada data transaksi penjualan yang cocok.</p>
                                    <p class="text-xs mt-1">Silakan sesuaikan filter tanggal atau gudang di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($penjualanList->hasPages())
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    {{ $penjualanList->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection

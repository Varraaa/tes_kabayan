@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')
@section('header', 'Laporan Mutasi Stok')

@section('content')
<div class="space-y-5">

    <!-- 1. Header & Aksi Cetak / Export -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        <!-- Tab Navigasi Laporan -->
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.penjualan') }}"
               class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Laporan Penjualan
            </a>
            <a href="{{ route('laporan.index') }}"
               class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-blue-600 text-white shadow-sm transition">
                Laporan Mutasi Stok
            </a>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('laporan.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel (CSV)
            </a>
        </div>
    </div>

    <!-- 2. Ringkasan Angka (Sederhana & Mudah Dibaca) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Masuk</span>
            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">
                +{{ number_format($stats['total_masuk'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Barang masuk & retur</span>
        </div>

        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Keluar</span>
            <div class="text-xl font-bold text-red-600 dark:text-red-400 mt-0.5 font-mono">
                -{{ number_format($stats['total_keluar'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Penjualan & pengiriman</span>
        </div>

        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Selisih Bersih</span>
            <div class="text-xl font-bold {{ $stats['net_mutasi'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400' }} mt-0.5 font-mono">
                {{ $stats['net_mutasi'] > 0 ? '+' : '' }}{{ number_format($stats['net_mutasi'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Arus bersih periode ini</span>
        </div>

        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Catatan</span>
            <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 font-mono">
                {{ number_format($stats['total_records'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Baris data terfilter</span>
        </div>
    </div>

    <!-- 3. Form Filter Sederhana & Rapi -->
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 print:hidden">
        <form method="GET" action="{{ route('laporan.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Gudang -->
                <div>
                    <label for="gudang_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Gudang
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

                <!-- Barang -->
                <div>
                    <label for="barang_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Produk / Barang
                    </label>
                    <select id="barang_id" name="barang_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Produk</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_barang }} ({{ $b->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Mutasi -->
                <div>
                    <label for="tipe" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Jenis Mutasi
                    </label>
                    <select id="tipe" name="tipe"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Jenis (Masuk & Keluar)</option>
                        <option value="masuk" {{ request('tipe') === 'masuk' ? 'selected' : '' }}>Hanya Barang Masuk (+)</option>
                        <option value="keluar" {{ request('tipe') === 'keluar' ? 'selected' : '' }}>Hanya Barang Keluar (-)</option>
                    </select>
                </div>

                <!-- Periode Tanggal -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Periode Tanggal
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. ref, SKU, atau barang..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if(request()->anyFilled(['gudang_id', 'barang_id', 'tipe', 'tgl_mulai', 'tgl_selesai', 'search']))
                        <a href="{{ route('laporan.index') }}" 
                           class="text-xs text-red-600 hover:text-red-700 font-medium px-2 py-1.5 dark:text-red-400">
                            Reset
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

    <!-- 4. Tabel Riwayat Mutasi Stok -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Tanggal & Waktu</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">No. Referensi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Gudang</th>
                        <th scope="col" class="px-4 py-3">Nama Barang</th>
                        <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Jenis</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Jumlah</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Sisa Stok</th>
                        <th scope="col" class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <!-- Tanggal -->
                            <td class="px-4 py-2.5 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                                {{ $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-' }}
                            </td>

                            <!-- No. Referensi -->
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($r->transaksi)
                                    <span class="font-mono font-semibold text-gray-900 dark:text-white">
                                        {{ $r->transaksi->no_referensi }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Gudang -->
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                {{ $r->gudang->nama_gudang ?? '-' }}
                            </td>

                            <!-- Barang -->
                            <td class="px-4 py-2.5">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $r->barang->nama_barang ?? '-' }}</span>
                                @if(optional($r->barang)->sku)
                                    <span class="text-[10px] text-gray-400 font-mono ml-1">({{ $r->barang->sku }})</span>
                                @endif
                            </td>

                            <!-- Jenis Mutasi -->
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                @if ($r->tipe === 'masuk')
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                                        MASUK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-50 border border-red-200 rounded dark:bg-red-950/40 dark:text-red-300 dark:border-red-800">
                                        KELUAR
                                    </span>
                                @endif
                            </td>

                            <!-- Jumlah -->
                            <td class="px-4 py-2.5 text-right font-mono font-bold whitespace-nowrap {{ $r->tipe === 'masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $r->tipe === 'masuk' ? '+' : '-' }}{{ number_format($r->jumlah, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok -->
                            <td class="px-4 py-2.5 text-right font-mono font-bold whitespace-nowrap text-gray-900 dark:text-white">
                                {{ number_format($r->sisa_stok, 0, ',', '.') }}
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate text-[11px]" title="{{ $r->keterangan }}">
                                {{ $r->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                <p class="text-sm font-medium">Tidak ada data mutasi persediaan yang ditemukan.</p>
                                <p class="text-xs mt-1">Silakan sesuaikan filter gudang, barang, atau tanggal di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($riwayat->hasPages())
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                {{ $riwayat->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

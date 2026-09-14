@extends('layouts.app')

@section('title', 'Laporan Mutasi & Kartu Stok')
@section('header', 'Laporan Mutasi & Kartu Stok')

@section('content')
<div class="space-y-6">

    <!-- 1. Header Informasi & Ringkasan Laporan -->
    <div class="p-5 bg-gradient-to-r from-blue-900 to-indigo-800 rounded-2xl text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-1.5 bg-blue-500/30 rounded-lg backdrop-blur-sm">
                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <h2 class="text-xl font-bold tracking-tight">Buku Besar Mutasi Persediaan</h2>
            </div>
            <p class="text-xs text-blue-100/80 max-w-2xl leading-relaxed">
                Log kartu stok mencatat seluruh pergerakan barang secara real-time dari penerimaan barang masuk (restock), penjualan kasir, transfer antar gudang, hingga pembatalan dokumen transaksi.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="window.print()" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-gray-800 bg-white hover:bg-gray-100 rounded-lg shadow transition">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Laporan
            </button>
            <a href="{{ route('laporan.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download CSV Excel
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Mutasi Stok (Flowbite Metric Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Unit Masuk -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Unit Masuk</p>
                <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 font-mono">
                    +{{ number_format($stats['total_masuk'], 0, ',', '.') }}
                </h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Penerimaan & retur batal</p>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Total Unit Keluar -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Unit Keluar</p>
                <h4 class="text-2xl font-black text-red-600 dark:text-red-400 mt-1 font-mono">
                    -{{ number_format($stats['total_keluar'], 0, ',', '.') }}
                </h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Penjualan kasir & transfer</p>
            </div>
            <div class="p-3 bg-red-50 dark:bg-red-950/40 rounded-xl text-red-600 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
        </div>

        <!-- Card 3: Net Mutasi Bersih -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Selisih Mutasi</p>
                <h4 class="text-2xl font-black {{ $stats['net_mutasi'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400' }} mt-1 font-mono">
                    {{ $stats['net_mutasi'] > 0 ? '+' : '' }}{{ number_format($stats['net_mutasi'], 0, ',', '.') }}
                </h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Selisih arus stok periode ini</p>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
        </div>

        <!-- Card 4: Total Log Terdata -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Frekuensi Mutasi</p>
                <h4 class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">
                    {{ number_format($stats['total_records'], 0, ',', '.') }}
                </h4>
                <p class="text-[11px] text-gray-400 mt-0.5">Total baris log terfilter</p>
            </div>
            <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl text-purple-600 dark:text-purple-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar Filter Multi-Parameter (Flowbite Filter Panel) -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between border-b pb-3 mb-4 dark:border-gray-700">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter Parameter Mutasi
            </h3>
            <div class="flex items-center gap-2">
                <button type="button" onclick="setPresetDate('today')" class="text-[11px] px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 dark:bg-gray-700 dark:text-gray-200">Hari Ini</button>
                <button type="button" onclick="setPresetDate('7days')" class="text-[11px] px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 dark:bg-gray-700 dark:text-gray-200">7 Hari Terakhir</button>
                <button type="button" onclick="setPresetDate('month')" class="text-[11px] px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 dark:bg-gray-700 dark:text-gray-200">Bulan Ini</button>
                <a href="{{ route('laporan.index') }}" class="text-[11px] px-2.5 py-1 text-red-600 hover:text-red-800 hover:underline dark:text-red-400">Reset Filter</a>
            </div>
        </div>

        <form method="GET" action="{{ route('laporan.index') }}" id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Filter Gudang -->
                <div>
                    <label for="gudang_id" class="block mb-1 text-[11px] font-bold uppercase text-gray-600 dark:text-gray-400">
                        Lokasi Gudang
                    </label>
                    <select id="gudang_id" name="gudang_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Gudang (Konsolidasi)</option>
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}" {{ request('gudang_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gudang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Barang -->
                <div>
                    <label for="barang_id" class="block mb-1 text-[11px] font-bold uppercase text-gray-600 dark:text-gray-400">
                        Produk / SKU
                    </label>
                    <select id="barang_id" name="barang_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Produk</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>
                                [{{ $b->sku }}] {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tipe Mutasi -->
                <div>
                    <label for="tipe" class="block mb-1 text-[11px] font-bold uppercase text-gray-600 dark:text-gray-400">
                        Tipe Mutasi
                    </label>
                    <select id="tipe" name="tipe"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Arus (Masuk & Keluar)</option>
                        <option value="masuk" {{ request('tipe') === 'masuk' ? 'selected' : '' }}>Hanya Mutasi Masuk (+)</option>
                        <option value="keluar" {{ request('tipe') === 'keluar' ? 'selected' : '' }}>Hanya Mutasi Keluar (-)</option>
                    </select>
                </div>

                <!-- Filter Rentang Tanggal -->
                <div class="lg:col-span-2">
                    <label class="block mb-1 text-[11px] font-bold uppercase text-gray-600 dark:text-gray-400">
                        Rentang Waktu Mutasi
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tgl_mulai" id="tgl_mulai" value="{{ request('tgl_mulai') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <span class="text-xs text-gray-400 font-bold">s/d</span>
                        <input type="date" name="tgl_selesai" id="tgl_selesai" value="{{ request('tgl_selesai') }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-xs px-4 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 whitespace-nowrap shadow-sm transition">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Keyword Bar -->
            <div class="pt-2 flex items-center gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor dokumen referensi, SKU, nama produk, atau keterangan alur..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-9 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>
                @if(request()->anyFilled(['gudang_id', 'barang_id', 'tipe', 'tgl_mulai', 'tgl_selesai', 'search']))
                    <a href="{{ route('laporan.index') }}" 
                       class="text-xs px-3 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg dark:bg-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Hapus Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Tabel Buku Besar Kartu Stok (Flowbite Designed Table) -->
    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Rincian Buku Besar Mutasi Stok</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan mutasi persediaan barang secara kronologis (terbaru ke terlama)</p>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                Total Data: <span class="font-bold text-gray-900 dark:text-white">{{ $riwayat->total() }} baris</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[11px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Waktu Mutasi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Dokumen Ref</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Lokasi Gudang</th>
                        <th scope="col" class="px-4 py-3">Produk & SKU</th>
                        <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Arus Mutasi</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Perubahan Unit</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Sisa Stok Akhir</th>
                        <th scope="col" class="px-4 py-3">Keterangan / Alur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 font-sans">
                    @forelse($riwayat as $r)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <!-- Waktu Mutasi -->
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $r->created_at ? $r->created_at->format('d/m/Y') : '-' }}</span>
                                <span class="block text-[10px] text-gray-400">{{ $r->created_at ? $r->created_at->format('H:i:s') : '' }} WIB</span>
                            </td>

                            <!-- No. Dokumen Referensi -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($r->transaksi)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-mono font-bold rounded text-[11px] border border-gray-200 dark:border-gray-600">
                                        {{ $r->transaksi->no_referensi }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-mono">-</span>
                                @endif
                            </td>

                            <!-- Lokasi Gudang -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $r->gudang->nama_gudang ?? '-' }}
                                </div>
                            </td>

                            <!-- Produk & SKU -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900 dark:text-white text-xs leading-tight">
                                    {{ $r->barang->nama_barang ?? '-' }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-[10px] bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 px-1.5 py-0.2 rounded font-semibold">
                                        {{ $r->barang->sku ?? '-' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">&bull; {{ $r->barang->kategori ?? '' }}</span>
                                </div>
                            </td>

                            <!-- Badge Tipe Mutasi -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($r->tipe === 'masuk')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-100 rounded-full dark:bg-emerald-950/60 dark:text-emerald-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                        MASUK
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-red-800 bg-red-100 rounded-full dark:bg-red-950/60 dark:text-red-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        </svg>
                                        KELUAR
                                    </span>
                                @endif
                            </td>

                            <!-- Perubahan Unit -->
                            <td class="px-4 py-3 text-right font-mono font-black text-sm whitespace-nowrap {{ $r->tipe === 'masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $r->tipe === 'masuk' ? '+' : '-' }}{{ number_format($r->jumlah, 0, ',', '.') }}
                                <span class="text-[11px] font-normal text-gray-400 font-sans">{{ $r->barang->satuan ?? '' }}</span>
                            </td>

                            <!-- Sisa Stok Akhir di Gudang Tersebut -->
                            <td class="px-4 py-3 text-right font-mono font-black text-sm bg-gray-50/50 dark:bg-gray-700/20 whitespace-nowrap text-gray-900 dark:text-white">
                                {{ number_format($r->sisa_stok, 0, ',', '.') }}
                                <span class="text-[11px] font-normal text-gray-400 font-sans">{{ $r->barang->satuan ?? '' }}</span>
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-xs">
                                <p class="truncate text-[11px]" title="{{ $r->keterangan }}">
                                    {{ $r->keterangan ?? '-' }}
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h4 class="font-bold text-sm text-gray-600 dark:text-gray-400">Tidak ada riwayat mutasi persediaan yang sesuai</h4>
                                    <p class="text-xs text-gray-400 max-w-sm">Coba sesuaikan parameter tanggal, lokasi gudang, atau kata kunci pencarian Anda.</p>
                                    <a href="{{ route('laporan.index') }}" class="mt-2 text-xs text-blue-600 hover:underline">Reset Semua Filter</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 5. Flowbite Pagination (Mempertahankan Filter Query) -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            {{ $riwayat->withQueryString()->links() }}
        </div>
    </div>

</div>

<script>
    function setPresetDate(type) {
        const today = new Date();
        const startInput = document.getElementById('tgl_mulai');
        const endInput = document.getElementById('tgl_selesai');

        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        if (type === 'today') {
            const todayStr = formatDate(today);
            startInput.value = todayStr;
            endInput.value = todayStr;
        } else if (type === '7days') {
            const past = new Date();
            past.setDate(today.getDate() - 6);
            startInput.value = formatDate(past);
            endInput.value = formatDate(today);
        } else if (type === 'month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(today);
        }

        document.getElementById('filterForm').submit();
    }
</script>
@endsection

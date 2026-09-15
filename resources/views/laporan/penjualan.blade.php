@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header', 'Laporan Penjualan')

@section('content')
<div class="space-y-5">

    <!-- 1. Tab Navigasi & Aksi Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        <!-- Tab Navigasi Laporan -->
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.penjualan') }}"
               class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-blue-600 text-white shadow-sm transition">
                Laporan Penjualan
            </a>
            <a href="{{ route('laporan.index') }}"
               class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Laporan Mutasi Stok
            </a>
        </div>

        <!-- Tombol Ekspor CSV Excel Saja (Cetak Nota/Struk Dihapus) -->
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('laporan.penjualan.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel (CSV)
            </a>
        </div>
    </div>

    <!-- 2. Ringkasan Finansial & Volume Penjualan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <!-- Total Pendapatan / Omset -->
        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Pendapatan (Omset)</span>
            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">
                Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Hanya transaksi berstatus selesai</span>
        </div>

        <!-- Total Struk / Transaksi -->
        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Transaksi Selesai</span>
            <div class="text-xl font-bold text-gray-900 dark:text-white mt-0.5 font-mono">
                {{ number_format($stats['total_selesai'], 0, ',', '.') }}
                <span class="text-xs font-normal text-gray-400">transaksi</span>
            </div>
            <span class="text-[10px] text-red-500 font-medium">
                @if($stats['total_dibatalkan'] > 0)
                    {{ $stats['total_dibatalkan'] }} transaksi dibatalkan (void)
                @else
                    Semua transaksi valid
                @endif
            </span>
        </div>

        <!-- Total Produk Terjual -->
        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Barang Terjual</span>
            <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">
                {{ number_format($stats['total_item_terjual'], 0, ',', '.') }}
                <span class="text-xs font-normal text-gray-400">pcs</span>
            </div>
            <span class="text-[10px] text-gray-400">Kuantitas fisik produk keluar</span>
        </div>

        <!-- Rata-rata Pembelian per Transaksi -->
        <div class="p-3.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Rata-Rata Transaksi</span>
            <div class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-0.5 font-mono">
                Rp {{ number_format($stats['rata_rata_transaksi'], 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">Nilai rata-rata belanja</span>
        </div>
    </div>

    <!-- 3. Form Filter Penjualan Sederhana & Rapi -->
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 print:hidden">
        <form method="GET" action="{{ route('laporan.penjualan') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Gudang Kasir -->
                <div>
                    <label for="gudang_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Lokasi Gudang Kasir
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

                <!-- Pelanggan -->
                <div>
                    <label for="pelanggan_id" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Pelanggan / Mitra
                    </label>
                    <select id="pelanggan_id" name="pelanggan_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Pelanggan</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}" {{ request('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} {{ $p->perusahaan ? '(' . $p->perusahaan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Transaksi -->
                <div>
                    <label for="status" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Status Transaksi
                    </label>
                    <select id="status" name="status"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Hanya Selesai (Sukses)</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Hanya Dibatalkan (Void)</option>
                    </select>
                </div>

                <!-- Periode Tanggal -->
                <div>
                    <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Rentang Tanggal (WIB)
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

            <!-- Baris Pencarian Kata Kunci & Tombol Filter -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-1">
                <div class="w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no ref, kasir, atau pelanggan..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if(request()->anyFilled(['gudang_id', 'pelanggan_id', 'status', 'tgl_mulai', 'tgl_selesai', 'search']))
                        <a href="{{ route('laporan.penjualan') }}" 
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

    <!-- 4. Tabel Rekap Penjualan -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                <thead class="text-[11px] uppercase bg-gray-50 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Waktu (WIB)</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">No. Referensi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Gudang Kasir</th>
                        <th scope="col" class="px-4 py-3">Pelanggan</th>
                        <th scope="col" class="px-4 py-3">Rincian Item</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Total Bayar</th>
                        <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 font-sans">
                    @forelse($transaksi as $t)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <!-- Waktu WIB -->
                            <td class="px-4 py-2.5 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                                {{ $t->created_at ? $t->created_at->format('d/m/Y H:i') : ($t->tanggal ? date('d/m/Y H:i', strtotime($t->tanggal)) : '-') }}
                            </td>

                            <!-- No. Referensi -->
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-gray-900 dark:text-white">
                                    {{ $t->no_referensi }}
                                </span>
                            </td>

                            <!-- Gudang Kasir -->
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                {{ $t->gudangAsal->nama_gudang ?? '-' }}
                            </td>

                            <!-- Pelanggan & Kasir / Petugas -->
                            <td class="px-4 py-2.5">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $t->pelanggan->nama ?? 'Pelanggan Umum' }}
                                </span>
                                @if(optional($t->pelanggan)->no_hp)
                                    <span class="text-[10px] text-gray-400 font-mono block">Telp: {{ $t->pelanggan->no_hp }}</span>
                                @endif
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    Petugas: {{ $t->user->name ?? '-' }}
                                </div>
                            </td>

                            <!-- Rincian Produk Singkat -->
                            <td class="px-4 py-2.5 max-w-xs">
                                <div class="truncate text-[11px] text-gray-700 dark:text-gray-300" title="{{ $t->details->map(fn($d) => ($d->barang->nama_barang ?? 'Item') . ' (' . $d->jumlah . 'x)')->implode(', ') }}">
                                    @foreach($t->details->take(2) as $d)
                                        <span class="inline-block bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-[10px] mr-1">
                                            {{ $d->barang->nama_barang ?? 'Item' }} <strong class="text-blue-600 dark:text-blue-400">({{ $d->jumlah }}x)</strong>
                                        </span>
                                    @endforeach
                                    @if($t->details->count() > 2)
                                        <span class="text-[10px] text-gray-400 font-semibold">+{{ $t->details->count() - 2 }} item</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Total Bayar -->
                            <td class="px-4 py-2.5 text-right font-mono font-bold whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                @if ($t->status === 'selesai')
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                                        SELESAI
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-50 border border-red-200 rounded dark:bg-red-950/40 dark:text-red-300 dark:border-red-800">
                                        DIBATALKAN
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi: Tombol Detail (Memunculkan Modal Rincian Pesanan) -->
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                <button type="button"
                                        data-modal-target="modal-detail-{{ $t->id }}"
                                        data-modal-toggle="modal-detail-{{ $t->id }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail Transaksi Penjualan -->
                        <div id="modal-detail-{{ $t->id }}" tabindex="-1" aria-hidden="true"
                             class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-2xl max-h-full">
                                <div class="relative bg-white rounded-xl shadow-xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    
                                    <!-- Modal Header -->
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200 rounded-t dark:border-gray-700">
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <span>Detail Pesanan Penjualan</span>
                                                <span class="font-mono text-blue-600 dark:text-blue-400">#{{ $t->no_referensi }}</span>
                                            </h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                Waktu: {{ $t->created_at ? $t->created_at->format('d F Y, H:i:s') : date('d F Y, H:i', strtotime($t->tanggal)) }} WIB
                                            </p>
                                        </div>
                                        <button type="button" data-modal-hide="modal-detail-{{ $t->id }}"
                                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="p-5 space-y-4">
                                        <!-- Ringkasan Metadata Transaksi -->
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-xs">
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400 text-[10px] uppercase font-bold">Gudang Kasir</span>
                                                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $t->gudangAsal->nama_gudang ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400 text-[10px] uppercase font-bold">Kasir</span>
                                                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $t->user->name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400 text-[10px] uppercase font-bold">Pelanggan / Tujuan Kirim</span>
                                                <p class="font-semibold text-gray-900 dark:text-white mt-0.5">{{ $t->pelanggan->nama ?? 'Pelanggan Umum' }}</p>
                                                @if(optional($t->pelanggan)->no_hp)
                                                    <p class="text-[10px] text-gray-400 font-mono">Telp: {{ $t->pelanggan->no_hp }}</p>
                                                @endif
                                                @if(optional($t->pelanggan)->alamat)
                                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Alamat: {{ $t->pelanggan->alamat }}</p>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400 text-[10px] uppercase font-bold">Status</span>
                                                <div class="mt-0.5">
                                                    @if ($t->status === 'selesai')
                                                        <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded dark:bg-emerald-950/60 dark:text-emerald-300">
                                                            SELESAI
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-100 rounded dark:bg-red-950/60 dark:text-red-300">
                                                            DIBATALKAN
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($t->catatan)
                                            <div class="text-xs text-gray-600 dark:text-gray-300 bg-amber-50 dark:bg-amber-950/30 p-2.5 rounded border border-amber-200 dark:border-amber-900">
                                                <strong class="font-semibold">Catatan:</strong> {{ $t->catatan }}
                                            </div>
                                        @endif

                                        <!-- Tabel Rincian Item yang Dibeli -->
                                        <div>
                                            <h4 class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300 mb-2">Rincian Barang yang Dibeli:</h4>
                                            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                                                    <thead class="text-[10px] uppercase bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        <tr>
                                                            <th scope="col" class="px-3 py-2 text-center w-8">No</th>
                                                            <th scope="col" class="px-3 py-2">Produk</th>
                                                            <th scope="col" class="px-3 py-2 text-right">Harga</th>
                                                            <th scope="col" class="px-3 py-2 text-center">Qty</th>
                                                            <th scope="col" class="px-3 py-2 text-right">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                        @php $subtotalTotal = 0; @endphp
                                                        @foreach($t->details as $index => $detail)
                                                            @php 
                                                                $harga = $detail->harga > 0 ? $detail->harga : ($detail->barang->harga_jual ?? 0);
                                                                $subtotal = $detail->subtotal > 0 ? $detail->subtotal : ($harga * $detail->jumlah);
                                                                $subtotalTotal += $subtotal;
                                                            @endphp
                                                            <tr>
                                                                <td class="px-3 py-2 text-center text-gray-400">{{ $index + 1 }}</td>
                                                                <td class="px-3 py-2">
                                                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $detail->barang->nama_barang ?? 'Item' }}</div>
                                                                    <div class="text-[10px] text-gray-400 font-mono">{{ $detail->barang->sku ?? '-' }}</div>
                                                                </td>
                                                                <td class="px-3 py-2 text-right font-mono text-gray-700 dark:text-gray-300">
                                                                    Rp {{ number_format($harga, 0, ',', '.') }}
                                                                </td>
                                                                <td class="px-3 py-2 text-center font-bold font-mono">
                                                                    {{ $detail->jumlah }}
                                                                </td>
                                                                <td class="px-3 py-2 text-right font-mono font-bold text-gray-900 dark:text-white">
                                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-gray-50 dark:bg-gray-700/60 font-semibold text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700">
                                                        <tr>
                                                            <td colspan="4" class="px-3 py-2.5 text-right uppercase text-xs">Total Pembayaran:</td>
                                                            <td class="px-3 py-2.5 text-right font-mono text-sm font-black text-blue-600 dark:text-blue-400">
                                                                Rp {{ number_format($t->total_bayar > 0 ? $t->total_bayar : $subtotalTotal, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                                        <button data-modal-hide="modal-detail-{{ $t->id }}" type="button"
                                                class="text-gray-700 bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-xs px-4 py-2 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
                                            Tutup
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                <p class="text-sm font-medium">Tidak ada data transaksi penjualan yang sesuai dengan filter.</p>
                                <p class="text-xs mt-1">Coba sesuaikan gudang, status, atau rentang tanggal di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transaksi->hasPages())
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                {{ $transaksi->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

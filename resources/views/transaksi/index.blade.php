@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Semua Transaksi')

@section('content')
    <div
        class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Filter Tabs & Tombol Aksi Cepat -->
        <div
            class="flex flex-col md:flex-row items-center justify-between p-4 gap-4 border-b border-gray-200 dark:border-gray-700">
            <!-- Filter Kategori Transaksi -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transaksi.index') }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ !request('jenis') ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Semua
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'jual']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ request('jenis') == 'jual' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Penjualan (Kasir)
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'masuk']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ request('jenis') == 'masuk' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Barang Masuk
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'transfer']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ request('jenis') == 'transfer' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Transfer Gudang
                </a>
            </div>

            <!-- Tombol Pintasan Transaksi Baru -->
            <div class="flex gap-2">
                <a href="{{ route('transaksi.jual') }}"
                    class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-xs px-3 py-2 flex items-center gap-1 shadow-sm">
                    + Kasir
                </a>
                <a href="{{ route('transaksi.masuk') }}"
                    class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-xs px-3 py-2 flex items-center gap-1 shadow-sm">
                    + Masuk
                </a>
                <a href="{{ route('transaksi.transfer') }}"
                    class="text-white bg-purple-600 hover:bg-purple-700 font-medium rounded-lg text-xs px-3 py-2 flex items-center gap-1 shadow-sm">
                    + Transfer
                </a>
            </div>
        </div>

        <!-- Tabel Riwayat Semua Transaksi -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3">No Referensi</th>
                        <th scope="col" class="px-4 py-3">Tanggal</th>
                        <th scope="col" class="px-4 py-3">Jenis</th>
                        <th scope="col" class="px-4 py-3">Gudang / Tujuan</th>
                        <th scope="col" class="px-4 py-3">Pihak Terkait</th>
                        <th scope="col" class="px-4 py-3">Total Nominal</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                        <th scope="col" class="px-4 py-3 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600/50">
                            {{-- No Referensi --}}
                            <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $t->no_referensi }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ date('d/m/Y', strtotime($t->tanggal)) }}
                            </td>

                            {{-- Jenis Transaksi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($t->jenis === 'jual')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Penjualan</span>
                                @elseif($t->jenis === 'masuk')
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Penerimaan</span>
                                @else
                                    <span
                                        class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Transfer</span>
                                @endif
                            </td>

                            {{-- Gudang / Rute Tujuan --}}
                            <td class="px-4 py-3 text-xs">
                                @if ($t->jenis === 'jual')
                                    Dari: <span
                                        class="font-medium text-gray-900 dark:text-white">{{ $t->gudangAsal->nama_gudang ?? '-' }}</span>
                                @elseif($t->jenis === 'masuk')
                                    Ke: <span
                                        class="font-medium text-gray-900 dark:text-white">{{ $t->gudangTujuan->nama_gudang ?? '-' }}</span>
                                @else
                                    {{ $t->gudangAsal->nama_gudang ?? '-' }} &rarr;
                                    {{ $t->gudangTujuan->nama_gudang ?? '-' }}
                                @endif
                            </td>

                            {{-- Pihak Terkait --}}
                            <td class="px-4 py-3">
                                {{ $t->pelanggan->nama ?? ($t->user->name ?? '-') }}
                            </td>

                            {{-- Total Nominal (Dengan fallback kalkulasi dari details) --}}
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                @php
                                    $nominal = $t->total_bayar > 0 ? $t->total_bayar : $t->details->sum('subtotal');
                                @endphp

                                @if ($nominal > 0)
                                    Rp {{ number_format($nominal, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 font-normal">Rp 0</span>
                                @endif
                            </td>

                            {{-- Status Transaksi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($t->status === 'selesai')
                                    <span
                                        class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">Selesai</span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Dibatalkan</span>
                                @endif
                            </td>

                            {{-- Aksi (Batalkan) --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($t->status === 'selesai')
                                    <form action="{{ route('transaksi.batal', $t->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi {{ $t->no_referensi }}? Seluruh stok mutasi akan otomatis dikembalikan.');">
                                        @csrf
                                        <button type="submit"
                                            class="text-white bg-red-600 hover:bg-red-700 font-medium rounded text-xs px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-red-400">
                                            Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Void</span>
                                @endif
                            </td>

                            {{-- Detail (Button & Modal Flowbite) --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button type="button" data-modal-target="modal-detail-{{ $t->id }}"
                                    data-modal-toggle="modal-detail-{{ $t->id }}"
                                    class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded text-xs px-2.5 py-1 inline-flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>

                                <!-- Flowbite Pop-up Modal Detail -->
                                <div id="modal-detail-{{ $t->id }}" tabindex="-1" aria-hidden="true"
                                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                    <div class="relative w-full max-w-2xl max-h-full text-left">
                                        <div
                                            class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">

                                            <!-- Header Modal -->
                                            <div
                                                class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                                                <div>
                                                    <h3
                                                        class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                        <span>{{ $t->no_referensi }}</span>
                                                        @if ($t->jenis === 'jual')
                                                            <span
                                                                class="bg-green-100 text-green-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">PENJUALAN</span>
                                                        @elseif($t->jenis === 'masuk')
                                                            <span
                                                                class="bg-blue-100 text-blue-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">PENERIMAAN</span>
                                                        @else
                                                            <span
                                                                class="bg-purple-100 text-purple-800 text-[10px] font-semibold px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">TRANSFER
                                                                GUDANG</span>
                                                        @endif
                                                    </h3>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                        Tanggal: {{ date('d F Y', strtotime($t->tanggal)) }} &bull; Dibuat
                                                        Oleh: {{ $t->user->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <button type="button"
                                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                                    data-modal-hide="modal-detail-{{ $t->id }}">
                                                    <svg class="w-3 h-3" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                    </svg>
                                                    <span class="sr-only">Tutup modal</span>
                                                </button>
                                            </div>

                                            <!-- Body Modal -->
                                            <div class="p-5 space-y-4">
                                                <!-- Ringkasan Rute & Pihak -->
                                                <div
                                                    class="grid grid-cols-2 gap-3 text-xs bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-600">
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Pihak /
                                                            Mitra:</span>
                                                        <p class="font-bold text-gray-900 dark:text-white">
                                                            {{ $t->pelanggan->nama ?? ($t->user->name ?? 'Internal Perusahaan') }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500 dark:text-gray-400">Lokasi /
                                                            Gudang:</span>
                                                        <p class="font-bold text-gray-900 dark:text-white">
                                                            @if ($t->jenis === 'jual')
                                                                {{ $t->gudangAsal->nama_gudang ?? '-' }}
                                                            @elseif($t->jenis === 'masuk')
                                                                {{ $t->gudangTujuan->nama_gudang ?? '-' }}
                                                            @else
                                                                {{ $t->gudangAsal->nama_gudang ?? '-' }} &rarr;
                                                                {{ $t->gudangTujuan->nama_gudang ?? '-' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    @if ($t->catatan)
                                                        <div
                                                            class="col-span-2 pt-1 border-t border-gray-200 dark:border-gray-600">
                                                            <span class="text-gray-500 dark:text-gray-400">Catatan:</span>
                                                            <p class="text-gray-800 dark:text-gray-200">
                                                                {{ $t->catatan }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Tabel Rincian Barang -->
                                                <div
                                                    class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                                                    <table
                                                        class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                                        <thead
                                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                                            <tr>
                                                                <th scope="col" class="px-3 py-2.5">Produk</th>
                                                                <th scope="col" class="px-3 py-2.5 text-center">Jumlah
                                                                </th>
                                                                <th scope="col" class="px-3 py-2.5 text-right">Harga
                                                                    Satuan</th>
                                                                <th scope="col" class="px-3 py-2.5 text-right">Subtotal
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                            @forelse($t->details as $d)
                                                                <tr
                                                                    class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                                    <td
                                                                        class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                                                        {{ $d->barang->nama_barang ?? 'Barang #' . $d->barang_id }}
                                                                        <span
                                                                            class="block text-[10px] text-gray-400 font-mono">{{ $d->barang->sku ?? '' }}</span>
                                                                    </td>
                                                                    <td class="px-3 py-2 text-center font-bold">
                                                                        {{ $d->jumlah }}
                                                                        {{ $d->barang->satuan ?? 'Unit' }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-right">
                                                                        Rp
                                                                        {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                                                    </td>
                                                                    <td
                                                                        class="px-3 py-2 text-right font-bold text-gray-900 dark:text-white">
                                                                        Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4"
                                                                        class="px-3 py-4 text-center text-gray-400">Rincian
                                                                        item tidak ditemukan.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Ringkasan Total Tagihan / Valuasi -->
                                                <div
                                                    class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                                    <span
                                                        class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                                                        {{ $t->jenis === 'transfer' ? 'Total Valuasi Aset Dipindahkan:' : 'Total Transaksi:' }}
                                                    </span>
                                                    <span
                                                        class="text-base font-extrabold text-blue-700 dark:text-blue-400">
                                                        Rp {{ number_format($nominal, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Footer Modal -->
                                            <div
                                                class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                                                <button data-modal-hide="modal-detail-{{ $t->id }}" type="button"
                                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-xs font-medium px-4 py-2 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
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
                            <td colspan="9" class="px-4 py-6 text-center text-gray-400">Belum ada catatan transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $transaksi->links() }}
        </div>
    </div>
@endsection

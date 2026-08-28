@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Semua Transaksi')

@section('content')
    <div
        class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Filter Tabs & Actions -->
        <div
            class="flex flex-col md:flex-row items-center justify-between p-4 gap-4 border-b border-gray-200 dark:border-gray-700">
            <!-- Filter Kategori Transaksi -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transaksi.index') }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg {{ !request('jenis') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                    Semua
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'jual']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg {{ request('jenis') == 'jual' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                    Penjualan (Kasir)
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'masuk']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg {{ request('jenis') == 'masuk' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                    Barang Masuk
                </a>
                <a href="{{ route('transaksi.index', ['jenis' => 'transfer']) }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg {{ request('jenis') == 'transfer' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                    Transfer Gudang
                </a>
            </div>

            <!-- Tombol Aksi Cepat -->
            <div class="flex gap-2">
                <a href="{{ route('transaksi.jual') }}"
                    class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-xs px-3 py-2">
                    + Kasir
                </a>
                <a href="{{ route('transaksi.masuk') }}"
                    class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-xs px-3 py-2">
                    + Masuk
                </a>
                <a href="{{ route('transaksi.transfer') }}"
                    class="text-white bg-purple-600 hover:bg-purple-700 font-medium rounded-lg text-xs px-3 py-2">
                    + Transfer
                </a>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi -->
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $t->no_referensi }}
                            </td>
                            <td class="px-4 py-3">{{ date('d/m/Y', strtotime($t->tanggal)) }}</td>
                            <td class="px-4 py-3">
                                @if ($t->jenis === 'jual')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Penjualan</span>
                                @elseif($t->jenis === 'masuk')
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Penerimaan</span>
                                @else
                                    <span
                                        class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Transfer</span>
                                @endif
                            </td>
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
                            <td class="px-4 py-3">{{ $t->pelanggan->nama ?? ($t->user->name ?? '-') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $t->total_bayar > 0 ? 'Rp ' . number_format($t->total_bayar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($t->status === 'selesai')
                                    <span
                                        class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">Selesai</span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($t->status === 'selesai')
                                    <form action="{{ route('transaksi.batal', $t->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi ini? Stok akan otomatis dikembalikan.');">
                                        @csrf
                                        <button type="submit"
                                            class="text-white bg-red-600 hover:bg-red-700 font-medium rounded text-xs px-2.5 py-1">
                                            Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Void</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada catatan transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $transaksi->links() }}
        </div>
    </div>
@endsection

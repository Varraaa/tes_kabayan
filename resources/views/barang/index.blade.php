@extends('layouts.app')

@section('title', 'Master Barang')
@section('header', 'Master Data Barang')

@section('content')
    <div
        class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Header & Tombol Tambah -->
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Katalog Barang Toko</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola informasi SKU, harga pokok, harga jual, dan status
                    barang </p>
            </div>
            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                <a href="{{ route('barang.create') }}"
                    class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                    </svg>
                    Tambah Barang Baru
                </a>
            </div>
        </div>

        <!-- Tabel Flowbite -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3">SKU</th>
                        <th scope="col" class="px-4 py-3">Nama Barang</th>
                        <th scope="col" class="px-4 py-3">Kategori</th>
                        <th scope="col" class="px-4 py-3">Satuan</th>
                        <th scope="col" class="px-4 py-3">Harga Pokok</th>
                        <th scope="col" class="px-4 py-3">Harga Jual</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $item)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $item->sku }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->nama_barang }}</td>
                            <td class="px-4 py-3">{{ $item->kategori }}</td>
                            <td class="px-4 py-3">{{ $item->satuan }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($item->harga_pokok, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">Rp
                                {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if ($item->status_aktif)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Aktif</span>
                                @else
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex items-center justify-center space-x-2">
                                <a href="{{ route('barang.edit', $item->id) }}"
                                    class="text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-amber-600 dark:hover:bg-amber-700 focus:outline-none">
                                    Edit
                                </a>
                                <form action="{{ route('barang.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada data barang. Silakan
                                tambahkan barang baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $barang->links() }}
        </div>
    </div>
@endsection

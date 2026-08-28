@extends('layouts.app')

@section('title', 'Master Pelanggan')
@section('header', 'Master Data Pelanggan')

@section('content')
    <div
        class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Pelanggan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kelola kontak dan alamat pelanggan untuk transaksi
                    kasir/penjualan</p>
            </div>
            <div class="w-full md:w-auto flex justify-end">
                <a href="{{ route('pelanggan.create') }}"
                    class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
                    <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                    </svg>
                    Tambah Pelanggan Baru
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">Nama Pelanggan</th>
                        <th scope="col" class="px-4 py-3">No. Telepon / WhatsApp</th>
                        <th scope="col" class="px-4 py-3">Alamat</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggan as $index => $item)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-semibold">{{ $pelanggan->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->nama }}</td>
                            <td class="px-4 py-3 font-mono">{{ $item->no_hp ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->alamat ?? '-' }}</td>
                            <td class="px-4 py-3 flex items-center justify-center space-x-2">
                                <a href="{{ route('pelanggan.edit', $item->id) }}"
                                    class="text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-amber-600 dark:hover:bg-amber-700">
                                    Edit
                                </a>
                                <form action="{{ route('pelanggan.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data pelanggan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-red-600 dark:hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $pelanggan->links() }}
        </div>
    </div>
@endsection

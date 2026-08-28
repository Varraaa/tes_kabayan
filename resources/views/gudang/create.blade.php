@extends('layouts.app')

@section('title', 'Tambah Gudang')
@section('header', 'Tambah Lokasi Gudang')

@section('content')
    <div class="max-w-2xl bg-white p-6 rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Formulir Gudang Baru</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftarkan lokasi penyimpanan atau cabang baru</p>
        </div>

        <form action="{{ route('gudang.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label for="nama_gudang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Gudang
                        / Cabang</label>
                    <input type="text" name="nama_gudang" id="nama_gudang" value="{{ old('nama_gudang') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Contoh: Gudang Cabang Bandung Timur">
                </div>

                <div>
                    <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat
                        Lengkap</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Masukkan alamat atau detail lokasi gudang...">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="status_aktif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                        Operasional</label>
                    <select name="status_aktif" id="status_aktif" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif / Beroperasi
                        </option>
                        <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Non-Aktif / Ditutup
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Simpan Gudang
                </button>
                <a href="{{ route('gudang.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

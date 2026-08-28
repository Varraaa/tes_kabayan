@extends('layouts.app')

@section('title', 'Edit Gudang')
@section('header', 'Edit Data Gudang')

@section('content')
    <div class="max-w-2xl bg-white p-6 rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ubah Data: {{ $gudang->nama_gudang }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui nama lokasi, alamat, atau status operasional gudang
            </p>
        </div>

        <form action="{{ route('gudang.update', $gudang->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4 mb-6">
                <div>
                    <label for="nama_gudang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Gudang
                        / Cabang</label>
                    <input type="text" name="nama_gudang" id="nama_gudang"
                        value="{{ old('nama_gudang', $gudang->nama_gudang) }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat
                        Lengkap</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('alamat', $gudang->alamat) }}</textarea>
                </div>

                <div>
                    <label for="status_aktif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                        Operasional</label>
                    <select name="status_aktif" id="status_aktif" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1" {{ old('status_aktif', $gudang->status_aktif) == 1 ? 'selected' : '' }}>Aktif
                            / Beroperasi</option>
                        <option value="0" {{ old('status_aktif', $gudang->status_aktif) == 0 ? 'selected' : '' }}>
                            Non-Aktif / Ditutup</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                    class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-amber-500 dark:hover:bg-amber-600">
                    Perbarui Gudang
                </button>
                <a href="{{ route('gudang.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

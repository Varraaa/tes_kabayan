@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('header', 'Tambah Barang Baru')

@section('content')
    <div class="max-w-3xl bg-white p-6 rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Formulir Data Produk</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Isi parameter barang untuk didaftarkan ke sistem inventaris
            </p>
        </div>

        <form action="{{ route('barang.store') }}" method="POST">
            @csrf
            <div class="grid gap-4 mb-6 sm:grid-cols-2">
                <div>
                    <label for="sku" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode SKU /
                        Barcode</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Contoh: BRG-001">
                </div>

                <div>
                    <label for="nama_barang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                        Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Contoh: Kertas HVS A4 75gr">
                </div>

                <div>
                    <label for="kategori"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Contoh: Alat Tulis Kantor, Packaging">
                </div>

                <div>
                    <label for="satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Satuan
                        Hitung</label>
                    <select name="satuan" id="satuan" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="Box" {{ old('satuan') == 'Box' ? 'selected' : '' }}>Box</option>
                        <option value="Dus" {{ old('satuan') == 'Dus' ? 'selected' : '' }}>Dus</option>
                        <option value="Rim" {{ old('satuan') == 'Rim' ? 'selected' : '' }}>Rim</option>
                        <option value="Roll" {{ old('satuan') == 'Roll' ? 'selected' : '' }}>Roll</option>
                        <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack</option>
                    </select>
                </div>

                <div>
                    <label for="harga_pokok" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga
                        Pokok / Modal (Rp)</label>
                    <input type="number" step="0.01" name="harga_pokok" id="harga_pokok"
                        value="{{ old('harga_pokok') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="0">
                </div>

                <div>
                    <label for="harga_jual" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Jual
                        (Rp)</label>
                    <input type="number" step="0.01" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="0">
                </div>

                <div>
                    <label for="status_aktif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                        Produk</label>
                    <select name="status_aktif" id="status_aktif" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif Dijual
                        </option>
                        <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">
                    Simpan Barang
                </button>
                <a href="{{ route('barang.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

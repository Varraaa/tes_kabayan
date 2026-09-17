@extends('layouts.app')

@section('title', 'Edit Barang')
@section('header', 'Edit Data Barang')

@section('content')
    <div class="max-w-3xl bg-white p-6 rounded-lg shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ubah Data: {{ $barang->nama_barang }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui atribut SKU, harga, atau status aktif produk</p>
        </div>

        <form action="{{ route('barang.update', $barang->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-4 mb-6 sm:grid-cols-2">
                <div>
                    <label for="sku" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode SKU /
                        Barcode</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $barang->sku) }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="nama_barang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                        Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang"
                        value="{{ old('nama_barang', $barang->nama_barang) }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="kategori"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $barang->kategori) }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Satuan
                        Hitung</label>
                    <select name="satuan" id="satuan" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach (['Pcs', 'Box', 'Dus', 'Rim', 'Roll', 'Pack'] as $sat)
                            <option value="{{ $sat }}"
                                {{ old('satuan', $barang->satuan) == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $rawPokok = old('harga_pokok', $barang->harga_pokok);
                    $valPokok = is_numeric($rawPokok) 
                        ? number_format((float)$rawPokok, 0, ',', '.') 
                        : (preg_replace('/[^0-9]/', '', (string)$rawPokok) ? number_format((float)preg_replace('/[^0-9]/', '', (string)$rawPokok), 0, ',', '.') : '');

                    $rawJual = old('harga_jual', $barang->harga_jual);
                    $valJual = is_numeric($rawJual) 
                        ? number_format((float)$rawJual, 0, ',', '.') 
                        : (preg_replace('/[^0-9]/', '', (string)$rawJual) ? number_format((float)preg_replace('/[^0-9]/', '', (string)$rawJual), 0, ',', '.') : '');
                @endphp

                <div>
                    <label for="harga_pokok" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Harga Pokok / Modal (Rp)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-500 dark:text-gray-400 font-semibold text-sm">
                            Rp
                        </div>
                        <input type="text" name="harga_pokok" id="harga_pokok"
                            value="{{ $valPokok }}" required
                            class="format-rupiah bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-11 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono"
                            placeholder="Contoh: 60.000" autocomplete="off">
                    </div>
                </div>

                <div>
                    <label for="harga_jual" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Harga Jual (Rp)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-500 dark:text-gray-400 font-semibold text-sm">
                            Rp
                        </div>
                        <input type="text" name="harga_jual" id="harga_jual"
                            value="{{ $valJual }}" required
                            class="format-rupiah bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-11 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono"
                            placeholder="Contoh: 75.000" autocomplete="off">
                    </div>
                </div>

                <div>
                    <label for="status_aktif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                        Produk</label>
                    <select name="status_aktif" id="status_aktif" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="1" {{ old('status_aktif', $barang->status_aktif) == 1 ? 'selected' : '' }}>
                            Aktif Dijual</option>
                        <option value="0" {{ old('status_aktif', $barang->status_aktif) == 0 ? 'selected' : '' }}>
                            Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                    class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-500 dark:hover:bg-amber-600">
                    Perbarui Barang
                </button>
                <a href="{{ route('barang.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function formatRupiahInput(input) {
            let clean = input.value.replace(/[^0-9]/g, '');
            if (clean) {
                input.value = parseInt(clean, 10).toLocaleString('id-ID');
            } else {
                input.value = '';
            }
        }

        document.querySelectorAll('.format-rupiah').forEach(el => {
            el.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            if (el.value) {
                formatRupiahInput(el);
            }
        });
    </script>
@endsection

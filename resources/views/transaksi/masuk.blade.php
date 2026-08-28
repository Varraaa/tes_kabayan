@extends('layouts.app')

@section('title', 'Penerimaan Barang')
@section('header', 'Input Barang Masuk (Restock)')

@section('content')
    <form action="{{ route('transaksi.masuk.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Tabel Item Penerimaan -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Barang Masuk</h2>
                    <button type="button" onclick="tambahBaris()"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-xs px-3 py-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Produk</th>
                                <th class="px-3 py-2 w-32">Harga Beli / Satuan</th>
                                <th class="px-3 py-2 w-24">Jumlah</th>
                                <th class="px-3 py-2 text-center w-12">#</th>
                            </tr>
                        </thead>
                        <tbody id="itemContainer">
                            <tr class="item-row border-b dark:border-gray-700">
                                <td class="p-2">
                                    <select name="items[0][barang_id]" onchange="setHargaPokok(this)" required
                                        class="barang-select bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->id }}" data-pokok="{{ $b->harga_pokok }}">
                                                {{ $b->sku }} - {{ $b->nama_barang }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" name="items[0][harga]" required
                                        class="harga-input bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" name="items[0][jumlah]" value="1" min="1" required
                                        class="jumlah-input bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="hapusBaris(this)"
                                        class="text-red-600 hover:text-red-800 font-bold">&times;</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Parameter Gudang Tujuan -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700 h-fit space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 dark:border-gray-700">Lokasi
                    Penerimaan</h2>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Gudang Tujuan</label>
                    <select name="gudang_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Tanggal Masuk</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Catatan / Supplier</label>
                    <textarea name="catatan" rows="3" placeholder="Nama supplier, nomor surat jalan..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <button type="submit"
                    class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-sm px-5 py-3 shadow-lg transition">
                    Simpan Penerimaan Barang
                </button>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = 1;

        function setHargaPokok(selectEl) {
            const row = selectEl.closest('tr');
            const pokok = selectEl.options[selectEl.selectedIndex].dataset.pokok || 0;
            row.querySelector('.harga-input').value = pokok;
        }

        function tambahBaris() {
            const container = document.getElementById('itemContainer');
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.barang-select').name = `items[${rowIndex}][barang_id]`;
            newRow.querySelector('.barang-select').value = '';
            newRow.querySelector('.harga-input').name = `items[${rowIndex}][harga]`;
            newRow.querySelector('.harga-input').value = '';
            newRow.querySelector('.jumlah-input').name = `items[${rowIndex}][jumlah]`;
            newRow.querySelector('.jumlah-input').value = '1';

            container.appendChild(newRow);
            rowIndex++;
        }

        function hapusBaris(btn) {
            const container = document.getElementById('itemContainer');
            if (container.querySelectorAll('.item-row').length > 1) {
                btn.closest('tr').remove();
            }
        }
    </script>
@endsection

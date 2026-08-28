@extends('layouts.app')

@section('title', 'Transfer Antar Gudang')
@section('header', 'Mutasi Antar Gudang')

@section('content')
    <form action="{{ route('transaksi.transfer.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Pilihan Barang yang Ditransfer -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Barang yang Dipindahkan</h2>
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
                                <th class="px-3 py-2">Pilih Barang</th>
                                <th class="px-3 py-2 w-32">Jumlah Mutasi</th>
                                <th class="px-3 py-2 text-center w-12">#</th>
                            </tr>
                        </thead>
                        <tbody id="itemContainer">
                            <tr class="item-row border-b dark:border-gray-700">
                                <td class="p-2">
                                    <select name="items[0][barang_id]" required
                                        class="barang-select bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->id }}">{{ $b->sku }} - {{ $b->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
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

            <!-- Rute Mutasi Gudang -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700 h-fit space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 dark:border-gray-700">Rute
                    Pengiriman</h2>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Gudang Asal
                        (Pengirim)</label>
                    <select name="gudang_asal_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Gudang Tujuan
                        (Penerima)</label>
                    <select name="gudang_tujuan_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}" {{ $loop->last ? 'selected' : '' }}>{{ $g->nama_gudang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengiriman</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Catatan Mutasi</label>
                    <textarea name="catatan" rows="2" placeholder="Keterangan transfer..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <button type="submit"
                    class="w-full text-white bg-purple-600 hover:bg-purple-700 font-bold rounded-lg text-sm px-5 py-3 shadow-lg transition">
                    Eksekusi Transfer Stok
                </button>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = 1;

        function tambahBaris() {
            const container = document.getElementById('itemContainer');
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.barang-select').name = `items[${rowIndex}][barang_id]`;
            newRow.querySelector('.barang-select').value = '';
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

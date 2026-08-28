@extends('layouts.app')

@section('title', 'Kasir / Penjualan')
@section('header', 'Point of Sales (Kasir)')

@section('content')
    <form action="{{ route('transaksi.jual.store') }}" method="POST" id="posForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Panel Kiri: Input Item Barang -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Item Belanja</h2>
                    <button type="button" onclick="tambahBaris()"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-xs px-3 py-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="tabelItems">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Pilih Produk</th>
                                <th class="px-3 py-2 w-28">Harga Satuan</th>
                                <th class="px-3 py-2 w-24">Jumlah</th>
                                <th class="px-3 py-2 w-32">Subtotal</th>
                                <th class="px-3 py-2 text-center w-12">#</th>
                            </tr>
                        </thead>
                        <tbody id="itemContainer">
                            <tr class="item-row border-b dark:border-gray-700">
                                <td class="p-2">
                                    <select name="items[0][barang_id]" onchange="updateHarga(this)" required
                                        class="barang-select bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->id }}" data-harga="{{ $b->harga_jual }}">
                                                {{ $b->sku }} - {{ $b->nama_barang }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="text" readonly
                                        class="harga-satuan bg-gray-100 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 font-mono dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                        value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" name="items[0][jumlah]" value="1" min="1"
                                        oninput="hitungSubtotal(this)" required
                                        class="jumlah-input bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </td>
                                <td class="p-2">
                                    <input type="text" readonly
                                        class="subtotal-input bg-gray-100 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2 font-bold font-mono dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                        value="0">
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

            <!-- Panel Kanan: Parameter & Checkout -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700 h-fit space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 dark:border-gray-700">Ringkasan
                    Transaksi</h2>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Gudang Pengeluaran</label>
                    <select name="gudang_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Pelanggan (Opsional)</label>
                    <select name="pelanggan_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Umum / Non-Member --</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->no_hp ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan transaksi..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Tagihan:</span>
                        <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-400" id="grandTotal">Rp 0</span>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-green-600 hover:bg-green-700 font-bold rounded-lg text-sm px-5 py-3 shadow-lg transition">
                        Bayar & Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = 1;

        function updateHarga(selectEl) {
            const row = selectEl.closest('tr');
            const harga = parseFloat(selectEl.options[selectEl.selectedIndex].dataset.harga || 0);
            row.querySelector('.harga-satuan').value = harga.toLocaleString('id-ID');
            row.querySelector('.harga-satuan').dataset.raw = harga;
            hitungSubtotal(row.querySelector('.jumlah-input'));
        }

        function hitungSubtotal(inputEl) {
            const row = inputEl.closest('tr');
            const harga = parseFloat(row.querySelector('.harga-satuan').dataset.raw || 0);
            const qty = parseInt(inputEl.value || 0);
            const subtotal = harga * qty;
            row.querySelector('.subtotal-input').value = subtotal.toLocaleString('id-ID');
            row.querySelector('.subtotal-input').dataset.raw = subtotal;
            hitungGrandTotal();
        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(el => {
                total += parseFloat(el.dataset.raw || 0);
            });
            document.getElementById('grandTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function tambahBaris() {
            const container = document.getElementById('itemContainer');
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.barang-select').name = `items[${rowIndex}][barang_id]`;
            newRow.querySelector('.barang-select').value = '';
            newRow.querySelector('.harga-satuan').value = '0';
            newRow.querySelector('.harga-satuan').dataset.raw = '0';
            newRow.querySelector('.jumlah-input').name = `items[${rowIndex}][jumlah]`;
            newRow.querySelector('.jumlah-input').value = '1';
            newRow.querySelector('.subtotal-input').value = '0';
            newRow.querySelector('.subtotal-input').dataset.raw = '0';

            container.appendChild(newRow);
            rowIndex++;
        }

        function hapusBaris(btn) {
            const container = document.getElementById('itemContainer');
            if (container.querySelectorAll('.item-row').length > 1) {
                btn.closest('tr').remove();
                hitungGrandTotal();
            }
        }
    </script>
@endsection

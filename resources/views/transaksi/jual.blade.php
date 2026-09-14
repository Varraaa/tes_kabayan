@extends('layouts.app')

@section('title', 'Kasir / Penjualan POS')
@section('header', 'Point of Sales (Kasir)')

@section('content')
    <!-- Banner Notifikasi Sukses & Cetak Struk Cepat -->
    @if(session('last_trx_id'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl shadow-sm dark:bg-gray-800 dark:border-emerald-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Transaksi Kasir Berhasil Disimpan!</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-300">
                        No. Referensi: <span class="font-mono font-bold text-emerald-700 dark:text-emerald-400">{{ session('last_trx_ref') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transaksi.cetak', session('last_trx_id')) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Struk Pembayaran
                </a>
            </div>
        </div>
    @endif

    <form action="{{ route('transaksi.jual.store') }}" method="POST" id="posForm" onsubmit="return validateCheckout()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Panel Kiri: Input Item Barang & Barcode Scanner -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Barcode & Pencarian Cepat Produk -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <input type="text" id="barcodeScanner" placeholder="Scan Barcode / Ketik SKU..."
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-9 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Tekan <kbd class="px-1.5 py-0.5 text-[10px] font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded dark:bg-gray-600 dark:text-gray-100">Enter</kbd> untuk cari</span>
                        <button type="button" onclick="tambahBaris()"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3.5 py-2 flex items-center gap-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Baris
                        </button>
                    </div>
                </div>

                <!-- Tabel Item Keranjang Belanja -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Daftar Item Belanja</span>
                            <span id="badgeItemCount" class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-semibold dark:bg-blue-900 dark:text-blue-300">1 Item</span>
                        </h2>
                        <!-- Status Sinkronisasi Real-Time -->
                        <div class="flex items-center gap-1.5 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Stok Terhubung Real-Time
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="tabelItems">
                            <thead class="text-[11px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-3 py-2.5">Produk & Stok Tersedia</th>
                                    <th class="px-3 py-2.5 w-32">Harga Satuan</th>
                                    <th class="px-3 py-2.5 w-24">Jumlah</th>
                                    <th class="px-3 py-2.5 w-36 text-right">Subtotal</th>
                                    <th class="px-3 py-2.5 text-center w-10">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemContainer" class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr class="item-row">
                                    <td class="p-2.5">
                                        <select name="items[0][barang_id]" onchange="handleProductChange(this)" required
                                                class="barang-select bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach ($barang as $b)
                                                <option value="{{ $b->id }}" 
                                                        data-sku="{{ $b->sku }}"
                                                        data-nama="{{ $b->nama_barang }}"
                                                        data-harga="{{ $b->harga_jual }}"
                                                        data-satuan="{{ $b->satuan }}">
                                                    {{ $b->sku }} - {{ $b->nama_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="stok-info-badge mt-1.5 flex items-center gap-1.5 text-[11px]">
                                            <span class="text-gray-400">Pilih produk untuk melihat sisa stok</span>
                                        </div>
                                    </td>
                                    <td class="p-2.5">
                                        <input type="text" readonly
                                               class="harga-satuan bg-gray-100 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 font-mono dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                               value="Rp 0">
                                        <input type="hidden" name="items[0][harga]" class="harga-raw" value="0">
                                    </td>
                                    <td class="p-2.5">
                                        <input type="number" name="items[0][jumlah]" value="1" min="1"
                                               oninput="hitungSubtotal(this)" required
                                               class="jumlah-input bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 text-center font-bold dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="p-2.5 text-right">
                                        <input type="text" readonly
                                               class="subtotal-input bg-gray-100 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 font-bold font-mono text-right text-blue-700 dark:text-blue-400 dark:bg-gray-600 dark:border-gray-500"
                                               value="Rp 0">
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <button type="button" onclick="hapusBaris(this)"
                                                title="Hapus baris"
                                                class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panel Kanan: Parameter Checkout & Kalkulator Kasir -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-fit space-y-4">
                <h2 class="text-base font-bold text-gray-900 dark:text-white border-b pb-3 dark:border-gray-700 flex items-center justify-between">
                    <span>Kasir & Pembayaran</span>
                    <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 font-semibold rounded dark:bg-blue-900 dark:text-blue-300">POS Tunai</span>
                </h2>

                <!-- Gudang Pengeluaran -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Gudang Pengeluaran Stok
                    </label>
                    <select name="gudang_id" id="gudangSelect" onchange="fetchRealtimeStock()" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-medium">
                        @foreach ($gudang as $g)
                            <option value="{{ $g->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $g->nama_gudang }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-gray-500 mt-1 block">Stok barang akan otomatis dipotong dari gudang ini.</span>
                </div>

                <!-- Pelanggan -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Pelanggan (Opsional)
                    </label>
                    <select name="pelanggan_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Umum / Non-Member --</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->no_hp ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Transaksi -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Tanggal Transaksi
                    </label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Catatan Transaksi
                    </label>
                    <textarea name="catatan" rows="2" placeholder="Catatan opsional..."
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <!-- Grand Total & Kalkulator Kembalian -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan:</span>
                        <span class="text-2xl font-black text-blue-700 dark:text-blue-400 font-mono" id="grandTotalText">Rp 0</span>
                    </div>

                    <!-- Input Nominal Pembayaran -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Uang Diterima (Rp):
                        </label>
                        <input type="number" id="uangDiterima" name="nominal_bayar" min="0" placeholder="0" oninput="hitungKembalian()"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-base font-mono font-bold rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Shortcut Tombol Uang Cepat -->
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="setUangPas()" class="px-2 py-1 text-[11px] font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-800 dark:bg-gray-700 dark:text-gray-200">Uang Pas</button>
                        <button type="button" onclick="quickCash(20000)" class="px-2 py-1 text-[11px] font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-800 dark:bg-gray-700 dark:text-gray-200">20k</button>
                        <button type="button" onclick="quickCash(50000)" class="px-2 py-1 text-[11px] font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-800 dark:bg-gray-700 dark:text-gray-200">50k</button>
                        <button type="button" onclick="quickCash(100000)" class="px-2 py-1 text-[11px] font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-800 dark:bg-gray-700 dark:text-gray-200">100k</button>
                        <button type="button" onclick="quickCash(500000)" class="px-2 py-1 text-[11px] font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-800 dark:bg-gray-700 dark:text-gray-200">500k</button>
                    </div>

                    <!-- Hasil Kembalian -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Kembalian:</span>
                        <span id="kembalianText" class="text-base font-bold font-mono text-gray-900 dark:text-white">Rp 0</span>
                        <input type="hidden" name="kembalian" id="kembalianRaw" value="0">
                    </div>

                    <button type="submit" id="btnSubmit"
                            class="w-full text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-bold rounded-lg text-sm px-5 py-3 shadow-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Simpan & Selesaikan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = 1;
        let currentStockMap = @json($stokAwal);
        let currentGrandTotal = 0;

        // Ambil stok real-time saat gudang diganti
        async function fetchRealtimeStock() {
            const gudangId = document.getElementById('gudangSelect').value;
            if (!gudangId) return;

            try {
                const res = await fetch(`{{ route('transaksi.stok.realtime') }}?gudang_id=${gudangId}`);
                if (res.ok) {
                    currentStockMap = await res.json();
                    // Update semua baris yang sudah ada
                    document.querySelectorAll('.item-row').forEach(row => {
                        const selectEl = row.querySelector('.barang-select');
                        updateStockBadgeForRow(row, selectEl.value);
                    });
                }
            } catch (err) {
                console.error('Gagal mengambil stok real-time:', err);
            }
        }

        function updateStockBadgeForRow(row, barangId) {
            const badgeEl = row.querySelector('.stok-info-badge');
            const qtyInput = row.querySelector('.jumlah-input');
            
            if (!barangId || !currentStockMap[barangId]) {
                badgeEl.innerHTML = `<span class="text-gray-400">Pilih produk untuk melihat stok</span>`;
                qtyInput.removeAttribute('max');
                return;
            }

            const item = currentStockMap[barangId];
            const stok = item.stok;

            if (stok <= 0) {
                badgeEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300">Stok Habis (0 ${item.satuan})</span>`;
                qtyInput.max = 0;
            } else if (stok <= 10) {
                badgeEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">Stok Kritis: ${stok} ${item.satuan}</span>`;
                qtyInput.max = stok;
            } else {
                badgeEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">Tersedia: ${stok} ${item.satuan}</span>`;
                qtyInput.max = stok;
            }
        }

        function handleProductChange(selectEl) {
            const row = selectEl.closest('tr');
            const barangId = selectEl.value;

            if (!barangId) {
                row.querySelector('.harga-satuan').value = 'Rp 0';
                row.querySelector('.harga-raw').value = '0';
                row.querySelector('.subtotal-input').value = 'Rp 0';
                row.querySelector('.subtotal-input').dataset.raw = '0';
                updateStockBadgeForRow(row, null);
                hitungGrandTotal();
                return;
            }

            const selectedOption = selectEl.options[selectEl.selectedIndex];
            let harga = parseFloat(selectedOption.dataset.harga || 0);

            if (currentStockMap[barangId] && currentStockMap[barangId].harga_jual) {
                harga = parseFloat(currentStockMap[barangId].harga_jual);
            }

            row.querySelector('.harga-satuan').value = 'Rp ' + harga.toLocaleString('id-ID');
            row.querySelector('.harga-raw').value = harga;
            
            updateStockBadgeForRow(row, barangId);
            hitungSubtotal(row.querySelector('.jumlah-input'));
        }

        function hitungSubtotal(inputEl) {
            const row = inputEl.closest('tr');
            const harga = parseFloat(row.querySelector('.harga-raw').value || 0);
            let qty = parseInt(inputEl.value || 0);
            const max = parseInt(inputEl.max);

            if (!isNaN(max) && qty > max && max >= 0) {
                alert(`Jumlah melebihi stok yang tersedia di gudang ini (Maksimal: ${max})`);
                qty = max > 0 ? max : 1;
                inputEl.value = qty;
            }

            const subtotal = harga * qty;
            row.querySelector('.subtotal-input').value = 'Rp ' + subtotal.toLocaleString('id-ID');
            row.querySelector('.subtotal-input').dataset.raw = subtotal;
            hitungGrandTotal();
        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(el => {
                total += parseFloat(el.dataset.raw || 0);
            });
            currentGrandTotal = total;
            document.getElementById('grandTotalText').innerText = 'Rp ' + total.toLocaleString('id-ID');
            hitungKembalian();
        }

        function hitungKembalian() {
            const uangVal = parseFloat(document.getElementById('uangDiterima').value || 0);
            const kembalianEl = document.getElementById('kembalianText');
            const kembalianRaw = document.getElementById('kembalianRaw');

            if (uangVal === 0 && currentGrandTotal > 0) {
                kembalianEl.innerText = 'Rp 0';
                kembalianEl.className = 'text-base font-bold font-mono text-gray-500';
                kembalianRaw.value = 0;
                return;
            }

            const selisih = uangVal - currentGrandTotal;
            kembalianRaw.value = selisih;

            if (selisih >= 0) {
                kembalianEl.innerText = 'Rp ' + selisih.toLocaleString('id-ID');
                kembalianEl.className = 'text-base font-bold font-mono text-emerald-600 dark:text-emerald-400';
            } else {
                kembalianEl.innerText = 'Kurang Rp ' + Math.abs(selisih).toLocaleString('id-ID');
                kembalianEl.className = 'text-base font-bold font-mono text-red-600 dark:text-red-400';
            }
        }

        function setUangPas() {
            document.getElementById('uangDiterima').value = currentGrandTotal;
            hitungKembalian();
        }

        function quickCash(amount) {
            document.getElementById('uangDiterima').value = amount;
            hitungKembalian();
        }

        function tambahBaris() {
            const container = document.getElementById('itemContainer');
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.barang-select').name = `items[${rowIndex}][barang_id]`;
            newRow.querySelector('.barang-select').value = '';
            newRow.querySelector('.harga-satuan').value = 'Rp 0';
            newRow.querySelector('.harga-raw').name = `items[${rowIndex}][harga]`;
            newRow.querySelector('.harga-raw').value = '0';
            newRow.querySelector('.jumlah-input').name = `items[${rowIndex}][jumlah]`;
            newRow.querySelector('.jumlah-input').value = '1';
            newRow.querySelector('.jumlah-input').removeAttribute('max');
            newRow.querySelector('.subtotal-input').value = 'Rp 0';
            newRow.querySelector('.subtotal-input').dataset.raw = '0';
            newRow.querySelector('.stok-info-badge').innerHTML = `<span class="text-gray-400">Pilih produk untuk melihat sisa stok</span>`;

            container.appendChild(newRow);
            rowIndex++;
            updateItemCount();
        }

        function hapusBaris(btn) {
            const container = document.getElementById('itemContainer');
            if (container.querySelectorAll('.item-row').length > 1) {
                btn.closest('tr').remove();
                hitungGrandTotal();
                updateItemCount();
            } else {
                alert('Minimal harus ada 1 item pada transaksi kasir.');
            }
        }

        function updateItemCount() {
            const count = document.querySelectorAll('.item-row').length;
            document.getElementById('badgeItemCount').innerText = `${count} Item`;
        }

        // Barcode scanner / SKU finder
        document.getElementById('barcodeScanner').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim().toLowerCase();
                if (!code) return;

                let foundId = null;
                const dummySelect = document.querySelector('.barang-select');
                for (let opt of dummySelect.options) {
                    if (opt.value && (opt.dataset.sku?.toLowerCase() === code || opt.dataset.nama?.toLowerCase().includes(code))) {
                        foundId = opt.value;
                        break;
                    }
                }

                if (!foundId) {
                    alert(`Produk dengan SKU/Barcode "${this.value}" tidak ditemukan.`);
                    return;
                }

                // Cari apakah ada baris kosong
                let targetRow = null;
                document.querySelectorAll('.item-row').forEach(row => {
                    const sel = row.querySelector('.barang-select');
                    if (!sel.value && !targetRow) {
                        targetRow = row;
                    }
                });

                if (!targetRow) {
                    tambahBaris();
                    const allRows = document.querySelectorAll('.item-row');
                    targetRow = allRows[allRows.length - 1];
                }

                const sel = targetRow.querySelector('.barang-select');
                sel.value = foundId;
                handleProductChange(sel);
                this.value = '';
            }
        });

        function validateCheckout() {
            if (currentGrandTotal <= 0) {
                alert('Total belanja masih Rp 0. Silakan pilih minimal 1 barang.');
                return false;
            }

            let valid = true;
            document.querySelectorAll('.item-row').forEach(row => {
                const selectEl = row.querySelector('.barang-select');
                const qtyInput = row.querySelector('.jumlah-input');
                const barangId = selectEl.value;

                if (!barangId) {
                    alert('Harap pilih produk pada seluruh baris item belanja.');
                    valid = false;
                    return;
                }

                if (currentStockMap[barangId]) {
                    const stok = currentStockMap[barangId].stok;
                    const qty = parseInt(qtyInput.value || 0);
                    if (stok < qty) {
                        alert(`Stok produk "${currentStockMap[barangId].nama_barang}" tidak cukup (Tersedia: ${stok}, diminta: ${qty})`);
                        valid = false;
                    }
                }
            });

            return valid;
        }

        // Inisialisasi awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            fetchRealtimeStock();
        });
    </script>
@endsection

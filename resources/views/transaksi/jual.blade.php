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
        </div>
    @endif

    <form action="{{ route('transaksi.jual.store') }}" method="POST" id="posForm" onsubmit="return validateCheckout()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Panel Kiri: Input Item Barang -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Tabel Item Keranjang Belanja -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">Daftar Item Belanja</h2>
                            <span id="badgeItemCount" class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-semibold dark:bg-blue-900 dark:text-blue-300">1 Item</span>
                        </div>
                        <button type="button" onclick="tambahBaris()"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3.5 py-2 flex items-center gap-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Baris Barang
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="tabelItems">
                            <thead class="text-[11px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-3 py-2.5">Pilih Produk</th>
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

            <!-- Panel Kanan: Parameter Checkout & Pembayaran Kasir -->
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

                <!-- Pelanggan / Tujuan Kirim -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Pelanggan / Tujuan Kirim
                    </label>
                    <select name="pelanggan_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pelanggan Umum --</option>
                        @foreach ($pelanggan as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} {{ $p->no_hp ? '('.$p->no_hp.')' : '' }} {{ $p->alamat ? ' - ' . Str::limit($p->alamat, 30) : '' }}</option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-gray-500 mt-1 block">Pilih pelanggan untuk pengiriman barang partai besar / grosir.</span>
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

                <!-- Grand Total & Input Pembayaran Manual -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan:</span>
                        <span class="text-2xl font-black text-blue-700 dark:text-blue-400 font-mono" id="grandTotalText">Rp 0</span>
                    </div>

                    <!-- Input Pembayaran Manual -->
                    <div>
                        <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Uang Diterima (Rp): <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="uangDiterima" name="nominal_bayar" min="0" placeholder="Ketik nominal uang..." oninput="hitungKembalian()" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-base font-mono font-bold rounded-lg block w-full p-3 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <span id="uangDiterimaHint" class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 block font-medium">
                            Wajib isi nominal uang diterima terlebih dahulu
                        </span>
                    </div>

                    <!-- Hasil Kembalian Otomatis -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Kembalian:</span>
                        <span id="kembalianText" class="text-base font-bold font-mono text-gray-900 dark:text-white">Rp 0</span>
                        <input type="hidden" name="kembalian" id="kembalianRaw" value="0">
                    </div>

                    <button type="submit" id="btnSubmit" disabled
                            class="w-full text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-bold rounded-lg text-sm px-5 py-3 shadow-lg transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:hover:bg-gray-400 disabled:shadow-none">
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

        function updateSubmitButtonState() {
            const btnSubmit = document.getElementById('btnSubmit');
            const uangInput = document.getElementById('uangDiterima');
            const uangRawVal = uangInput ? uangInput.value.trim() : '';
            const uangVal = parseFloat(uangRawVal || 0);
            const hintEl = document.getElementById('uangDiterimaHint');

            const isFilled = uangRawVal !== '';
            const isSufficient = isFilled && uangVal >= currentGrandTotal && currentGrandTotal > 0;

            if (isSufficient) {
                btnSubmit.disabled = false;
                if (hintEl) {
                    hintEl.innerText = '✓ Pembayaran cukup, transaksi siap diselesaikan.';
                    hintEl.className = 'text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 block font-medium';
                }
            } else {
                btnSubmit.disabled = true;
                if (hintEl) {
                    if (!isFilled) {
                        hintEl.innerText = 'Wajib isi nominal uang';
                        hintEl.className = 'text-[11px] text-amber-600 dark:text-amber-400 mt-1 block font-medium';
                    } else if (currentGrandTotal <= 0) {
                        hintEl.innerText = '* Pilih barang belanjaan terlebih dahulu';
                        hintEl.className = 'text-[11px] text-gray-500 dark:text-gray-400 mt-1 block font-medium';
                    } else {
                        hintEl.innerText = `* Uang diterima kurang Rp ${(currentGrandTotal - uangVal).toLocaleString('id-ID')}`;
                        hintEl.className = 'text-[11px] text-red-600 dark:text-red-400 mt-1 block font-medium';
                    }
                }
            }
        }

        function hitungKembalian() {
            const uangInput = document.getElementById('uangDiterima');
            const uangVal = parseFloat(uangInput.value || 0);
            const kembalianEl = document.getElementById('kembalianText');
            const kembalianRaw = document.getElementById('kembalianRaw');

            if ((uangInput.value.trim() === '' || uangVal === 0) && currentGrandTotal > 0) {
                kembalianEl.innerText = 'Rp 0';
                kembalianEl.className = 'text-base font-bold font-mono text-gray-500';
                kembalianRaw.value = 0;
                updateSubmitButtonState();
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

            updateSubmitButtonState();
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

        function validateCheckout() {
            if (currentGrandTotal <= 0) {
                alert('Total belanja masih Rp 0. Silakan pilih minimal 1 barang.');
                return false;
            }

            const uangInput = document.getElementById('uangDiterima');
            if (!uangInput || !uangInput.value || uangInput.value.trim() === '') {
                alert('Bagian Uang Diterima wajib diisi terlebih dahulu sebelum menyimpan dan menyelesaikan transaksi.');
                if (uangInput) uangInput.focus();
                return false;
            }

            const uangVal = parseFloat(uangInput.value || 0);
            if (uangVal < currentGrandTotal) {
                alert(`Uang diterima (Rp ${uangVal.toLocaleString('id-ID')}) belum mencukupi total tagihan (Rp ${currentGrandTotal.toLocaleString('id-ID')}).`);
                uangInput.focus();
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

        document.addEventListener('DOMContentLoaded', () => {
            fetchRealtimeStock();
            updateSubmitButtonState();
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard Real-Time')
@section('header', 'Dashboard Operasional Real-Time')

@section('content')
<div class="space-y-6">

    <!-- 1. Header Filter Gudang & Status Real-Time -->
    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                    Hari Ini: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Fokus pemantauan: <strong class="text-gray-800 dark:text-gray-200" id="labelFokusGudang">{{ $selectedGudangName }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2" id="gudangFilterForm">
                <label for="gudang_id" class="text-xs font-semibold text-gray-600 dark:text-gray-400 whitespace-nowrap">
                    Gudang:
                </label>
                <select name="gudang_id" id="gudang_id" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-medium">
                    <option value="">Semua Gudang (Konsolidasi)</option>
                    @foreach ($gudangList as $g)
                        <option value="{{ $g->id }}" {{ $selectedGudangId == $g->id ? 'selected' : '' }}>
                            {{ $g->nama_gudang }}
                        </option>
                    @endforeach
                </select>
                @if($selectedGudangId)
                    <a href="{{ route('dashboard') }}" 
                       class="text-xs px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg dark:bg-gray-700 dark:text-gray-300 transition"
                       title="Kembali ke semua gudang">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- 2. Ringkasan Kartu Statistik Aktivitas Hari Ini (Real-Time) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Penjualan Hari Ini -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Penjualan Hari Ini</span>
                <span class="p-1.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2 font-mono" id="statPenjualan">
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                <span id="statPenjualanCount" class="font-semibold text-gray-600 dark:text-gray-300">{{ $penjualanHariIniCount }}</span> transaksi kasir selesai
            </div>
        </div>

        <!-- Total Aktivitas Hari Ini -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Transaksi Hari Ini</span>
                <span class="p-1.5 bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-gray-900 dark:text-white mt-2 font-mono" id="statTotalTrx">
                {{ $transaksiHariIniCount }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                Total aktivitas keluar & masuk
            </div>
        </div>

        <!-- Barang Masuk Hari Ini -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Penerimaan Masuk</span>
                <span class="p-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-2 font-mono" id="statMasukCount">
                {{ $masukHariIniCount }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                Restock barang hari ini
            </div>
        </div>

        <!-- Transfer Antar Gudang Hari Ini -->
        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Transfer Antar Gudang</span>
                <span class="p-1.5 bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-2 font-mono" id="statTransferCount">
                {{ $transferHariIniCount }}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                Perpindahan stok hari ini
            </div>
        </div>
    </div>

    <!-- 3. Tombol Menu Cepat -->
    <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">Aksi Cepat:</div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('transaksi.jual') }}"
                class="px-3.5 py-2 text-xs font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 shadow-sm transition dark:bg-blue-600 dark:hover:bg-blue-700">
                + Kasir Penjualan
            </a>
            <a href="{{ route('transaksi.masuk') }}"
                class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 shadow-sm transition">
                + Catat Barang Masuk
            </a>
            <a href="{{ route('transaksi.transfer') }}"
                class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 shadow-sm transition">
                + Transfer Antar Gudang
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('barang.create') }}"
                class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 shadow-sm transition">
                + Tambah Barang Baru
            </a>
            <a href="{{ route('laporan.index') }}"
                class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 shadow-sm transition">
                Lihat Rekap Laporan
            </a>
            @endif
        </div>
    </div>

    <!-- 4. Tabel Aktivitas Transaksi Hari Ini (Live Stream) -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Aktivitas Transaksi Hari Ini</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Khusus transaksi tanggal {{ date('d/m/Y') }} &bull; Data diperbarui otomatis
                </p>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Total Hari Ini: <span class="font-bold text-gray-900 dark:text-white font-mono" id="labelTotalRows">{{ count($transaksiTerbaru) }}</span> transaksi
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300 uppercase text-[11px] border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Waktu</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">No. Referensi</th>
                        <th scope="col" class="px-4 py-3 whitespace-nowrap">Jenis</th>
                        <th scope="col" class="px-4 py-3">Gudang Terkait</th>
                        <th scope="col" class="px-4 py-3">Pelanggan / Pihak</th>
                        <th scope="col" class="px-4 py-3 text-right whitespace-nowrap">Total Nominal</th>
                        <th scope="col" class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody id="trxTableBody" class="divide-y divide-gray-100 dark:divide-gray-700 font-sans">
                    @forelse($transaksiTerbaru as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" id="row-trx-{{ $trx->id }}">
                        <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                            {{ $trx->created_at ? $trx->created_at->format('H:i:s') : date('H:i:s', strtotime($trx->tanggal)) }} WIB
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $trx->no_referensi }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($trx->jenis === 'jual')
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">Penjualan</span>
                            @elseif($trx->jenis === 'masuk')
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Penerimaan</span>
                            @else
                                <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Transfer</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            @if($trx->jenis === 'jual')
                                Dari: <span class="font-semibold">{{ $trx->gudangAsal->nama_gudang ?? '-' }}</span>
                            @elseif($trx->jenis === 'masuk')
                                Ke: <span class="font-semibold">{{ $trx->gudangTujuan->nama_gudang ?? '-' }}</span>
                            @else
                                {{ $trx->gudangAsal->nama_gudang ?? '-' }} &rarr; {{ $trx->gudangTujuan->nama_gudang ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $trx->pelanggan->nama ?? ($trx->user->name ?? '-') }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            @if($trx->total_bayar > 0)
                                Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($trx->status === 'selesai')
                                <span class="text-emerald-700 font-bold dark:text-emerald-400">Selesai</span>
                            @else
                                <span class="text-red-700 font-bold dark:text-red-400">Batal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center space-y-1">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    Belum ada transaksi pada hari ini ({{ date('d/m/Y') }}) untuk {{ $selectedGudangName }}.
                                </p>
                                <p class="text-xs text-gray-400">
                                    Transaksi kasir atau mutasi stok yang terjadi hari ini akan langsung muncul di sini secara real-time.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Script Real-Time Auto-Polling (Tiap 5 Detik) -->
<script>
    const currentGudangId = "{{ $selectedGudangId }}";
    let isFetching = false;

    async function fetchRealtimeData(manual = false) {
        if (isFetching) return;
        isFetching = true;

        const refreshIcon = document.getElementById('iconRefresh');
        if (manual && refreshIcon) {
            refreshIcon.classList.add('animate-spin');
        }

        try {
            const url = `{{ route('dashboard.realtime') }}?gudang_id=${encodeURIComponent(currentGudangId)}`;
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('Network response not ok');
            const data = await res.json();

            // 1. Update timestamp
            if (data.timestamp) {
                document.getElementById('lastUpdated').textContent = data.timestamp;
            }

            // 2. Update KPI Stats
            if (data.penjualan_hari_ini_formatted !== undefined) {
                document.getElementById('statPenjualan').textContent = data.penjualan_hari_ini_formatted;
            }
            if (data.penjualan_hari_ini_count !== undefined) {
                document.getElementById('statPenjualanCount').textContent = data.penjualan_hari_ini_count;
            }
            if (data.transaksi_hari_ini_count !== undefined) {
                document.getElementById('statTotalTrx').textContent = data.transaksi_hari_ini_count;
            }
            if (data.masuk_hari_ini_count !== undefined) {
                document.getElementById('statMasukCount').textContent = data.masuk_hari_ini_count;
            }
            if (data.transfer_hari_ini_count !== undefined) {
                document.getElementById('statTransferCount').textContent = data.transfer_hari_ini_count;
            }

            // 3. Update Tabel Real-Time
            const tbody = document.getElementById('trxTableBody');
            const totalRowsLabel = document.getElementById('labelTotalRows');

            if (data.transaksi_terbaru) {
                totalRowsLabel.textContent = data.transaksi_terbaru.length;

                if (data.transaksi_terbaru.length === 0) {
                    tbody.innerHTML = `
                        <tr id="emptyRow">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                        Belum ada transaksi pada hari ini ({{ date('d/m/Y') }}) untuk {{ $selectedGudangName }}.
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Transaksi kasir atau mutasi stok yang terjadi hari ini akan langsung muncul di sini secara real-time.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';
                    data.transaksi_terbaru.forEach(trx => {
                        let badgeJenis = '';
                        if (trx.jenis === 'jual') {
                            badgeJenis = '<span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">Penjualan</span>';
                        } else if (trx.jenis === 'masuk') {
                            badgeJenis = '<span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Penerimaan</span>';
                        } else {
                            badgeJenis = '<span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Transfer</span>';
                        }

                        let badgeStatus = trx.status === 'selesai'
                            ? '<span class="text-emerald-700 font-bold dark:text-emerald-400">Selesai</span>'
                            : '<span class="text-red-700 font-bold dark:text-red-400">Batal</span>';

                        html += `
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" id="row-trx-${trx.id}">
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600 dark:text-gray-300">
                                    ${trx.jam} WIB
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    ${trx.no_referensi}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    ${badgeJenis}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    ${trx.gudang_info}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    ${trx.pihak}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    ${trx.total_bayar_formatted}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    ${badgeStatus}
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            }
        } catch (err) {
            console.error('Error saat sinkronisasi realtime dashboard:', err);
        } finally {
            isFetching = false;
            if (manual && refreshIcon) {
                setTimeout(() => refreshIcon.classList.remove('animate-spin'), 400);
            }
        }
    }

    // Polling otomatis setiap 5 detik
    setInterval(() => {
        fetchRealtimeData(false);
    }, 5000);
</script>
@endsection
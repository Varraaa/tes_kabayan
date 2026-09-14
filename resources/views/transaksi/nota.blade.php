<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $transaksi->no_referensi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
                color: black;
                font-size: 12px;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-6 px-4 flex flex-col items-center justify-center font-mono">

    <!-- Action Bar (Kembali & Cetak) -->
    <div class="no-print max-w-sm w-full mb-4 flex justify-between items-center">
        <a href="{{ route('transaksi.index') }}" 
           class="text-xs px-3 py-2 bg-white text-gray-700 font-sans font-medium rounded-lg border border-gray-300 shadow-sm hover:bg-gray-50 flex items-center gap-1">
            &larr; Kembali ke Transaksi
        </a>
        <button onclick="window.print()" 
                class="text-xs px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-sans font-bold rounded-lg shadow flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Struk (Print)
        </button>
    </div>

    <!-- Container Struk Thermal (58mm / 80mm style) -->
    <div class="print-container bg-white p-6 rounded-lg shadow-md border border-gray-300 max-w-sm w-full text-xs text-gray-800">
        <!-- Header Toko -->
        <div class="text-center pb-3 border-b border-dashed border-gray-400">
            <h2 class="text-base font-bold uppercase tracking-wider text-gray-900">PT Sinar Abadi</h2>
            <p class="text-[11px] text-gray-600">Sistem POS & Pergudangan Terpadu</p>
            <p class="text-[10px] text-gray-500 mt-1">{{ $transaksi->gudangAsal->nama_gudang ?? 'Gudang Pusat' }}</p>
            <p class="text-[10px] text-gray-500">{{ $transaksi->gudangAsal->alamat ?? 'Bandung - Jawa Barat' }}</p>
        </div>

        <!-- Meta Data Transaksi -->
        <div class="py-2.5 border-b border-dashed border-gray-400 space-y-1 text-[11px]">
            <div class="flex justify-between">
                <span class="text-gray-500">No. Ref:</span>
                <span class="font-bold text-gray-900">{{ $transaksi->no_referensi }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Waktu:</span>
                <span>{{ date('d/m/Y H:i', strtotime($transaksi->created_at ?? $transaksi->tanggal)) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Kasir:</span>
                <span>{{ $transaksi->user->name ?? 'Kasir Utama' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan:</span>
                <span class="font-semibold">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</span>
            </div>
        </div>

        <!-- Daftar Rincian Barang -->
        <div class="py-3 border-b border-dashed border-gray-400">
            <table class="w-full text-left text-[11px]">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-200">
                        <th class="pb-1">Item Produk</th>
                        <th class="pb-1 text-center">Qty</th>
                        <th class="pb-1 text-right">Harga</th>
                        <th class="pb-1 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksi->details as $item)
                        <tr>
                            <td class="py-1.5 pr-1">
                                <div class="font-semibold text-gray-900 leading-tight">{{ $item->barang->nama_barang ?? 'Barang' }}</div>
                                <div class="text-[9px] text-gray-400">{{ $item->barang->sku ?? '' }}</div>
                            </td>
                            <td class="py-1.5 text-center whitespace-nowrap">{{ $item->jumlah }}</td>
                            <td class="py-1.5 text-right whitespace-nowrap">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-1.5 text-right font-bold text-gray-900 whitespace-nowrap">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Total & Pembayaran -->
        <div class="py-2.5 space-y-1.5 text-[11px]">
            <div class="flex justify-between text-xs font-bold text-gray-900 pt-1">
                <span>TOTAL TAGIHAN:</span>
                <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>

            @if($transaksi->catatan)
                <div class="pt-2 text-[10px] text-gray-500 border-t border-dotted border-gray-300">
                    {{ $transaksi->catatan }}
                </div>
            @endif
        </div>

        <!-- Footer Struk -->
        <div class="text-center pt-4 border-t border-dashed border-gray-400 text-[10px] text-gray-500 space-y-1">
            <p class="font-bold text-gray-700">*** TERIMA KASIH ***</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar kembali kecuali dengan perjanjian sebelumnya.</p>
            <p class="text-[9px] text-gray-400 mt-2 font-sans">{{ date('Y') }} &copy; PT Sinar Abadi POS System</p>
        </div>
    </div>

</body>
</html>

<?php

namespace App\Services;

use App\Models\{Barang, RiwayatStok, Transaksi, TransaksiDetail};
use Illuminate\Support\Facades\{Auth, DB};

class TransaksiService
{
    public static function getStok($gudangId, $barangId): int
    {
        return RiwayatStok::where('gudang_id', $gudangId)
            ->where('barang_id', $barangId)
            ->latest('id')
            ->value('sisa_stok') ?? 0;
    }

    public static function catatMutasi($trxId, $gudangId, $barangId, $tipe, $jumlah, $keterangan): void
    {
        $stokLama = self::getStok($gudangId, $barangId);
        $sisaStok = ($tipe === 'masuk') ? ($stokLama + $jumlah) : ($stokLama - $jumlah);

        RiwayatStok::create([
            'transaksi_id' => $trxId,
            'gudang_id'    => $gudangId,
            'barang_id'    => $barangId,
            'tipe'         => $tipe,
            'jumlah'       => $jumlah,
            'sisa_stok'    => $sisaStok,
            'keterangan'   => $keterangan,
        ]);
    }

    public static function simpanMasuk(array $data)
    {
        return DB::transaction(function () use ($data) {
            $noRef = 'IN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            // 1. Hitung total nominal penerimaan barang
            $total = 0;
            foreach ($data['items'] as $item) {
                $total += (float)$item['jumlah'] * (float)$item['harga'];
            }

            $trx = Transaksi::create([
                'no_referensi'     => $noRef,
                'jenis'            => 'masuk',
                'gudang_tujuan_id' => $data['gudang_id'],
                'tanggal'          => $data['tanggal'],
                'total_bayar'      => $total, // <-- Tersimpan di database
                'status'           => 'selesai',
                'catatan'          => $data['catatan'] ?? null,
                'user_id'          => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $item['harga'],
                    'subtotal'     => (float)$item['jumlah'] * (float)$item['harga'],
                ]);

                self::catatMutasi($trx->id, $data['gudang_id'], $item['barang_id'], 'masuk', $item['jumlah'], "Penerimaan: {$noRef}");
            }

            return $trx;
        });
    }

    public static function simpanTransfer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $noRef = 'TRF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

            $barangIds = collect($data['items'])->pluck('barang_id')->toArray();
            $barangMap = Barang::whereIn('id', $barangIds)->get()->keyBy('id');

            // 2. Hitung total valuasi aset yang ditransfer (berdasarkan harga pokok/modal)
            $total = 0;
            foreach ($data['items'] as $item) {
                $hargaPokok = $barangMap[$item['barang_id']]->harga_pokok ?? 0;
                $total += (float)$item['jumlah'] * (float)$hargaPokok;
            }

            $trx = Transaksi::create([
                'no_referensi'     => $noRef,
                'jenis'            => 'transfer',
                'gudang_asal_id'   => $data['gudang_asal_id'],
                'gudang_tujuan_id' => $data['gudang_tujuan_id'],
                'tanggal'          => $data['tanggal'],
                'total_bayar'      => $total, // <-- Valuasi transfer tersimpan
                'status'           => 'selesai',
                'catatan'          => $data['catatan'] ?? null,
                'user_id'          => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $hargaPokok = $barangMap[$item['barang_id']]->harga_pokok ?? 0;

                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $hargaPokok,
                    'subtotal'     => (float)$item['jumlah'] * (float)$hargaPokok,
                ]);

                // Catat mutasi keluar di gudang asal & masuk di gudang tujuan
                self::catatMutasi($trx->id, $data['gudang_asal_id'], $item['barang_id'], 'keluar', $item['jumlah'], "Transfer Keluar: {$noRef}");
                self::catatMutasi($trx->id, $data['gudang_tujuan_id'], $item['barang_id'], 'masuk', $item['jumlah'], "Transfer Masuk: {$noRef}");
            }

            return $trx;
        });
    }

    public static function batalkanTrx(Transaksi $trx): void
    {
        DB::transaction(function () use ($trx) {
            $trx->update(['status' => 'batal']);
            $noRef = $trx->no_referensi;

            foreach ($trx->details as $d) {
                if ($trx->jenis === 'masuk') {
                    self::catatMutasi($trx->id, $trx->gudang_tujuan_id, $d->barang_id, 'keluar', $d->jumlah, "Batal Masuk: {$noRef}");
                } elseif ($trx->jenis === 'jual') {
                    self::catatMutasi($trx->id, $trx->gudang_asal_id, $d->barang_id, 'masuk', $d->jumlah, "Batal Jual: {$noRef}");
                } elseif ($trx->jenis === 'transfer') {
                    self::catatMutasi($trx->id, $trx->gudang_asal_id, $d->barang_id, 'masuk', $d->jumlah, "Batal Transfer (Balik ke Asal): {$noRef}");
                    self::catatMutasi($trx->id, $trx->gudang_tujuan_id, $d->barang_id, 'keluar', $d->jumlah, "Batal Transfer (Tarik dari Tujuan): {$noRef}");
                }
            }
        });
    }
}

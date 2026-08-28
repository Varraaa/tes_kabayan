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
            $total = collect($data['items'])->sum(fn($i) => $i['jumlah'] * $i['harga']);

            $trx = Transaksi::create([
                'no_referensi'     => $noRef,
                'jenis'            => 'masuk',
                'gudang_tujuan_id' => $data['gudang_id'],
                'tanggal'          => $data['tanggal'],
                'total_bayar'      => $total,
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
                    'subtotal'     => $item['jumlah'] * $item['harga'],
                ]);
                self::catatMutasi($trx->id, $data['gudang_id'], $item['barang_id'], 'masuk', $item['jumlah'], "Penerimaan: {$noRef}");
            }
            return $trx;
        });
    }

    public static function simpanJual(array $data)
    {
        return DB::transaction(function () use ($data) {
            $barangMap = Barang::whereIn('id', collect($data['items'])->pluck('barang_id'))->get()->keyBy('id');
            $noRef = 'POS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $total = collect($data['items'])->sum(fn($i) => $i['jumlah'] * $barangMap[$i['barang_id']]->harga_jual);

            $trx = Transaksi::create([
                'no_referensi'   => $noRef,
                'jenis'          => 'jual',
                'gudang_asal_id' => $data['gudang_id'],
                'pelanggan_id'   => $data['pelanggan_id'] ?? null,
                'tanggal'        => $data['tanggal'],
                'total_bayar'    => $total,
                'status'         => 'selesai',
                'catatan'        => $data['catatan'] ?? null,
                'user_id'        => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $harga = $barangMap[$item['barang_id']]->harga_jual;
                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $harga,
                    'subtotal'     => $item['jumlah'] * $harga,
                ]);
                self::catatMutasi($trx->id, $data['gudang_id'], $item['barang_id'], 'keluar', $item['jumlah'], "Penjualan: {$noRef}");
            }
            return $trx;
        });
    }

    public static function simpanTransfer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $noRef = 'TRF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $trx = Transaksi::create([
                'no_referensi'     => $noRef,
                'jenis'            => 'transfer',
                'gudang_asal_id'   => $data['gudang_asal_id'],
                'gudang_tujuan_id' => $data['gudang_tujuan_id'],
                'tanggal'          => $data['tanggal'],
                'total_bayar'      => 0,
                'status'           => 'selesai',
                'catatan'          => $data['catatan'] ?? null,
                'user_id'          => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => 0,
                    'subtotal'     => 0,
                ]);
                self::catatMutasi($trx->id, $data['gudang_asal_id'], $item['barang_id'], 'keluar', $item['jumlah'], "Transfer keluar ({$noRef})");
                self::catatMutasi($trx->id, $data['gudang_tujuan_id'], $item['barang_id'], 'masuk', $item['jumlah'], "Transfer masuk ({$noRef})");
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
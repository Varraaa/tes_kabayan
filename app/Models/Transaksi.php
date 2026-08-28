<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    
    protected $fillable = [
        'no_referensi',
        'jenis',
        'gudang_asal_id',
        'gudang_tujuan_id',
        'pelanggan_id',
        'tanggal',
        'total bayar',
        'status',
        'catatan',
        'user_id',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_id');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'transaksi_id');

    }
    
}

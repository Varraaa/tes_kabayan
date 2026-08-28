<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'sku',
        'nama_barang',
        'kategori',
        'satuan',
        'harga_pokok',
        'harga_jual',
        'status_aktif',
    ];

    public function transaksiDetail() 
    {
        return $this->hasMany(TransaksiDetail::class, 'barang_id');
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'barang_id');
    }
}

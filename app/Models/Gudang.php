<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $table = 'gudang';
    
    protected $fillable = [
        'nama_gudang',
        'alamat',
        'status_aktif',
    ];

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'gudang_id');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Gudang;
use Illuminate\Database\Seeder;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gudang = [
            [
                'nama_gudang'  => 'Gudang Pusat (Bandung)',
                'alamat'       => 'Jl. Soekarno Hatta No. 123, Bandung',
                'status_aktif' => true,
            ],
            [
                'nama_gudang'  => 'Gudang Cabang Katapang',
                'alamat'       => 'Jl. Raya Katapang No. 45, Kab. Bandung',
                'status_aktif' => true,
            ],
            [
                'nama_gudang'  => 'Gudang Toko Depan',
                'alamat'       => 'Area Display Kasir',
                'status_aktif' => true,
            ],
        ];

        foreach ($gudang as $item) {
            Gudang::create($item);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barang = [
            [
                'sku'          => 'BRG-001',
                'nama_barang'  => 'Buku Tulis Sinar Dunia 38 Lbr (1 Dus)',
                'kategori'     => 'Alat Tulis Kantor',
                'satuan'       => 'Dus',
                'harga_pokok'  => 300000.00,
                'harga_jual'   => 450000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-002',
                'nama_barang'  => 'Pulpen Gel Faster 0.5 Black (1 Box)',
                'kategori'     => 'Alat Tulis Kantor',
                'satuan'       => 'Box',
                'harga_pokok'  => 250000.00,
                'harga_jual'   => 400000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-003',
                'nama_barang'  => 'Kertas HVS PaperOne A4 75gr (1 Box/5 Rim)',
                'kategori'     => 'Kertas & Percetakan',
                'satuan'       => 'Box',
                'harga_pokok'  => 210000.00,
                'harga_jual'   => 260000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-004',
                'nama_barang'  => 'Lakban Coklat Daimaru 2 Inch (1 Dus)',
                'kategori'     => 'Packaging',
                'satuan'       => 'Dus',
                'harga_pokok'  => 510000.00,
                'harga_jual'   => 720000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-005',
                'nama_barang'  => 'Kardus Packing Polos (100 Pcs)',
                'kategori'     => 'Packaging',
                'satuan'       => 'Pack',
                'harga_pokok'  => 350000.00,
                'harga_jual'   => 600000.00,
                'status_aktif' => true,
            ],
        ];

        foreach ($barang as $item) {
            Barang::create($item);
        }
    }
}
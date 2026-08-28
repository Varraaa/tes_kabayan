<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barang = [
            [
                'sku'          => 'BRG-001',
                'nama_barang'  => 'Buku Tulis Sinar Dunia 38 Lbr',
                'kategori'     => 'Alat Tulis Kantor',
                'satuan'       => 'Pcs',
                'harga_pokok'  => 3000.00,
                'harga_jual'   => 4500.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-002',
                'nama_barang'  => 'Pulpen Gel Faster 0.5 Black',
                'kategori'     => 'Alat Tulis Kantor',
                'satuan'       => 'Pcs',
                'harga_pokok'  => 2500.00,
                'harga_jual'   => 4000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-003',
                'nama_barang'  => 'Kertas HVS PaperOne A4 75gr',
                'kategori'     => 'Kertas & Percetakan',
                'satuan'       => 'Rim',
                'harga_pokok'  => 42000.00,
                'harga_jual'   => 52000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-004',
                'nama_barang'  => 'Lakban Coklat Daimaru 2 Inch',
                'kategori'     => 'Packaging',
                'satuan'       => 'Roll',
                'harga_pokok'  => 8500.00,
                'harga_jual'   => 12000.00,
                'status_aktif' => true,
            ],
            [
                'sku'          => 'BRG-005',
                'nama_barang'  => 'Kardus Packing Polos (30x20x15)',
                'kategori'     => 'Packaging',
                'satuan'       => 'Pcs',
                'harga_pokok'  => 3500.00,
                'harga_jual'   => 6000.00,
                'status_aktif' => true,
            ],
        ];

        foreach ($barang as $item) {
            Barang::create($item);
        }
    }
}

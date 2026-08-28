# Sistem Manajemen Stok

Aplikasi web untuk mengelola persediaan barang pada beberapa gudang. Aplikasi ini mendukung pencatatan barang masuk, penjualan, transfer stok antar-gudang, pembatalan transaksi, serta laporan transaksi dalam format CSV.

## Fitur

- Dashboard ringkasan persediaan dan aktivitas transaksi.
- Manajemen master gudang, barang, dan pelanggan.
- Transaksi barang masuk.
- Transaksi penjualan kepada pelanggan.
- Transfer barang antar-gudang.
- Pembatalan transaksi.
- Laporan transaksi dan ekspor CSV.
- Autentikasi dengan pembatasan akses berdasarkan role.

## Role Pengguna

| Role | Akses |
| --- | --- |
| Admin | Dashboard, transaksi, master gudang, barang, pelanggan, dan laporan |
| Operator | Dashboard dan transaksi |

## Teknologi

- PHP `^8.3`
- Laravel `^13.17`
- SQLite sebagai database default
- Vite `^8.0`
- Tailwind CSS `^4.0`

## Persyaratan

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm

## Instalasi

1. Clone repository lalu masuk ke folder proyek.

2. Install dependency PHP dan JavaScript:

	```bash
	composer install
	npm install
	```

3. Buat file environment dan application key:

	```bash
	copy .env.example .env
	php artisan key:generate
	```

	Pada macOS/Linux, gunakan `cp .env.example .env` sebagai pengganti perintah `copy`.

4. Konfigurasi database pada `.env`. Konfigurasi bawaan menggunakan SQLite:

	```env
	DB_CONNECTION=sqlite
	```

	Buat file database jika belum tersedia:

	```bash
	type nul > database\database.sqlite
	```

	Pada macOS/Linux, gunakan `touch database/database.sqlite`.

5. Jalankan migration dan seeder:

	```bash
	php artisan migrate --seed
	```

6. Build asset frontend:

	```bash
	npm run build
	```

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Buka [http://localhost:8000](http://localhost:8000) di browser.

Untuk pengembangan frontend dengan Vite, jalankan pada terminal terpisah:

```bash
npm run dev
```

Alternatifnya, perintah berikut menjalankan alur development yang disediakan proyek:

```bash
composer run dev
```

## Akun Demo

Seeder menyediakan akun berikut:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@sinar.com` | `password` |
| Operator | `operator@sinar.com` | `password` |

Ganti password akun demo sebelum digunakan pada lingkungan produksi.

## Struktur Data Utama

- `users`: pengguna dan role akses.
- `gudang`: daftar lokasi penyimpanan.
- `barang`: SKU, kategori, satuan, serta harga pokok dan harga jual.
- `pelanggan`: data pelanggan penjualan.
- `transaksi`: header transaksi masuk, jual, atau transfer.
- `transaksi_detail`: rincian barang pada setiap transaksi.
- `riwayat_stok`: histori perubahan stok per gudang dan barang.

## Pengujian

Jalankan test suite dengan:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer run test
```

## Perintah Berguna

```bash
php artisan route:list
php artisan migrate:fresh --seed
php artisan config:clear
```

`migrate:fresh --seed` akan menghapus seluruh tabel dan data yang ada, lalu membuatnya kembali. Gunakan hanya pada lingkungan pengembangan atau pengujian.

## Lisensi

Proyek ini menggunakan lisensi MIT.

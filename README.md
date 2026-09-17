# PT Sinar Nusantara - Sistem Inventaris Multi-Gudang & Kasir (Point of Sale)

Aplikasi web komprehensif berbasis **Laravel** dan **Tailwind CSS / Flowbite** yang dirancang untuk mengelola rantai pasokan dan persediaan barang di banyak lokasi gudang (*multi-warehouse inventory*), kasir penjualan multi-produk, mutasi transfer stok antar-gudang, pembatalan transaksi dengan mekanisme rollback stok otomatis, hingga laporan analitik terpadu yang dapat diekspor ke CSV.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Hak Akses Pengguna (Multi-Role)](#hak-akses-pengguna-multi-role)
- [Teknologi & Dependensi](#teknologi--dependensi)
- [Kredensial Akun Bawaan (Demo)](#kredensial-akun-bawaan-demo)
- [Struktur Database & Model](#struktur-database--model)
- [Daftar Rute & Endpoint](#daftar-rute--endpoint)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Panduan Instalasi & Setup](#panduan-instalasi--setup)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Direktori Proyek](#struktur-direktori-proyek)
- [Perintah Berguna (Artisan & Composer)](#perintah-berguna-artisan--composer)

---

## Fitur Utama

### 1. Dashboard Eksekutif & Analitik Realtime
- **Kartu Metrik KPI Utama**:
  - Total jenis barang terdaftar.
  - Total gudang aktif.
  - Total pelanggan terdata.
  - Total omset penjualan hari ini.
- **Peringatan Stok Rendah (Low Stock Alerts)**: Menampilkan produk dengan sisa stok terendah atau kritis untuk tindakan restok cepat.
- **Polling Realtime**: Endpoint API `/dashboard/realtime` untuk pembaruan data statistik secara otomatis tanpa *refresh* halaman.

### 2. Modul Transaksi Persediaan & Kasir Terpadu
- **Barang Masuk (Inbound Stock)**: Pencatatan pasokan/pembelian barang masuk ke gudang tujuan dengan pembaruan stok otomatis.
- **Penjualan (Kasir / Outbound)**: 
  - Dukungan pembelian multi-produk dalam satu transaksi.
  - Pengecekan ketersediaan stok fisik per gudang secara realtime.
  - Perhitungan subtotal dan total bayar otomatis.
  - Terhubung langsung dengan master pelanggan.
- **Transfer Stok Antar-Gudang (Inter-Warehouse Transfer)**: Pemindahan fisik barang dari gudang sumber ke gudang tujuan yang dieksekusi secara atomik menggunakan *Database Transaction* untuk menjaga integritas data.
- **Pembatalan Transaksi**: Fasilitas pembatalan transaksi dengan pengembalian (*rollback*) jumlah stok fisik ke posisi semula.

### 3. Manajemen Master Data (CRUD Lengkap)
- **Master Gudang**: Pengelolaan kode gudang, nama gudang, alamat/lokasi, dan status operasional.
- **Master Barang**: Pengelolaan SKU, nama produk, kategori, satuan (Pcs, Box, Pack, dll), harga pokok (beli), dan harga jual.
- **Master Pelanggan**: Pengelolaan data kontak pelanggan (nama, nomor HP/telepon, dan alamat pengiriman).

### 4. Pusat Laporan Terpadu (Integrated Reports)
Halaman laporan interaktif dengan tiga tab terpusat:
- **Laporan Stok**: Menampilkan kuantitas stok barang per lokasi gudang lengkap dengan status persediaan (Aman / Rendah / Habis).
- **Laporan Barang Masuk**: Riwayat pencatatan barang masuk berdasarkan rentang tanggal dan gudang.
- **Laporan Penjualan**:
  - Rekap transaksi penjualan lengkap dengan detail produk yang dibeli.
  - Setiap produk dalam satu pesanan tercatat rapi dalam satu seksi transaksi tanpa garis pemisah yang membingungkan.
  - Dilengkapi pencarian nama/kontak pelanggan, filter gudang, dan filter rentang tanggal.
  - Modal detail transaksi interaktif.
- **Ekspor Data ke CSV**: Mengunduh data laporan langsung ke file `.csv` sesuai dengan tab dan kriteria filter yang sedang diterapkan.

---

## Hak Akses Pengguna (Multi-Role)

Aplikasi mengimplementasikan pembatasan akses berbasis peran (*Role-Based Access Control*):

| Role | Dashboard | Transaksi (Masuk, Jual, Transfer, Batal) | Master Data (Barang, Gudang, Pelanggan) | Laporan & Ekspor CSV |
| :--- | :---: | :---: | :---: | :---: |
| **Admin** | Ya | Ya | Ya | Ya |
| **Operator** | Ya | Ya | Tidak | Tidak |

---

## Teknologi & Dependensi

- **Backend**: [PHP](https://www.php.net/) ^8.3, [Laravel Framework](https://laravel.com/) ^13.17 (Eloquent ORM, Service Layer Pattern, Database Transactions)
- **Database**: SQLite (bawaan pengembangan) / MySQL / PostgreSQL
- **Frontend UI**: Blade Templating Engine, [Tailwind CSS](https://tailwindcss.com/) ^4.0, [Flowbite](https://flowbite.com/) UI Components, Vanilla JavaScript
- **Build Tooling & Asset Bundler**: [Vite](https://vitejs.dev/) ^8.0
- **Dependency Manager**: [Composer](https://getcomposer.org/) & [Node.js / npm](https://nodejs.org/)

---

## Kredensial Akun Bawaan (Demo)

Database seeder menyediakan dua akun siap pakai:

| Peran (Role) | Alamat Email | Kata Sandi (Password) |
| :--- | :--- | :--- |
| **Administrator** | `admin@sinar.com` | `password` |
| **Operator Gudang** | `operator@sinar.com` | `password` |

> [!IMPORTANT]
> Segera ganti kata sandi atau buat user baru jika aplikasi digunakan di lingkungan *production*.

---

## Struktur Database & Model

1. **`users`**: Menyimpan akun pengguna, kata sandi terenkripsi (Bcrypt/Hash), dan role (`admin`, `operator`).
2. **`gudang`**: Lokasi fisik penyimpanan (`id`, `nama`, `kode`, `lokasi`, `status`).
3. **`pelanggan`**: Data pelanggan pembeli (`id`, `nama`, `no_hp`, `alamat`).
4. **`barang`**: Master katalog produk (`id`, `sku`, `nama`, `kategori`, `satuan`, `harga_pokok`, `harga_jual`).
5. **`transaksi`**: Header transaksi (`id`, `nomor_transaksi`, `tipe` [masuk/jual/transfer], `gudang_id`, `gudang_tujuan_id`, `pelanggan_id`, `user_id`, `total_bayar`, `status` [selesai/dibatalkan], `tanggal`).
6. **`transaksi_detail`**: Detail item produk per transaksi (`id`, `transaksi_id`, `barang_id`, `jumlah`, `harga_satuan`, `subtotal`).
7. **`riwayat_stok`**: Kartu mutasi stok fisik (`id`, `gudang_id`, `barang_id`, `jenis` [masuk/keluar], `jumlah`, `stok_akhir`, `referensi_tipe`, `referensi_id`, `keterangan`, `tanggal`).

---

## Daftar Rute & Endpoint

### Publik / Tamu (Guest)
- `GET /`: Halaman formulir login (`login`)
- `POST /login`: Proses autentikasi masuk (`login.post`)
- `POST /logout`: Proses keluar akun (`logout`)

### Terotentikasi (Admin & Operator)
- `GET /dashboard`: Halaman utama Dashboard statistik & KPI
- `GET /dashboard/realtime`: API data realtime statistik (JSON)
- `GET /transaksi`: Daftar seluruh riwayat transaksi
- `GET /transaksi/stok-realtime`: Pengecekan stok gudang saat transaksi
- `GET /transaksi/masuk` & `POST /transaksi/masuk`: Formulir dan simpan barang masuk
- `GET /transaksi/jual` & `POST /transaksi/jual`: Formulir kasir dan proses penjualan
- `GET /transaksi/transfer` & `POST /transaksi/transfer`: Formulir dan eksekusi transfer antar-gudang
- `POST /transaksi/{transaksi}/batal`: Batalkan transaksi dan pulihkan stok

### Khusus Admin (Role: Admin)
- `RESOURCE /barang`: CRUD Master Data Barang
- `RESOURCE /gudang`: CRUD Master Data Gudang
- `RESOURCE /pelanggan`: CRUD Master Data Pelanggan
- `GET /laporan`: Pusat Laporan Terpadu (`?tab=stok`, `?tab=masuk`, `?tab=penjualan`)
- `GET /laporan/export`: Ekspor data laporan aktif ke format CSV

---

## Persyaratan Sistem

Pastikan lingkungan server atau komputer lokal telah terinstal:
- **PHP** versi `8.3` atau lebih baru (dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`).
- **Composer** versi `2.x`.
- **Node.js** versi `18.x` atau lebih baru beserta **npm**.

---

## Panduan Instalasi & Setup

### 1. Masuk ke Direktori Proyek
```bash
cd tes_kabayan
```

### 2. Install Dependency PHP dan Node.js
```bash
composer install
npm install
```

### 3. Konfigurasi File Environment
Salin file `.env.example` menjadi `.env`:
- **Windows (Command Prompt / PowerShell)**:
  ```bash
  copy .env.example .env
  ```
- **Linux / macOS**:
  ```bash
  cp .env.example .env
  ```

### 4. Buat Application Encryption Key
```bash
php artisan key:generate
```

### 5. Inisialisasi Database
Secara default proyek dikonfigurasi menggunakan **SQLite**. Jika file database belum ada, buat file kosong di folder `database`:
- **Windows**:
  ```bash
  type nul > database\database.sqlite
  ```
- **Linux / macOS**:
  ```bash
  touch database/database.sqlite
  ```

*(Opsional: Jika ingin menggunakan MySQL/PostgreSQL, cukup sesuaikan nilai `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada file `.env`)*.

### 6. Jalankan Migrasi dan Seeder Data Awal
```bash
php artisan migrate --seed
```

---

## Menjalankan Aplikasi

Jalankan dua perintah berikut di dua jendela terminal terpisah:

### Terminal 1 - Server PHP Laravel
```bash
php artisan serve --port=8004
```
*(Atau `php artisan serve` untuk port default `8000`)*

### Terminal 2 - Asset Bundler (Vite)
```bash
npm run dev
```

Buka browser dan akses aplikasi melalui alamat:
👉 **[http://localhost:8004](http://localhost:8004)** *(atau port yang sedang Anda jalankan)*.

---

## Struktur Direktori Proyek

```
tes_kabayan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── BarangController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── GudangController.php
│   │   │   ├── LaporanController.php
│   │   │   ├── PelangganController.php
│   │   │   └── TransaksiController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Barang.php
│   │   ├── Gudang.php
│   │   ├── Pelanggan.php
│   │   ├── RiwayatStok.php
│   │   ├── Transaksi.php
│   │   ├── TransaksiDetail.php
│   │   └── User.php
│   └── Services/
│       └── TransaksiService.php          # Logika bisnis transaksi & stok
├── database/
│   ├── migrations/                       # Skema tabel database
│   └── seeders/                          # Data inisial (user, gudang, barang)
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── auth/                         # View login
│       ├── barang/                       # View master barang
│       ├── dashboard.blade.php           # View dashboard analitik
│       ├── gudang/                       # View master gudang
│       ├── laporan/
│       │   └── index.blade.php           # Pusat laporan terpadu (tab stok, masuk, jual)
│       ├── layouts/                      # Template master & navigasi
│       ├── pelanggan/                    # View master pelanggan
│       └── transaksi/                    # View transaksi (masuk, jual, transfer, index)
├── routes/
│   ├── console.php
│   └── web.php                           # Definisi rute aplikasi
├── storage/
└── tests/
```

---

## Perintah Berguna (Artisan & Composer)

```bash
# Membersihkan seluruh cache konfigurasi dan view Blade
php artisan optimize:clear

# Menampilkan seluruh daftar rute aplikasi
php artisan route:list

# Reset ulang database dan isi ulang data seeder
php artisan migrate:fresh --seed

# Menjalankan pengujian otomatis (Testing)
php artisan test
```

---


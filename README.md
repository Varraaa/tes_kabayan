# PT Sinar Nusantara - Sistem Manajemen Inventaris Multi-Gudang & Kasir Point of Sale (POS)

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg?style=flat-square&logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

Aplikasi web korporasi (*Enterprise Web Application*) berbasis **PHP 8.3**, **Laravel**, dan **Tailwind CSS / Flowbite** yang dirancang untuk mengelola rantai pasokan logistik di banyak lokasi gudang cabang (*multi-warehouse inventory*), melayani transaksi penjualan ritel kasir (*Point of Sale*) dengan validasi pembayaran ketat, mutasi transfer stok antar-gudang berbasis transaksi atomik (ACID), mekanisme pembatalan transaksi dengan *stock rollback* otomatis, pemantauan dashboard real-time melalui *background polling*, hingga pusat laporan terpadu dengan *streaming export* ke CSV.

---

## Daftar Isi

1. [Profil & Gambaran Umum Sistem](#profil--gambaran-umum-sistem)
2. [Permasalahan Nyata & Solusi yang Diimplementasikan](#permasalahan-nyata--solusi-yang-diimplementasikan)
3. [Arsitektur Perangkat Lunak & Pola Desain](#arsitektur-perangkat-lunak--pola-desain)
4. [Hak Akses Pengguna (Role-Based Access Control / RBAC)](#hak-akses-pengguna-role-based-access-control--rbac)
5. [Bedah Fitur & Teknologi per Halaman (Page-by-Page)](#bedah-fitur--teknologi-per-halaman-page-by-page)
6. [Teknologi & Dependensi (Tech Stack)](#teknologi--dependensi-tech-stack)
7. [Daftar Rute & Endpoint API](#daftar-rute--endpoint-api)
8. [Struktur Basis Data & Model Relasional](#struktur-basis-data--model-relasional)
9. [Kredensial Akun Bawaan (Demo)](#kredensial-akun-bawaan-demo)
10. [Panduan Instalasi & Menjalankan Aplikasi](#panduan-instalasi--menjalankan-aplikasi)
11. [Struktur Direktori Proyek](#struktur-direktori-proyek)
12. [Perintah Artisan & Pengujian](#perintah-artisan--pengujian)

---

## Profil & Gambaran Umum Sistem

* **Nama Perusahaan:** PT Sinar Nusantara
* **Kategori Aplikasi:** *Enterprise Web Application* / Sistem Informasi Manajemen Rantai Pasokan & Kasir Ritel.
* **Tujuan Utama:** Menghubungkan seluruh pergerakan barang secara terpusat dari banyak lokasi gudang fisik (Pusat, Cabang Utara, Cabang Selatan, dll.), mencegah terjadinya selisih persediaan fisik dengan catatan buku pembukuan, serta meminimalisir potensi *human error* pada operasional kasir harian.

---

## Permasalahan Nyata & Solusi yang Diimplementasikan

Dalam operasional bisnis distribusi dan ritel konvensional, terdapat **3 kendala nyata** yang diselesaikan oleh sistem ini:

| No | Masalah dalam Dunia Nyata | Dampak Bisnis | Solusi yang Diimplementasikan Sistem |
| :---: | :--- | :--- | :--- |
| **1** | **Selisih Stok Fisik vs Buku** *(Data Asimetris)* | Perusahaan tidak mengetahui sisa kuota riil barang, barang hilang di jalan saat dipindahkan antar-cabang, atau pengadaan barang ganda yang memboroskan modal kerja. | **Multi-Warehouse Isolation & Atomic Transfer:** Sistem mendata stok per lokasi gudang terpisah. Perpindahan barang antar-gudang dibungkus `DB::transaction`—memotong stok asal dan menambah stok tujuan secara bersamaan (mustahil ada barang hilang tanpa tercatat). |
| **2** | **Human Error Kasir** *(Stok Minus & Salah Hitung)* | Kasir salah menghitung kembalian secara manual, salah ambil gudang, atau menjual barang melebihi stok yang ada sehingga saldo barang bernilai negatif (*negative inventory*). | **Strict POS Gatekeeper & AJAX Stock Checker:** Pengecekan sisa stok fisik riil di gudang secara langsung saat barang dipilih (mengunci batas maksimal input) dan mengunci tombol checkout dalam kondisi *disabled* sampai uang diterima kasir diisi dan mencukupi nilai belanja. |
| **3** | **Laporan Lambat & Rawan Crash** | Manajemen terlambat menerima omset harian karena rekapitulasi manual di akhir bulan, serta server rawan mengalami error *Memory Exhausted* saat mengunduh data besar. | **Live Real-Time Dashboard & Streamed CSV Export:** Dashboard memantau omset dan transaksi secara otomatis setiap 5 detik (*live polling*), serta ekspor laporan ribuan baris menggunakan *Symfony StreamedResponse* dengan injeksi UTF-8 BOM untuk Microsoft Excel. |

---

## Arsitektur Perangkat Lunak & Pola Desain

Aplikasi dibangun dengan mematuhi standar rekayasa perangkat lunak modern:

1. **Model-View-Controller (MVC) + Service Layer Pattern**:
   - **Controller:** Murni menangani permintaan HTTP (*request handling*) dan mengembalikan tampilan (*Blade*) atau respons data (*JSON*).
   - **Service Layer (`TransaksiService`):** Mengisolasi seluruh aturan logika bisnis (*Business Logic*), mencakup kalkulasi mutasi stok, pembuatan nomor referensi otomatis, dan pembatalan transaksi berjejak audit.
2. **Prinsip ACID Database Transactions (`DB::transaction`)**:
   - Menjamin bahwa operasi manipulasi data majemuk dieksekusi secara atomik (*Atomicity*): seluruh query berhasil 100%, atau jika ada satu kegagalan teknis, seluruh mutasi dibatalkan otomatis (*rollback*).
3. **Audit Trail (Kartu Mutasi Stok Otomatis)**:
   - Tidak ada penghapusan data fisik (*no hard delete*) pada transaksi yang dibatalkan demi kepatuhan audit akuntansi. Sistem menggunakan algoritma mutasi pembalik (*reversal mutation*).
4. **Memory-Efficient Data Streaming**:
   - Menghindari pemuatan seluruh objek data ke RAM server saat ekspor file, melainkan mengalirkannya baris per baris (*chunk streaming*) langsung ke peramban pengguna.

---

## Hak Akses Pengguna (Role-Based Access Control / RBAC)

Sistem membedakan hak akses secara ketat menggunakan middleware kustom `role:admin,operator`:

| Menu / Fitur | Administrator (`admin`) | Operator Gudang (`operator`) | Catatan Otorisasi |
| :--- | :---: | :---: | :--- |
| **Login & Logout** | Ya | Ya | Terproteksi sesi & CSRF |
| **Dashboard Analitik & Live Feed** | Ya | Ya | Monitoring transaksi hari ini |
| **Kasir / Penjualan (POS)** | Ya | Ya | Pencatatan transaksi jual eceran & wholesale |
| **Penerimaan Barang Masuk** | Ya | Ya | Restock pasokan barang dari supplier |
| **Transfer Antar-Gudang** | Ya | Ya | Pemindahan stok antar-cabang fisik |
| **Riwayat Transaksi & Pembatalan** | Ya | Ya | Detail faktur & rollback stok |
| **Master Data Barang** | Ya | Tidak | Akses CRUD dilindungi middleware `role:admin` |
| **Master Data Gudang** | Ya | Tidak | Akses CRUD dilindungi middleware `role:admin` |
| **Master Data Pelanggan** | Ya | Tidak | Akses CRUD dilindungi middleware `role:admin` |
| **Pusat Laporan & Export CSV** | Ya | Tidak | Akses laporan analitik & pembukuan finansial |

---

## Bedah Fitur & Teknologi per Halaman (Page-by-Page)

Setiap halaman di dalam aplikasi ini dirancang dengan teknologi utama (*core*) yang memiliki fungsi spesifik:

### 1. Page Login & Autentikasi (`/login`)
- **Teknologi Utama:** **Middleware `guest` & Bcrypt Password Hashing**
- **Fungsi:** 
  - Middleware `guest` memvalidasi status sesi pengguna; pengguna yang sudah dalam kondisi login otomatis dialihkan ke dashboard tanpa bisa membuka form login kembali.
  - Form dilindungi token rahasia **CSRF Protection (`@csrf`)** untuk menangkal serangan siber *Cross-Site Request Forgery*.
  - Kata sandi dienkripsi menggunakan algoritma satu arah **Bcrypt Hashing** yang bergaram (*salted*), memastikan kata sandi tersimpan aman di database.

### 2. Page Dashboard Operasional (`/dashboard`)
- **Teknologi Utama:** **Real-Time Data Polling (JavaScript Async Fetch API)**
- **Fungsi:**
  - Script JavaScript di [`resources/views/dashboard.blade.php`](file:///c:/SEKOLAH/12semester1/tes_kabayan/tes_kabayan/resources/views/dashboard.blade.php) mengeksekusi `setInterval(..., 5000)` untuk menembak endpoint `/dashboard/realtime` setiap 5 detik di latar belakang.
  - Mengupdate angka omset harian dan menyuntikkan baris transaksi terbaru langsung ke tabel HTML (`<tbody>`) secara otomatis tanpa pengguna perlu me-refresh halaman browser.
  - Dilengkapi **Filter Gudang Dinamis** (opsi konsolidasi semua gudang atau isolasi gudang tertentu) dan **Conditional Status Classifier** yang menandai status stok fisik barang (*Habis (0)*, *Kritis (1-10)*, *Menipis*, atau *Aman*).

### 3. Page Master Data Barang (`/barang`)
- **Teknologi Utama:** **Interactive Input Masking (Format Rupiah) & Backend Sanitization**
- **Fungsi:**
  - Di frontend, event listener JavaScript `format-rupiah` secara interaktif mengubah nominal angka yang diketikkan pengguna (misal `60000` menjadi **`60.000`**) lengkap dengan pemisah ribuan titik dan ikon `Rp` agar operator terhindar dari salah membaca nominal.
  - Di backend [`BarangController`](file:///c:/SEKOLAH/12semester1/tes_kabayan/tes_kabayan/app/Http/Controllers/BarangController.php), fungsi pembersih `preg_replace('/[^0-9]/', '', ...)` menghapus titik format rupiah sebelum validasi dijalankan, sehingga database menyimpan nilai numerik murni (`60000`).
  - Dilindungi aturan **`unique:barang,sku`** untuk mencegah duplikasi kode produk serta **Relational Integrity Guard** yang melarang penghapusan data barang jika sudah pernah memiliki histori transaksi logistik.

### 4. Page Master Gudang & Master Pelanggan (`/gudang`, `/pelanggan`)
- **Teknologi Utama:** **RESTful Resource Controller (`Route::resource`) & Eloquent Relational Mapping**
- **Fungsi:**
  - Mengelola 7 rute CRUD standar (*Index, Create, Store, Show, Edit, Update, Destroy*) secara terstruktur dan modular.
  - Relasi Eloquent `hasMany` menghubungkan entitas lokasi fisik gudang dengan seluruh rekaman mutasi stok dan transaksi yang berlangsung di gudang tersebut.

### 5. Page Kasir / Point of Sale (POS) (`/transaksi/jual`)
- **Teknologi Utama:** **Real-Time AJAX Stock Checker & Strict Payment Gatekeeper**
- **Fungsi:**
  - **Dynamic DOM Cloning (`cloneNode`):** Kasir dapat menambah dan menghapus baris barang belanjaan sebanyak apa pun dalam satu formulir kasir secara dinamis.
  - **Real-Time AJAX Stock Checker:** Saat barang dipilih, sistem memanggil API `/transaksi/stok-realtime` untuk mengambil kuota fisik riil di gudang terkait dan langsung memasang atribut `max` pada input kuantitas, menjamin kasir tidak bisa menjual melebihi sisa fisik (*zero negative inventory*).
  - **Strict Payment Gatekeeper:** Tombol *'Simpan & Selesaikan Transaksi'* dalam keadaan terkunci (*disabled*) secara default. Tombol tidak dapat diklik sebelum kasir mengisi kolom uang yang diterima. Jika uangnya kurang dari total belanja, muncul teks merah peringatan (*"Uang diterima kurang Rp..."*).
  - **Automatic Change Calculator:** Begitu uang tunai yang diinput pas atau berlebih, sistem menghitung kembalian secara otomatis dan membuka kunci tombol simpan menjadi aktif berwarna hijau.
  - **Default Customer Fallback:** Menyetel opsi *'-- Pelanggan Umum --'* secara otomatis untuk konsumen ritel eceran tanpa kartu member.

### 6. Page Transaksi Masuk & Transfer Antar-Gudang (`/transaksi/masuk`, `/transaksi/transfer`)
- **Teknologi Utama:** **Database Transaction (`DB::transaction`)**
- **Fungsi:**
  - Mengamankan proses perpindahan barang agar datanya selalu sinkron dan presisi.
  - Pada transaksi **Barang Masuk**: Mencatat kuantitas barang pasokan dan mengkalkulasi valuasi aset inventaris.
  - Pada transaksi **Transfer Antar-Gudang**: Menjalankan mutasi debet (potong stok) di gudang asal dan kredit (tambah stok) di gudang tujuan secara bersamaan dalam satu siklus atomik. Menjamin mustahil ada barang yang berkurang di gudang asal tapi gagal masuk ke gudang tujuan.
  - Dilengkapi aturan validasi `different:gudang_tujuan_id` agar gudang asal dan gudang tujuan transfer tidak boleh sama.

### 7. Page Riwayat Transaksi & Pembatalan (`/transaksi`)
- **Teknologi Utama:** **Stock Rollback / Reversal Mutation Algorithm**
- **Fungsi:**
  - Mengelola riwayat seluruh transaksi dengan **Server-Side Pagination (`paginate(15)`)** agar query tetap kencang.
  - Dilengkapi **Flowbite Modal Pop-Up** untuk menampilkan faktur rincian transaksi kasir langsung di layar tanpa reload halaman.
  - **Stock Rollback Algorithm:** Jika transaksi dibatalkan oleh petugas, sistem tidak menghapus data (*no hard delete*), melainkan mengubah statusnya menjadi `batal` dan secara otomatis membuat mutasi pembalik di tabel `riwayat_stok` untuk mengembalikan fisik barang ke gudang asal, menjaga transparansi jejak audit (*audit trail*).

### 8. Page Pusat Laporan Terpadu & Export CSV (`/laporan`)
- **Teknologi Utama:** **Dynamic Query Filtering & Laravel `StreamedResponse` (UTF-8 BOM)**
- **Fungsi:**
  - **Multi-Tab Dynamic Routing:** Menyatukan 3 jenis laporan (Stok Gudang, Barang Masuk, dan Penjualan) dalam satu halaman navigasi dengan query string `?tab=...`.
  - **Multi-Column Order Sorting:** Query diurutkan berdasarkan `orderBy('tanggal', 'desc')->orderBy('id', 'desc')` agar transaksi penjualan terbaru selalu tampil pada baris paling atas.
  - **Dynamic Query Filtering:** Menggunakan klausa dinamis Eloquent (`where`, `whereBetween`, `whereHas`) untuk menyaring data berdasarkan gudang, rentang tanggal, jenis produk, dan pencarian nama barang.
  - **Export CSV via `StreamedResponse`:** Komponen bawaan fondasi HTTP Laravel (`Symfony\Component\HttpFoundation\StreamedResponse`) mengalirkan baris data laporan langsung ke peramban secara bertahap (*streaming*). Sangat hemat RAM server (mencegah error *Memory Exhausted*) dan dilengkapi injeksi karakter khusus **UTF-8 BOM (`\xEF\xBB\xBF`)** agar file CSV langsung terbuka rapi berkolom sempurna di Microsoft Excel.

---

## Teknologi & Dependensi (Tech Stack)

### Sisi Server (Backend):
- **Bahasa Pemrograman:** [PHP](https://www.php.net/) ^8.3
- **Web Framework:** [Laravel Framework](https://laravel.com/) ^13.17 (Arsitektur MVC, Service Layer Pattern, Eloquent ORM, Database Transactions)
- **Komponen HTTP Streaming:** `Symfony\Component\HttpFoundation\StreamedResponse` (Bawaan Laravel HTTP Foundation)
- **Database:** SQLite (lingkungan pengembangan lokal) / MySQL / PostgreSQL (lingkungan produksi)

### Sisi Klien (Frontend):
- **Templating Engine:** Laravel Blade
- **CSS Framework:** [Tailwind CSS](https://tailwindcss.com/) ^4.0
- **UI Components:** [Flowbite](https://flowbite.com/)
- **Scripting:** Vanilla JavaScript (ES6+), Async Fetch API, DOM Manipulation
- **Asset Bundler:** [Vite](https://vitejs.dev/) ^8.0

---

## Daftar Rute & Endpoint API

| Metode HTTP | Alamat URL (URI) | Nama Rute (Route Name) | Controller & Method | Hak Akses |
| :---: | :--- | :--- | :--- | :---: |
| `GET` | `/` atau `/login` | `login` | `Auth\LoginController@showLoginForm` | Publik (`guest`) |
| `POST` | `/login` | `login.submit` | `Auth\LoginController@login` | Publik (`guest`) |
| `POST` | `/logout` | `logout` | `Auth\LoginController@logout` | Terautentikasi |
| `GET` | `/dashboard` | `dashboard` | `DashboardController@index` | Admin & Operator |
| `GET` | `/dashboard/realtime` | `dashboard.realtime` | `DashboardController@realtimeData` | Admin & Operator |
| `GET` | `/transaksi` | `transaksi.index` | `TransaksiController@index` | Admin & Operator |
| `GET` | `/transaksi/stok-realtime` | `transaksi.stok.realtime` | `TransaksiController@getStokRealtime` | Admin & Operator |
| `GET` | `/transaksi/masuk` | `transaksi.masuk` | `TransaksiController@createMasuk` | Admin & Operator |
| `POST` | `/transaksi/masuk` | `transaksi.masuk.store` | `TransaksiController@storeMasuk` | Admin & Operator |
| `GET` | `/transaksi/jual` | `transaksi.jual` | `TransaksiController@createJual` | Admin & Operator |
| `POST` | `/transaksi/jual` | `transaksi.jual.store` | `TransaksiController@storeJual` | Admin & Operator |
| `GET` | `/transaksi/transfer` | `transaksi.transfer` | `TransaksiController@createTransfer` | Admin & Operator |
| `POST` | `/transaksi/transfer` | `transaksi.transfer.store` | `TransaksiController@storeTransfer` | Admin & Operator |
| `POST` | `/transaksi/{transaksi}/batal` | `transaksi.batal` | `TransaksiController@batalkan` | Admin & Operator |
| `RESOURCE` | `/barang` | `barang.*` | `BarangController` *(CRUD 7 Rute)* | Khusus Admin |
| `RESOURCE` | `/gudang` | `gudang.*` | `GudangController` *(CRUD 7 Rute)* | Khusus Admin |
| `RESOURCE` | `/pelanggan` | `pelanggan.*` | `PelangganController` *(CRUD 7 Rute)* | Khusus Admin |
| `GET` | `/laporan` | `laporan.index` | `LaporanController@index` | Khusus Admin |
| `GET` | `/laporan/export` | `laporan.export` | `LaporanController@exportCsv` | Khusus Admin |

---

## Struktur Basis Data & Model Relasional

Aplikasi menggunakan skema database relasional dengan model-model Eloquent berikut:

```
[ users ] (Admin / Operator)
    │
    ├──< [ transaksi ] (No. Ref, Jenis: masuk/jual/transfer, Status: selesai/batal)
    │        │
    │        ├──< [ transaksi_detail ] (Barang ID, Qty, Harga, Subtotal)
    │        │        │
    │        │        └──> [ barang ] (SKU, Nama, Kategori, Satuan, Harga Pokok, Harga Jual)
    │        │
    │        ├──> [ gudang ] (Gudang Asal & Gudang Tujuan)
    │        │
    │        ├──> [ pelanggan ] (Nama, No HP, Alamat)
    │        │
    │        └──< [ riwayat_stok ] (Audit Trail Mutasi: Gudang ID, Barang ID, Tipe, Qty, Sisa Stok)
```

- **`users`**: Menyimpan kredensial pengguna, nama, email, password terenkripsi, dan wewenang peran (`role`: `admin` atau `operator`).
- **`barang`**: Data katalog produk, harga pokok (modal), harga jual, satuan, dan status aktif.
- **`gudang`**: Lokasi fisik penyimpanan persediaan barang dan status operasionalnya.
- **`pelanggan`**: Profil dan nomor kontak pelanggan untuk transaksi eceran maupun partai besar.
- **`transaksi`**: Header transaksi persediaan (tanggal, nomor referensi unik, tipe transaksi, status selesai/batal, kasir/user pencatat, gudang terkait, dan pelanggan terkait).
- **`transaksi_detail`**: Item rincian barang, kuantitas per baris pesanan, harga satuan, dan subtotal tagihan.
- **`riwayat_stok`**: Kartu mutasi stok terpusat yang mencatat saldo awal, mutasi masuk/keluar, saldo akhir sisa stok, dan keterangan referensi transaksi (*Audit Trail*).

---

## Kredensial Akun Bawaan (Demo)

Database seeder telah menyediakan dua akun siap pakai untuk pengujian sistem:

| Peran (Role) | Alamat Email | Kata Sandi (Password) | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@sinar.com` | `password` | Akses penuh seluruh modul (Dashboard, Transaksi, Master Data, Laporan & Ekspor CSV) |
| **Operator Gudang** | `operator@sinar.com` | `password` | Khusus operasional lapangan (Dashboard, Kasir Penjualan, Penerimaan Masuk, Transfer, Riwayat) |

---

## Panduan Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menyiapkan proyek di lingkungan lokal:

### 1. Kloning Repositori
```bash
git clone https://github.com/Varraaa/tes_kabayan.git
cd tes_kabayan
```

### 2. Pasang Dependensi Backend & Frontend
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file percontohan `.env.example` menjadi `.env`:
- **Windows (PowerShell / CMD):**
  ```powershell
  copy .env.example .env
  ```
- **Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

### 4. Buat Kunci Enkripsi Aplikasi (App Key)
```bash
php artisan key:generate
```

### 5. Inisialisasi Database
Secara bawaan aplikasi dikonfigurasi menggunakan basis data **SQLite**. Jika file database belum tersedia:
- **Windows (PowerShell):**
  ```powershell
  New-Item -ItemType File -Path database\database.sqlite -Force
  ```
- **Linux / macOS:**
  ```bash
  touch database/database.sqlite
  ```

### 6. Jalankan Migrasi Skema & Seeder Awal
```bash
php artisan migrate --seed
```

### 7. Menjalankan Server Aplikasi
Jalankan dua perintah berikut di dua terminal terpisah:

**Terminal 1 - Web Server PHP Laravel:**
```bash
php artisan serve --port=8004
```

**Terminal 2 - Asset Bundler (Vite):**
```bash
npm run dev
```

Buka peramban (*browser*) dan akses aplikasi melalui tautan:
👉 **[http://127.0.0.1:8004](http://127.0.0.1:8004)**

---

## Struktur Direktori Proyek

```
tes_kabayan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php       # Controller login, autentikasi, & logout
│   │   │   ├── BarangController.php            # Controller CRUD master barang & sanitasi nominal
│   │   │   ├── DashboardController.php         # Controller analitik & endpoint live polling JSON
│   │   │   ├── GudangController.php            # Controller CRUD master gudang
│   │   │   ├── LaporanController.php           # Controller pusat laporan & streamed CSV export
│   │   │   ├── PelangganController.php         # Controller CRUD master pelanggan
│   │   │   └── TransaksiController.php         # Controller transaksi (jual, masuk, transfer, batal)
│   │   └── Middleware/
│   │       └── CheckRole.php                   # Middleware pembatas hak akses (RBAC)
│   ├── Models/
│   │   ├── Barang.php                          # Model katalog produk
│   │   ├── Gudang.php                          # Model lokasi gudang
│   │   ├── Pelanggan.php                       # Model data pelanggan
│   │   ├── RiwayatStok.php                     # Model kartu mutasi stok (audit trail)
│   │   ├── Transaksi.php                       # Model header transaksi
│   │   ├── TransaksiDetail.php                 # Model rincian item transaksi
│   │   └── User.php                            # Model akun pengguna & peran (role)
│   └── Services/
│       └── TransaksiService.php                # Jantung logika bisnis (ACID mutations & rollback)
├── database/
│   ├── migrations/                             # Skema tabel database relasional
│   └── seeders/                                # Inisialisasi akun demo & data master awal
├── resources/
│   ├── css/                                    # Sumber CSS kustom & Tailwind directives
│   ├── js/                                     # Skrip JavaScript kustom
│   └── views/
│       ├── auth/                               # Blade view login
│       ├── barang/                             # Blade view master barang (index, create, edit)
│       ├── dashboard.blade.php                 # Blade view dashboard analitik & live feed
│       ├── gudang/                             # Blade view master gudang (index, create, edit)
│       ├── laporan/
│       │   └── index.blade.php                 # Blade view pusat laporan terpadu (3 tab)
│       ├── layouts/
│       │   └── app.blade.php                   # Master layout, navigasi sidebar, & header
│       ├── pelanggan/                          # Blade view master pelanggan (index, create, edit)
│       └── transaksi/                          # Blade view transaksi (jual, masuk, transfer, index)
├── routes/
│   ├── console.php                             # Rute perintah artisan console
│   └── web.php                                 # Definisi seluruh rute web aplikasi & middleware
├── storage/                                    # Log aplikasi & file upload internal
└── tests/                                      # Pengujian unit & fitur otomatis (PHPUnit / Pest)
```

---

## Perintah Artisan & Pengujian

```bash
# Membersihkan seluruh cache rute, view, dan konfigurasi:
php artisan optimize:clear

# Menampilkan seluruh daftar rute aplikasi di terminal:
php artisan route:list

# Menyetel ulang database dari nol dan menjalankan seeder ulang:
php artisan migrate:fresh --seed

# Menjalankan pengujian otomatis:
php artisan test
```

---

## Lisensi

Proyek ini dikembangkan di bawah lisensi [MIT](LICENSE).
Hak Cipta © 2026 **PT Sinar Nusantara**. Seluruh hak cipta dilindungi undang-undang.

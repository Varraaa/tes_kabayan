### 1. PEMBUKAAN

> "Selamat sore kepada Bapak dan Kakak sekalian yang saya hormati.
>
> Puji dan syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa. Pertama-tama, saya ingin mengucapkan terima kasih yang sebesar-besarnya kepada Bapak dan Kakak sekalian atas waktu, ruang, dan kesempatan berharga yang telah diberikan kepada saya pada sesi Zoom meeting sore hari ini.
>
> Perkenalkan, nama saya **Muhammad Rafi Hibatulloh**. Sebagai seorang pengembang perangkat lunak (*software developer*), fokus utama saya bukan sekadar menulis baris kode, melainkan bagaimana rekayasa sistem yang saya bangun dapat menjadi solusi nyata, efisien, dan memberikan nilai tambah yang terukur bagi operasional bisnis.
>
> Oleh karena itu, pada sore hari ini saya sangat antusias untuk mempresentasikan sebuah proyek *Web Application* berskala korporasi (*enterprise*) yang telah saya rancang dan bangun dari awal, yaitu **Sistem Manajemen Inventaris Multi-Gudang dan Kasir Point of Sale untuk PT Sinar Nusantara**."

---

### 2. APA SEBENARNYA WEB APLIKASI YANG SAYA BUAT INI?
**[Aksi Layar]**: *Tetap di halaman landing login, gerakkan kursor perlahan menunjukkan kerapian antarmuka.*

> "Aplikasi yang saya buat ini adalah sebuah **Enterprise Web Application** di bidang logistik rantai pasokan (*supply chain*) dan penjualan kasir ritel (*Point of Sale*). 
>
> Berbeda dengan website profil biasa yang hanya menampilkan teks statis untuk dibaca, web aplikasi ini adalah sistem pemrosesan operasional bisnis aktif. Sistem ini dirancang untuk menghubungkan dan mengendalikan pergerakan barang di berbagai lokasi gudang cabang secara terpusat, memfasilitasi transaksi kasir harian secara cepat dan presisi, serta menyajikan audit mutasi barang dan laporan keuangan secara instan."

---

### 3. MASALAH DALAM DUNIA NYATA
> "Dalam operasional perusahaan distribusi dan ritel modern, terdapat **3 masalah nyata** mendasar yang sangat sering terjadi di lapangan:
>
> 1. **Selisih Stok Fisik vs Buku (Data Asimetris)**:
>    Sering kali terjadi ketidakcocokan antara catatan barang di kantor dengan stok fisik nyata di gudang cabang. Perusahaan kesulitan mengetahui secara pasti berapa sisa stok riil karena pencatatan masih terpisah-pisah.
>
> 2. **Human Error pada Kasir & Pembukuan**:
>    Kasir rentan salah menghitung uang kembalian secara manual, salah memilih gudang asal barang, atau bahkan menjual barang melebihi kuota fisik yang ada di gudang sehingga menyebabkan saldo minus (*negative inventory*).
>
> 3. **Keterlambatan Laporan Keuangan & Mutasi**:
>    Pihak manajemen atau pemilik bisnis sering terlambat menerima laporan omset harian dan riwayat perpindahan barang, karena data harus direkap secara manual pada akhir bulan yang memakan waktu dan rentan manipulasi data."

---

### 4. SOLUSI YANG DIIMPLEMENTASIKAN PADA WEB APPS INI

> "Untuk menuntaskan ketiga masalah dunia nyata tersebut, web aplikasi **PT Sinar Nusantara** ini mengimplementasikan solusi terpadu:
>
> 1. **Multi-Warehouse Stock Isolation**:
>    Sistem mengisolasi dan mendata stok barang per masing-masing gudang secara terpisah, lengkap dengan modul **Transfer Antar-Gudang** yang menjamin barang keluar dari gudang asal pasti bertambah di gudang tujuan tanpa selisih.
>
> 2. **Strict Point of Sale (POS) Gatekeeper**:
>    Modul kasir dilengkapi validasi ketat: sistem mengecek sisa fisik barang di gudang secara real-time untuk mencegah stok minus, menghitung kembalian otomatis, dan mengunci tombol checkout jika uang tunai yang diterima belum diisi atau masih kurang.
>
> 3. **Automated Stock Mutation Ledger & Real-Time Dashboard**:
>    Setiap butir barang yang masuk, keluar, dipindahkan, ataupun dibatalkan, langsung tercatat otomatis ke dalam kartu mutasi stok (*audit trail*), dan ringkasan omset harian langsung terpantau pada dashboard pemantau real-time."

---

### 5. MASUK KE WEB: PENJELASAN TECH STACK SECARA MENYELURUH

> "Sebelum kita masuk membedah setiap halamannya, mari kita bedah terlebih dahulu **Tech Stack (tumpukan teknologi)** yang menopang web aplikasi ini:
>
> - **Backend Engine**: Menggunakan **PHP 8.3** dan framework **Laravel 13**, dengan mengadopsi pola arsitektur **MVC (Model-View-Controller)** yang diperkuat dengan **Service Layer Pattern** (`TransaksiService`) untuk memisahkan logika bisnis dari controller agar kode bersih, modular, dan terstruktur rapi.
> - **Database & ACID Transactions**: Menggunakan basis data relasional dengan penerapan **`DB::transaction`**, yang menjamin prinsip *Atomicity*: seluruh manipulasi stok dan keuangan berhasil bersamaan, atau dibatalkan seutuhnya (*rollback*) jika terjadi kegagalan.
> - **Frontend & User Interface**: Menggunakan templating engine **Blade**, dikombinasikan dengan **Tailwind CSS** dan komponen **Flowbite** untuk menghasilkan antarmuka modern, interaktif, bersih, responsif, dan mendukung *dark mode*.
> - **Client-Side Scripting**: Menggunakan **Vanilla JavaScript & Async Fetch API** untuk menangani komunikasi data ke server di latar belakang tanpa reload halaman, manipulasi DOM dinamis, serta *real-time input masking*.
>
> Sekarang, mari kita telusuri setiap halaman dan membedah teknologi utama apa saja yang bekerja di dalamnya."

---

### 6. MASUK KE SETIAP HALAMAN: BEDAH TEKNOLOGI UTAMA (CORE) PAGE-BY-PAGE

---

#### 6.1. Page Login & Autentikasi (`/login`)
> "Di page login ini, teknologi utama (*core*) yang saya gunakan adalah **Middleware `guest` dan Bcrypt Authentication**, yang di mana teknologi ini berfungsi untuk memvalidasi status sesi pengguna—sehingga pengguna yang sudah dalam kondisi login dicegah mengakses form login kembali dan langsung dialihkan ke dashboard—serta mengenkripsi kata sandi menjadi bentuk *salted hash* yang aman di database."

---

#### 6.2. Page Dashboard Operasional (`/dashboard`)
> "Di page dashboard ini, teknologi utama (*core*) yang saya gunakan adalah **Real-Time Data Polling (menggunakan JavaScript Fetch API)**, yang di mana teknologi ini berfungsi untuk memperbarui data di latar belakang secara otomatis setiap 5 detik.

"Fitur real-time di dashboard ini saya bangun menggunakan konsep Background Polling di file resources/views/dashboard.blade.php, di mana JavaScript menembak endpoint /dashboard/realtime yang dihandle oleh DashboardController.php setiap 5 detik. Dengan begitu, data omset dan aktivitas harian selalu sinkron secara live."
> 
> Maksudnya, ketika ada kasir yang baru saja menyelesaikan transaksi penjualan, angka omset dan daftar transaksi pada layar dashboard ini akan langsung terbarui secara otomatis dan *real-time*, tanpa pengguna harus me-refresh atau memuat ulang halaman browser."

---

#### 6.3. Page Master Data Barang (`/barang`)
> "Di page master barang ini, teknologi utama (*core*) yang saya gunakan adalah **Interactive Input Masking (Format Rupiah) & Backend Sanitization**, yang di mana teknologi ini berfungsi untuk memformat angka nominal yang diketikkan pengguna secara otomatis menjadi format rupiah (misal `60000` langsung berubah menjadi **`60.000`**), lalu membersihkan titiknya di sisi backend agar nilai numerik yang tersimpan ke database tetap murni, presisi, dan operator terhindar dari salah ketik jumlah nol."

---

#### 6.4. Page Master Gudang & Master Pelanggan (`/gudang` & `/pelanggan`)
> "Di page master gudang dan pelanggan ini, teknologi utama (*core*) yang saya gunakan adalah **RESTful Resource Controller & Eloquent Relational Mapping**, yang di mana arsitektur ini berfungsi untuk menstandarisasi pengelolaan seluruh operasi CRUD (tambah, lihat, ubah, hapus) secara modular dan menghubungkan relasi lokasi fisik gudang dengan seluruh riwayat mutasi barang."

---

#### 6.5. Page Kasir / Point of Sale (POS) (`/transaksi/jual`)
> "Di page kasir ini, teknologi utama (*core*) yang saya gunakan adalah **Real-Time AJAX Stock Checker & Strict Payment Gatekeeper**, yang di mana teknologi ini berfungsi untuk mengecek sisa kuota fisik barang di gudang secara langsung agar kasir tidak menjual melebihi stok yang ada (mencegah stok minus), serta secara otomatis mengunci tombol checkout dalam keadaan nonaktif (*disabled*) sampai uang yang diterima kasir diisi dan mencukupi nilai belanja."

"Di halaman kasir ini, saya menerapkan Real-Time AJAX Stock Checker & Strict Payment Gatekeeper. Maksudnya, sistem secara otomatis mengecek stok riil ke server agar kasir tidak bisa menjual melebihi sisa stok di gudang, serta mengunci tombol checkout agar tidak bisa diklik sama sekali sampai kasir memasukkan uang tunai yang cukup dari pembeli."

---

#### 6.6. Page Transaksi Masuk & Transfer Antar-Gudang (`/transaksi/masuk` & `/transaksi/transfer`)
> "Di page transaksi masuk dan transfer ini, teknologi utama (*core*) yang saya gunakan adalah **Database Transaction (`DB::transaction`)**, yang di mana teknologi ini berfungsi untuk mengamankan proses perpindahan stok barang agar selalu sinkron dan tidak ada data yang selisih:
> - Pada **Barang Masuk**: Sistem langsung mencatat barang yang baru dipasok ke gudang tujuan secara otomatis.
> - Pada **Transfer Antar-Gudang**: Sistem memotong stok di gudang asal dan langsung menambahkannya ke gudang tujuan secara bersamaan. Dengan teknologi ini, dijamin mustahil ada barang yang berkurang di gudang asal tapi gagal masuk ke gudang tujuan."

---

#### 6.7. Page Riwayat Transaksi & Pembatalan Transaksi (`/transaksi`)
> "Di page riwayat transaksi ini, teknologi utama (*core*) yang saya gunakan adalah **Stock Rollback / Reversal Mutation Algorithm**, yang di mana algoritma ini berfungsi untuk membatalkan transaksi yang keliru tanpa menghapus data dari database, lalu secara otomatis mengembalikan kuantitas fisik barang ke gudang asal demi menjaga transparansi jejak audit (*audit trail*)."

"Teknologi Stock Rollback / Reversal Mutation Algorithm ini adalah algoritma logika bisnis yang saya bangun sendiri di dalam TransaksiService. Tujuannya untuk menangani kasus pembatalan transaksi tanpa menghapus data (no hard delete). Sistem menandai transaksi menjadi 'Batal', lalu secara atomik mengeksekusi mutasi pembalik ke kartu stok di gudang terkait sehingga stok fisik barang kembali utuh seperti semula dan jejak audit pembukuan tetap terjaga."

---

#### 6.8. Page Pusat Laporan Terpadu & Export CSV (`/laporan`)
> "Di page laporan ini, teknologi utama (*core*) yang saya gunakan berfokus pada dua hal: **Filterisasi Data Dinamis** dan **Export CSV**:

> - **Filterisasi Data**: Saya menggunakan teknologi *Dynamic Query Filtering* di mana pengguna bisa menyaring data laporan dengan sangat fleksibel—mulai dari memilih gudang tertentu, memilih produk barang, menentukan rentang tanggal, hingga mencari nama barang. Sistem secara otomatis hanya menampilkan data yang sesuai dengan kriteria yang dipilih.

"Untuk filterisasi di halaman laporan, saya mengimplementasikannya menggunakan Dynamic Query Builder dari Eloquent Laravel. Fitur ini memanfaatkan method $request->filled() dan klausa dinamis seperti whereBetween untuk rentang tanggal dan whereHas untuk filter barang. Dengan teknik ini, query SQL yang dijalankan server menjadi sangat efisien karena hanya mengambil data sesuai kombinasi filter yang dipilih oleh pengguna."

> - **Export CSV**: Menggunakan teknologi *StreamedResponse*, di mana data laporan yang sudah difilter dapat langsung diunduh ke dalam format file Excel/CSV secara cepat, rapi, dan tidak membebani memori server walau datanya sangat banyak."

"StreamedResponse adalah komponen bawaan (core HTTP foundation) dari Laravel. Komponen ini sudah include di dalam Laravel untuk menangani response pengaliran data (streaming) secara langsung ke browser tanpa perlu menginstal package eksternal tambahan."

---

### 7. PENUTUPAN

> "Bapak dan Kakak sekalian yang saya hormati,
>
> Demikian pemaparan menyeluruh mengenai arsitektur, rekayasa logika bisnis, serta teknologi utama yang saya terapkan pada setiap halaman dalam sistem inventaris dan kasir **PT Sinar Nusantara**.
>
> Bagi saya pribadi, pengembangan web aplikasi ini adalah wujud komitmen saya sebagai developer dalam menghadirkan sistem yang tidak hanya memikat dari sisi antarmuka, melainkan juga tangguh secara fondasi arsitektur, presisi dalam integritas data, serta siap menjawab tantangan efisiensi bisnis modern.
>
> Saya sangat terbuka dan menghargai setiap masukan, saran, maupun pertanyaan dari Bapak dan Kakak sekalian demi penyempurnaan sistem ini ke depan.
>
> Terima kasih banyak atas perhatian dan waktu berharga yang telah diberikan kepada saya pada sore hari ini. Sekarang, dengan senang hati saya persilakan jika ada hal yang ingin didiskusikan lebih lanjut. Selamat sore."

---

### 8. PREDIKSI PERTANYAAN DISKUSI & JAWABAN DEVELOPER (CHEAT SHEET)

Berikut daftar pertanyaan teknis yang berpotensi muncul dalam sesi diskusi, lengkap dengan cara menjawab yang profesional:

---

#### Pertanyaan 1:
**"Mengapa Anda memilih menggunakan Service Layer (`TransaksiService`) daripada menuliskan seluruh logika langsung di Controller?"**

> **Jawaban Anda:**
> *"Penerapan Service Layer bertujuan untuk menerapkan prinsip **Single Responsibility Principle (SRP)** dalam standar arsitektur SOLID. Controller murni bertugas menerima HTTP Request dan mengembalikan response (View atau JSON). Logika bisnis inti—seperti pembungkusan `DB::transaction`, kalkulasi mutasi stok, pembuatan nomor referensi otomatis, dan pencatatan riwayat stok—diisolasi di dalam `TransaksiService`. Dengan pemisahan ini, kode menjadi lebih modular, mudah dipelihara (*maintainable*), dapat digunakan kembali (*reusable*), dan siap untuk pengujian otomatis (*unit testing*)."*

---

#### Pertanyaan 2:
**"Bagaimana aplikasi ini mencegah terjadinya selisih stok jika dua kasir menjual barang yang sama secara bersamaan (*race condition*)?"**

> **Jawaban Anda:**
> *"Aplikasi mencegahnya di dua sisi. Pertama, di sisi frontend, sistem memvalidasi sisa stok melalui endpoint AJAX real-time. Kedua dan yang paling utama, di sisi backend saat data disubmit, sistem mengecek sisa kuota riil dari record mutasi terakhir di database dan membungkus eksekusi pengurangan stok dalam blok **`DB::transaction`**. Jika sistem dioperasikan dalam volume transaksi konkuren yang sangat masif, kita dapat menambahkan query locking berupa `lockForUpdate()` pada model barang saat proses checkout, sehingga transaksi dieksekusi secara serial dan terjamin bebas dari race condition."*

---

#### Pertanyaan 3:
**"Kenapa Anda menggunakan `StreamedResponse` untuk fitur Export CSV, dan bukan library Excel biasa seperti Maatwebsite/Laravel-Excel?"**

> **Jawaban Anda:**
> *"Library Excel pihak ketiga umumnya membaca seluruh ribuan objek data ke dalam memori RAM server terlebih dahulu untuk membentuk file XML `.xlsx`. Jika data transaksi mencapai puluhan ribu, hal itu akan memicu fatal error **`Allowed memory size exhausted`** dan server bisa down. Dengan **`StreamedResponse`**, server mengalirkan data baris per baris (*chunk streaming*) langsung ke peramban pengguna tanpa membebani RAM server sama sekali. Selain itu, dengan menyisipkan **UTF-8 BOM (`\xEF\xBB\xBF`)**, file CSV langsung otomatis terbaca rapi dan berkolom di Microsoft Excel tanpa font rusak."*

---

#### Pertanyaan 4:
**"Mengapa proyek ini dikategorikan sebagai Web Application dan bukan sekadar Website biasa?"**

> **Jawaban Anda:**
> *"Karena proyek ini berorientasi pada penyelesaian masalah operasional (*task-driven*), bukan sekadar penyajian informasi statis. Web aplikasi ini memiliki **logika bisnis yang kompleks** (mutasi stok otomatis, kalkulasi kembalian, pembatalan transaksi berjejak audit), menerapkan **manajemen hak akses bertingkat (RBAC)** melalui middleware, terintegrasi penuh dengan **database relasional berbasis transaksi ACID**, serta memiliki **komunikasi asinkronus (Fetch API/AJAX)** untuk pembaruan data real-time di latar belakang tanpa reload halaman."*

---

#### Pertanyaan 5:
**"Apa yang terjadi pada sistem ketika seorang kasir membatalkan sebuah transaksi di halaman Riwayat Transaksi?"**

> **Jawaban Anda:**
> *"Sistem menerapkan **Stock Rollback / Reversal Mutation Algorithm**. Data transaksi tidak dihapus dari database demi menjaga integritas jejak audit (*audit trail*). Sistem akan mengubah status transaksi menjadi 'Dibatalkan', lalu secara otomatis membuat mutasi stok pembalik di tabel `riwayat_stok` yang mengkreditkan kembali jumlah fisik barang ke gudang asal transaksi. Dengan demikian, saldo stok fisik barang kembali utuh seperti sebelum transaksi terjadi."*

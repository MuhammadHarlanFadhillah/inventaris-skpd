---

SISTEM INFORMASI INVENTARIS & GUDANG BARANG SKPD

---

DESKRIPSI
Aplikasi Sistem Informasi Inventaris Barang berbasis PHP dan MySQL yang digunakan
untuk mengelola data barang, stok persediaan, transaksi barang masuk dan keluar,
serta pembuatan laporan inventaris yang siap cetak.

Aplikasi ini dirancang untuk membantu instansi SKPD dalam melakukan pencatatan
inventaris secara terstruktur, akurat, dan efisien.

---

FITUR UTAMA

1. Manajemen Data Barang

   * Tambah data barang
   * Edit data barang
   * Hapus data barang
   * Menyimpan informasi kode, uraian, spesifikasi, satuan, dan stok

2. Manajemen Stok Barang

   * Transaksi barang masuk
   * Transaksi barang keluar
   * Stok otomatis bertambah dan berkurang
   * Validasi agar stok tidak bernilai minus

3. Riwayat Transaksi

   * Menampilkan seluruh transaksi masuk dan keluar
   * Berdasarkan tanggal dan ID transaksi
   * Hapus satu transaksi (stok otomatis dikembalikan)
   * Hapus seluruh transaksi

4. Laporan

   * Laporan stok barang keseluruhan
   * Laporan transaksi berdasarkan periode tanggal
   * Tampilan khusus cetak (kop surat, logo, tanda tangan)

5. Tabel Interaktif

   * Pencarian data
   * Sorting
   * Pagination

---

TEKNOLOGI YANG DIGUNAKAN

Backend    : PHP Native
Database   : MySQL / MariaDB
Frontend   : Bootstrap 5, Font Awesome, DataTables
Server     : XAMPP / Laragon / Hosting PHP

---

STRUKTUR FOLDER

inventaris_skpd
|
|-- assets
|   |-- css
|   |-- js
|   |-- img
|       |-- gudang.png
|
|-- barang
|   |-- index.php
|   |-- tambah.php
|   |-- edit.php
|   |-- hapus.php
|
|-- transaksi
|   |-- index.php
|   |-- tambah.php
|   |-- hapus.php
|   |-- hapus_semua.php
|
|-- laporan
|   |-- index.php
|   |-- cetak_stok.php
|   |-- cetak_transaksi.php
|
|-- config
|   |-- koneksi.php
|
|-- layout
|   |-- header.php
|   |-- footer.php
|
|-- index.php

---

INSTALASI DAN KONFIGURASI

1. Upload atau salin folder inventaris_skpd ke:

   * htdocs (XAMPP)
   * public_html (Hosting)

2. Konfigurasi database
   Buka file:
   config/koneksi.php

   Sesuaikan dengan server:
   host     : localhost
   username : root
   password : (kosong)
   database : inventaris_skpd

3. Import database
   Import file SQL ke phpMyAdmin
   Pastikan tabel berikut tersedia:

   * BARANG
   * STOK_PERSEDIAAN
   * DETAIL_STOK

4. Akses aplikasi
   Buka browser:
   [http://localhost/inventaris_skpd](http://localhost/inventaris_skpd)

---

FITUR CETAK LAPORAN

* Laporan Stok Barang
  Menu: Laporan -> Laporan Aset & Stok

* Laporan Transaksi Periode
  Menu: Laporan -> Laporan Periode
  Pilih tanggal awal dan akhir

Semua laporan sudah disesuaikan untuk cetak kertas A4.

---

CATATAN KEAMANAN (DISARANKAN)

* Tambahkan sistem login dan session
* Gunakan validasi input
* Proteksi folder config
* Backup database secara berkala

---

LISENSI

Aplikasi ini digunakan untuk keperluan internal dan edukasi.
Bebas dikembangkan sesuai kebutuhan instansi.

---

PENGEMBANG

Dikembangkan untuk kebutuhan
Memenuhi Tugas Mata Kuliah Pemograman Basis Data Unikom

---


# 🚨 URGENT FIX: Table 'railway.BARANG' doesn't exist

## ❌ Error:

```
Fatal error: Uncaught mysqli_sql_exception: Table 'railway.BARANG' doesn't exist
in /app/transaksi/tambah.php:43
```

## 🔍 Penyebab:

Database Railway masih punya **TRIGGER LAMA** yang menggunakan nama tabel `BARANG` (huruf besar). Saat Anda INSERT ke `detail_stok`, trigger otomatis jalan dan mencoba:

```sql
UPDATE BARANG SET STOK_AKHIR = ...
```

Tapi di Railway (Linux), tabelnya namanya `barang` (huruf kecil), jadi error!

## ✅ Solusi (3 Pilihan):

### ✨ PILIHAN 1: Auto-Fix via Web (PALING MUDAH)

1. **Buka di browser Railway Anda:**

   ```
   https://yourrailwayapp.com/config/auto_fix_railway.php
   ```

2. **Tunggu script berjalan** - akan:
   - ✅ Drop semua trigger bermasalah
   - ✅ Rename kolom UPPERCASE → lowercase (jika ada)
   - ✅ Verifikasi database

3. **Selesai!** Coba tambah transaksi lagi.

---

### 🛠️ PILIHAN 2: Manual via MySQL Client

```bash
# Connect ke Railway
mysql -h <MYSQL_HOST> -P <MYSQL_PORT> -u root -p<MYSQL_PASSWORD> railway

# Jalankan di MySQL console:
DROP TRIGGER IF EXISTS update_stok_after_insert;
DROP TRIGGER IF EXISTS update_stok_after_delete;
DROP TRIGGER IF EXISTS update_stok_masuk;
DROP TRIGGER IF EXISTS update_stok_keluar;
DROP TRIGGER IF EXISTS barang_masuk;
DROP TRIGGER IF EXISTS barang_keluar;
DROP TRIGGER IF EXISTS TG_STOK_UPDATE;

# Verifikasi
SHOW TRIGGERS;  -- Harus kosong
```

---

### 📡 PILIHAN 3: Import Database Baru

Jika masih error setelah drop trigger, gunakan database baru yang sudah fixed:

1. **Drop tabel lama:**

   ```sql
   DROP TABLE IF EXISTS detail_stok;
   DROP TABLE IF EXISTS stok_persediaan;
   DROP TABLE IF EXISTS barang;
   DROP TABLE IF EXISTS skpd;
   DROP TABLE IF EXISTS user;
   ```

2. **Import database baru:**
   ```bash
   mysql -h <HOST> -P <PORT> -u root -p<PASSWORD> railway < DATABASE/db_inventaris_railway.sql
   ```

---

## 🔧 Code Sudah Diupdate

File **[transaksi/tambah.php](../transaksi/tambah.php)** sudah saya update dengan:

✅ **Error handling** untuk trigger error
✅ **Auto-retry** jika trigger error terjadi
✅ **Drop trigger** saat session jalan
✅ **PHP redirect** yang lebih reliable

---

## 📋 Checklist:

- [ ] Jalankan `/config/auto_fix_railway.php` DI RAILWAY (bukan lokal!)
- [ ] Cek Railway Dashboard → buka URL aplikasi Anda + `/config/auto_fix_railway.php`
- [ ] Tunggu script selesai
- [ ] Test tambah transaksi
- [ ] Pastikan tidak ada error
- [ ] Cek stok barang terupdate

---

## 🎯 Expected Result (Setelah Fix):

✅ Click "Simpan Transaksi" → **Berhasil Disimpan**
✅ Redirect ke halaman Riwayat Transaksi
✅ Data transaksi muncul di tabel
✅ Stok barang di Master Barang berubah
✅ **TIDAK ADA ERROR!**

---

## ⚠️ Jika Masih Error:

1. **Pastikan trigger benar-benar sudah dihapus:**

   ```sql
   SHOW TRIGGERS;
   -- Harus kosong (tidak ada hasil)
   ```

2. **Cek nama kolom database:**

   ```sql
   DESCRIBE barang;
   -- Semua harus lowercase: id_barang, nama_barang, satuan, spesifikasi, stok_akhir
   ```

3. **Cek nama tabel:**

   ```sql
   SHOW TABLES;
   -- Harus ada: barang, detail_stok, skpd, stok_persediaan, user
   ```

4. **Jika masih bermasalah, hubungi support dengan error message lengkap.**

---

## 💡 Tips:

- **Auto-fix script** sudah include di project
- **Bisa dijalankan berkali-kali** (aman)
- **Tidak akan menghapus data** yang sudah ada
- **Hanya fix struktur table**

# 🔧 FIX ERROR RAILWAY - COMPLETE GUIDE

## ❌ Error yang Terjadi:

```
Fatal error: Uncaught mysqli_sql_exception: Table 'railway.BARANG' doesn't exist
in /app/transaksi/tambah.php:43
```

## 🔍 Penyebab:

1. **Railway menggunakan MySQL di Linux** → Case-sensitive untuk nama tabel/kolom
2. **Database memiliki TRIGGER** yang masih menggunakan nama tabel `BARANG` (huruf besar)
3. **Trigger dijalankan otomatis** saat INSERT ke `detail_stok`

## ✅ Solusi Lengkap:

### LANGKAH 1: Drop Database Lama & Import Baru

1. **Akses Railway Database:**
   - Buka Railway Dashboard
   - Pilih Database → Variables
   - Copy semua credentials (HOST, PORT, USER, PASSWORD, DATABASE)

2. **Connect via MySQL Client:**

   ```bash
   mysql -h <MYSQL_HOST> -P <MYSQL_PORT> -u root -p<MYSQL_PASSWORD> railway
   ```

3. **Drop semua tabel & trigger:**

   ```sql
   -- Drop trigger dulu
   DROP TRIGGER IF EXISTS update_stok_after_insert;
   DROP TRIGGER IF EXISTS update_stok_after_delete;

   -- Drop tabel (otomatis drop constraint)
   DROP TABLE IF EXISTS detail_stok;
   DROP TABLE IF EXISTS stok_persediaan;
   DROP TABLE IF EXISTS barang;
   DROP TABLE IF EXISTS skpd;
   DROP TABLE IF EXISTS user;
   ```

4. **Import database baru:**

   ```bash
   mysql -h <MYSQL_HOST> -P <MYSQL_PORT> -u root -p<MYSQL_PASSWORD> railway < DATABASE/db_inventaris_railway.sql
   ```

5. **Verifikasi:**

   ```sql
   SHOW TABLES;
   -- Harus menampilkan: barang, detail_stok, skpd, stok_persediaan, user (huruf kecil semua)

   SHOW TRIGGERS;
   -- Harus kosong atau tidak ada (karena sudah dihapus)

   SELECT * FROM barang;
   -- Harus ada 3 data dummy
   ```

### LANGKAH 2: Push Code Terbaru ke Railway

```bash
# Pastikan semua file sudah di-commit
git add .
git commit -m "Fix Railway case-sensitive issue & trigger error"
git push origin main
```

Railway akan otomatis deploy ulang.

### LANGKAH 3: Test di Railway

1. Buka aplikasi Railway Anda
2. Login dengan `admin` / `admin`
3. Test tambah transaksi:
   - Pilih barang
   - Pilih jenis (Masuk/Keluar)
   - Isi jumlah & harga
   - Klik Simpan
4. **Harus redirect ke halaman Riwayat Transaksi** dengan alert sukses
5. Cek apakah:
   - ✅ Data muncul di Riwayat Transaksi
   - ✅ Stok barang berubah (lihat di Master Barang)
   - ✅ Tidak ada error

## 🛠️ Troubleshooting

### Error: "Access denied for user 'root'@'...' to database 'railway'"

**Solusi:** Gunakan nama database yang benar dari Railway Variables

### Error: "Unknown database 'railway'"

**Solusi:**

```sql
CREATE DATABASE IF NOT EXISTS railway;
USE railway;
-- lalu import
```

### Trigger masih ada setelah di-drop

**Solusi:**

```sql
-- Drop manual satu per satu
DROP TRIGGER IF EXISTS update_stok_after_insert;
DROP TRIGGER IF EXISTS update_stok_after_delete;
DROP TRIGGER IF EXISTS update_stok_masuk;
DROP TRIGGER IF EXISTS update_stok_keluar;

-- Cek lagi
SHOW TRIGGERS;
```

### Data hilang setelah import

**Solusi:** Import ulang & pastikan file `db_inventaris_railway.sql` sudah ter-upload

## 📋 Checklist Final

- [ ] Database lama sudah di-drop
- [ ] Database baru sudah di-import
- [ ] SHOW TABLES → semua huruf kecil
- [ ] SHOW TRIGGERS → kosong
- [ ] Code sudah di-push ke Railway
- [ ] Deploy selesai (check Railway logs)
- [ ] Test tambah transaksi → berhasil
- [ ] Data masuk ke riwayat transaksi
- [ ] Stok barang terupdate

## 🎯 Expected Result

**Setelah simpan transaksi:**

1. Redirect ke `/transaksi/index.php?msg=success`
2. Muncul alert hijau: "✅ Berhasil! Transaksi telah disimpan."
3. Data transaksi muncul di tabel Riwayat Transaksi
4. Stok barang di Master Barang berubah sesuai jenis transaksi

**Tidak boleh ada:**

- ❌ Error "Table 'railway.BARANG' doesn't exist"
- ❌ Data masuk tapi error
- ❌ Redirect gagal

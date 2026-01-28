#!/bin/bash
# =======================================================
# RAILWAY DATABASE IMPORT SCRIPT
# =======================================================
# Cara pakai:
# 1. Isi kredensial Railway di bawah ini
# 2. Jalankan: bash import_to_railway.sh
# =======================================================

echo "========================================="
echo "RAILWAY DATABASE IMPORT TOOL"
echo "========================================="
echo ""

# ===== ISI KREDENSIAL RAILWAY DI SINI =====
# Ambil dari Railway Dashboard -> Database -> Variables
MYSQL_HOST="your-railway-host.railway.app"
MYSQL_PORT="3306"
MYSQL_USER="root"
MYSQL_PASSWORD="your-password-here"
MYSQL_DATABASE="railway"
# ===========================================

echo "🔧 Config:"
echo "   Host: $MYSQL_HOST"
echo "   Port: $MYSQL_PORT"
echo "   Database: $MYSQL_DATABASE"
echo ""

read -p "Apakah kredensial sudah benar? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "❌ Dibatalkan. Silakan edit file ini dan isi kredensial yang benar."
    exit 1
fi

echo ""
echo "⚠️  PERINGATAN: Script ini akan MENGHAPUS semua data lama!"
read -p "Lanjutkan? (y/n): " confirm2
if [ "$confirm2" != "y" ]; then
    echo "❌ Dibatalkan."
    exit 1
fi

echo ""
echo "🗑️  Step 1: Drop trigger & tabel lama..."
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE <<EOF
DROP TRIGGER IF EXISTS update_stok_after_insert;
DROP TRIGGER IF EXISTS update_stok_after_delete;
DROP TRIGGER IF EXISTS update_stok_masuk;
DROP TRIGGER IF EXISTS update_stok_keluar;
DROP TABLE IF EXISTS detail_stok;
DROP TABLE IF EXISTS stok_persediaan;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS skpd;
DROP TABLE IF EXISTS user;
EOF

if [ $? -eq 0 ]; then
    echo "✅ Drop tabel & trigger berhasil"
else
    echo "❌ Gagal drop tabel. Periksa koneksi database."
    exit 1
fi

echo ""
echo "📥 Step 2: Import database baru..."
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < DATABASE/db_inventaris_railway.sql

if [ $? -eq 0 ]; then
    echo "✅ Import database berhasil!"
else
    echo "❌ Gagal import database."
    exit 1
fi

echo ""
echo "🔍 Step 3: Verifikasi..."
mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE <<EOF
SHOW TABLES;
SHOW TRIGGERS;
SELECT COUNT(*) as total_barang FROM barang;
EOF

echo ""
echo "========================================="
echo "✅ SELESAI!"
echo "========================================="
echo ""
echo "Langkah selanjutnya:"
echo "1. Buka Railway Dashboard"
echo "2. Tunggu deployment selesai"
echo "3. Test aplikasi (tambah transaksi)"
echo ""

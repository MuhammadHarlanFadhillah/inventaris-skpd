@echo off
REM =======================================================
REM RAILWAY DATABASE IMPORT SCRIPT (Windows)
REM =======================================================
REM Cara pakai:
REM 1. Isi kredensial Railway di bawah ini
REM 2. Install MySQL Client (XAMPP sudah include)
REM 3. Jalankan: import_to_railway.bat
REM =======================================================

echo =========================================
echo RAILWAY DATABASE IMPORT TOOL (Windows)
echo =========================================
echo.

REM ===== ISI KREDENSIAL RAILWAY DI SINI =====
REM Ambil dari Railway Dashboard -^> Database -^> Variables
set MYSQL_HOST=your-railway-host.railway.app
set MYSQL_PORT=3306
set MYSQL_USER=root
set MYSQL_PASSWORD=your-password-here
set MYSQL_DATABASE=railway
REM ===========================================

set MYSQL_BIN=C:\xampp\mysql\bin\mysql.exe

echo Config:
echo    Host: %MYSQL_HOST%
echo    Port: %MYSQL_PORT%
echo    Database: %MYSQL_DATABASE%
echo.

echo PERINGATAN: Script ini akan MENGHAPUS semua data lama!
pause

echo.
echo [Step 1] Drop trigger ^& tabel lama...
echo DROP TRIGGER IF EXISTS update_stok_after_insert; DROP TRIGGER IF EXISTS update_stok_after_delete; DROP TABLE IF EXISTS detail_stok; DROP TABLE IF EXISTS stok_persediaan; DROP TABLE IF EXISTS barang; DROP TABLE IF EXISTS skpd; DROP TABLE IF EXISTS user; | "%MYSQL_BIN%" -h %MYSQL_HOST% -P %MYSQL_PORT% -u %MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE%

if %errorlevel% neq 0 (
    echo Gagal drop tabel. Periksa koneksi database.
    pause
    exit /b 1
)
echo Done!

echo.
echo [Step 2] Import database baru...
"%MYSQL_BIN%" -h %MYSQL_HOST% -P %MYSQL_PORT% -u %MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE% < DATABASE\db_inventaris_railway.sql

if %errorlevel% neq 0 (
    echo Gagal import database.
    pause
    exit /b 1
)
echo Done!

echo.
echo [Step 3] Verifikasi...
echo SHOW TABLES; SHOW TRIGGERS; SELECT COUNT(*) as total_barang FROM barang; | "%MYSQL_BIN%" -h %MYSQL_HOST% -P %MYSQL_PORT% -u %MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE%

echo.
echo =========================================
echo SELESAI!
echo =========================================
echo.
echo Langkah selanjutnya:
echo 1. Buka Railway Dashboard
echo 2. Tunggu deployment selesai
echo 3. Test aplikasi (tambah transaksi)
echo.
pause

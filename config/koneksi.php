<?php
// =========================================================
// KONFIGURASI UTAMA SISTEM (DATABASE & BASE URL)
// =========================================================

// 1. PENGATURAN WAKTU
// Set zona waktu ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');

// 2. PENGATURAN BASE URL
// Fungsi: Agar link (CSS, JS, Gambar, Menu) tidak error saat masuk ke sub-folder.
// PENTING: Ganti 'inventaris_skpd' sesuai nama folder asli project Anda di htdocs.
// Pastikan diakhiri tanda slash '/'
$base_url = "http://localhost/inventaris_skpd/";

$host = $_ENV['MYSQLHOST'] ?? 'localhost';
$user = $_ENV['MYSQLUSER'] ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? '';
$db   = $_ENV['MYSQLDATABASE'] ?? 'db_inventaris';
$port = $_ENV['MYSQLPORT'] ?? 3306;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("❌ KONEKSI DATABASE GAGAL: " . mysqli_connect_error());
}


// 4. MEMBUAT KONEKSI
$conn = mysqli_connect($host, $user, $pass, $db);

// 5. CEK KONEKSI
if (!$conn) {
    // Jika gagal, stop loading dan tampilkan pesan error
    die("❌ KONEKSI DATABASE GAGAL: " . mysqli_connect_error());
}
?>
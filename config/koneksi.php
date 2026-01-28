<?php
date_default_timezone_set('Asia/Jakarta');

// BASE URL: Nanti kalau udah deploy, ganti "localhost" jadi domain Railway lu
$base_url = "http://localhost/inventaris_skpd/"; 

// DETEKSI KONEKSI (Otomatis baca settingan Railway)
$host = $_ENV['MYSQLHOST'] ?? 'localhost'; 
$user = $_ENV['MYSQLUSER'] ?? 'root';
$pass = $_ENV['MYSQLPASSWORD'] ?? ''; // Kosongin aja kalau di laptop gak ada pass
$db   = $_ENV['MYSQLDATABASE'] ?? 'db_inventaris';
$port = $_ENV['MYSQLPORT'] ?? 3306;

// BIKIN KONEKSI
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// CEK KONEKSI
if (!$conn) {
    die("❌ Gagal Connect: " . mysqli_connect_error());
}
?>git 
<?php
date_default_timezone_set('Asia/Jakarta');

// Load .env file
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Auto-detect domain (bisa Railway atau custom domain)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$http_host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . "://" . $http_host . "/";

// PAKE GETENV (Lebih aman buat Server)
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Debugging: Kalau variabel kosong, paksa error biar ketahuan
if (!$host) {
    die("❌ Error: Variabel Environment belum terbaca. Cek .env atau Tab Variables di Railway.");
}

// BIKIN KONEKSI
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// CEK KONEKSI
if (!$conn) {
    die("❌ Gagal Connect: " . mysqli_connect_error());
}
?>
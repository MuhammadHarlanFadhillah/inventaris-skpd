<?php
date_default_timezone_set('Asia/Jakarta');

$base_url = "https://inventaris-skpd.app"; // Ganti domain railway lu

// PAKE GETENV (Lebih aman buat Server)
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Debugging: Kalau variabel kosong, paksa error biar ketahuan
if (!$host) {
    die("❌ Error: Variabel Environment belum terbaca. Cek Tab Variables di Railway.");
}

// BIKIN KONEKSI
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// CEK KONEKSI
if (!$conn) {
    die("❌ Gagal Connect: " . mysqli_connect_error());
}
?>
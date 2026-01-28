<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect(
    getenv("MYSQLHOST"),
    getenv("MYSQLUSER"),
    getenv("MYSQLPASSWORD"),
    getenv("MYSQLDATABASE"),
    getenv("MYSQLPORT")
);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$sqlPath = __DIR__ . "/DATABASE/db_inventaris.sql";

if (!file_exists($sqlPath)) {
    die("FILE SQL TIDAK DITEMUKAN: " . $sqlPath);
}

$sql = file_get_contents($sqlPath);

if (!$sql) {
    die("FILE SQL TIDAK BISA DIBACA");
}

$queries = explode(";", $sql);
foreach ($queries as $q) {
    if (trim($q)) {
        mysqli_query($conn, $q);
    }
}

echo "DATABASE BERHASIL DIIMPORT";

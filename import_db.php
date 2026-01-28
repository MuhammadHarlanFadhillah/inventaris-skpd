<?php
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

$sql = file_get_contents(__DIR__ . "/DATABASE/db_inventaris.sql");

if (!$sql) {
    die("File SQL tidak terbaca");
}

$queries = explode(";", $sql);
foreach ($queries as $q) {
    if (trim($q)) {
        mysqli_query($conn, $q);
    }
}

echo "DATABASE BERHASIL DIIMPORT";

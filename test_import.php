<?php
// Test connection dan import database
$host = 'hopper.proxy.rlwy.net';
$user = 'root';
$pass = 'elItqXkhfoUSKEbmvKXYDmKFGOZNnhVn';
$db = 'railway';
$port = 39363;

// Connect
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    echo "❌ Connection Failed: " . mysqli_connect_error();
    exit;
}

echo "✅ Connected to Railway Database\n\n";

// Check existing tables
$result = mysqli_query($conn, "SHOW TABLES;");
echo "📊 Existing Tables:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "  - " . $row[key($row)] . "\n";
}

// Check barang count
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang;");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "\n📦 Barang Count: " . $row['total'] . "\n";
}

// Try to import SQL
echo "\n⏳ Importing database schema...\n";
$sql = file_get_contents(__DIR__ . '/DATABASE/db_inventaris_clean.sql');

if (mysqli_multi_query($conn, $sql)) {
    while (mysqli_more_results($conn)) {
        mysqli_next_result($conn);
    }
    echo "✅ Import berhasil!\n";
} else {
    echo "❌ Import gagal: " . mysqli_error($conn) . "\n";
}

// Verify after import
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang;");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "📦 Barang Count after import: " . $row['total'] . "\n";
}

mysqli_close($conn);
?>

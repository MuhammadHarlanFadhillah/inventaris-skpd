<?php
// File proses, tidak perlu header tampilan
include '../config/koneksi.php';

// Aktifkan exception MySQLi agar try-catch bekerja
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id'])) {

    $id = trim($_GET['id']);

    try {

        // Proses hapus barang
        $sql   = "DELETE FROM BARANG WHERE ID_BARANG = '$id'";
        $hapus = mysqli_query($conn, $sql);

        if ($hapus) {
            echo "<script>
                alert('✅ Data Barang berhasil dihapus.');
                window.location = 'index.php';
            </script>";
        }

    } catch (mysqli_sql_exception $e) {

        // Error FK: Barang sudah dipakai di DETAIL_STOK
        if ($e->getCode() == 1451) {
            echo "<script>
                alert('⛔ GAGAL MENGHAPUS!\\n\\nBarang ini sudah memiliki transaksi stok (masuk/keluar).\\nSilakan hapus data transaksi terlebih dahulu.');
                window.location = 'index.php';
            </script>";
        } else {
            echo "<script>
                alert('❌ Error Database: {$e->getMessage()}');
                window.location = 'index.php';
            </script>";
        }
    }

} else {
    // Jika akses langsung tanpa ID
    header('Location: index.php');
    exit;
}
?>

<?php
// File proses, tidak perlu header tampilan
include '../config/koneksi.php';

// Aktifkan mode error reporting exception untuk MySQLi
// Agar block try-catch bisa menangkap error database
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id'])) {

    // Amankan ID dari URL
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    try {
        // Proses hapus barang
        // REVISI: Menggunakan $koneksi
        $sql   = "DELETE FROM BARANG WHERE ID_BARANG = '$id'";
        $hapus = mysqli_query($conn, $sql);

        if ($hapus) {
            echo "<script>
                alert('✅ Data Barang berhasil dihapus.');
                window.location = 'index.php';
            </script>";
        }

    } catch (mysqli_sql_exception $e) {

        // Error Code 1451: Cannot delete or update a parent row (Foreign Key Fail)
        // Artinya: Barang ini sedang dipakai di tabel transaksi/detail_stok
        if ($e->getCode() == 1451) {
            echo "<script>
                alert('⛔ TIDAK BISA DIHAPUS!\\n\\nBarang ini memiliki riwayat transaksi (masuk/keluar).\\nData historis tidak boleh dihapus sembarangan.');
                window.location = 'index.php';
            </script>";
        } else {
            // Error database lainnya
            echo "<script>
                alert('❌ Error Database: " . $e->getMessage() . "');
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
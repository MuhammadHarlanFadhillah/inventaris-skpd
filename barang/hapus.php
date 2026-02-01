<?php
// File proses, tidak perlu header tampilan
include '../config/koneksi.php';

if (isset($_GET['id'])) {

    // Amankan ID dari URL
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Proses hapus barang
    $sql   = "DELETE FROM barang WHERE id_barang = '$id'";
    $hapus = mysqli_query($conn, $sql);

    if ($hapus) {
        echo "<script>
            alert('✅ Data Barang berhasil dihapus.');
            window.location = 'index.php';
        </script>";
    } else {
        // Cek jenis error
        $error_code = mysqli_errno($conn);
        
        // Error Code 1451: Cannot delete or update a parent row (Foreign Key Fail)
        if ($error_code == 1451) {
            echo "<script>
                alert('⛔ TIDAK BISA DIHAPUS!\\n\\nBarang ini memiliki riwayat transaksi (masuk/keluar).\\nData historis tidak boleh dihapus sembarangan.');
                window.location = 'index.php';
            </script>";
        } else {
            // Error database lainnya
            echo "<script>
                alert('❌ Error Database: " . mysqli_error($conn) . "');
                window.location = 'index.php';
            </script>";
        }
    }

} else {
    // Jika akses langsung tanpa ID
    echo "<script>
        window.location = 'index.php';
    </script>";
    exit;
}
?>
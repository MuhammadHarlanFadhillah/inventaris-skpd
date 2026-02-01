<?php
// =======================================================
// HAPUS SEMUA RIWAYAT & RESET STOK KE 0
// =======================================================
include '../config/koneksi.php';

// Cek akses langsung
if (!isset($koneksi)) { die('Error Koneksi'); }

mysqli_begin_transaction($koneksi);

try {
    // 1. KOSONGKAN TABEL TRANSAKSI
    // Hapus detail dulu karena Foreign Key
    mysqli_query($conn, "DELETE FROM DETAIL_STOK");
    
    // Hapus header
    mysqli_query($conn, "DELETE FROM STOK_PERSEDIAAN");

    // 2. RESET STOK BARANG MENJADI 0
    // Karena riwayat hilang, stok fisik dianggap nol kembali
    $reset_stok = mysqli_query($conn, "UPDATE BARANG SET STOK_AKHIR = 0");

    if (!$reset_stok) {
        throw new Exception("Gagal mereset stok barang.");
    }

    mysqli_commit($koneksi);

    echo "<script>
        alert('✅ DATABASE DI-RESET!\\nSemua riwayat dihapus dan stok barang kembali ke 0.');
        window.location = 'index.php';
    </script>";

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    
    echo "<script>
        alert('❌ Gagal Reset: " . $e->getMessage() . "');
        window.location = 'index.php';
    </script>";
}
?>
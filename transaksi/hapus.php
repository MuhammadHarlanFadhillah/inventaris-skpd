<?php
// =======================================================
// HAPUS SATU TRANSAKSI & ROLLBACK STOK
// =======================================================
include '../config/koneksi.php';

// Pastikan ada ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$id_stok = mysqli_real_escape_string($koneksi, $_GET['id']);

// Mulai Transaksi
mysqli_begin_transaction($koneksi);

try {
    // 1. AMBIL DATA LAMA SEBELUM DIHAPUS
    // Kita butuh tahu barang apa dan berapa jumlahnya untuk dikembalikan
    $cek_data = mysqli_query($conn, "SELECT ID_BARANG, KUANTITAS_MASUK, KUANTITAS_KELUAR FROM DETAIL_STOK WHERE ID_STOK = '$id_stok'");
    $data = mysqli_fetch_assoc($cek_data);

    if (!$data) {
        throw new Exception("Data transaksi tidak ditemukan.");
    }

    $id_barang = $data['ID_BARANG'];
    $masuk     = $data['KUANTITAS_MASUK']; // Contoh: 10
    $keluar    = $data['KUANTITAS_KELUAR']; // Contoh: 0

    // 2. LOGIKA ROLLBACK STOK (PENTING!)
    // Jika dulu barang MASUK, sekarang stok harus DIKURANGI.
    // Jika dulu barang KELUAR, sekarang stok harus DITAMBAH.
    
    if ($masuk > 0) {
        // Hapus transaksi masuk -> Stok dikurangi
        $sql_update = "UPDATE BARANG SET STOK_AKHIR = STOK_AKHIR - $masuk WHERE ID_BARANG = '$id_barang'";
    } else {
        // Hapus transaksi keluar -> Stok dikembalikan (ditambah)
        $sql_update = "UPDATE BARANG SET STOK_AKHIR = STOK_AKHIR + $keluar WHERE ID_BARANG = '$id_barang'";
    }

    if (!mysqli_query($conn, $sql_update)) {
        throw new Exception("Gagal mengembalikan stok barang.");
    }

    // 3. HAPUS DETAIL TRANSAKSI
    if (!mysqli_query($conn, "DELETE FROM DETAIL_STOK WHERE ID_STOK = '$id_stok'")) {
        throw new Exception("Gagal hapus detail.");
    }

    // 4. HAPUS HEADER TRANSAKSI
    if (!mysqli_query($conn, "DELETE FROM STOK_PERSEDIAAN WHERE ID_STOK = '$id_stok'")) {
        throw new Exception("Gagal hapus header.");
    }

    // SUKSES -> COMMIT
    mysqli_commit($koneksi);

    echo "<script>
        alert('✅ Transaksi berhasil dihapus.\\nStok barang telah dikembalikan ke posisi semula.');
        window.location = 'index.php';
    </script>";

} catch (Exception $e) {
    // ERROR -> ROLLBACK
    mysqli_rollback($koneksi);
    
    echo "<script>
        alert('❌ Gagal menghapus: " . $e->getMessage() . "');
        window.location = 'index.php';
    </script>";
}
?>
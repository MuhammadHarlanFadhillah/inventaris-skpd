<?php
// =======================================================
// HAPUS SEMUA TRANSAKSI
// =======================================================
include '../config/koneksi.php';

if (!isset($conn)) {
    die('Koneksi database tidak tersedia.');
}

mysqli_begin_transaction($conn);

try {
    // 1. Hapus detail dulu (FK aman) 
    // PERBAIKAN: Ganti 'DETAIL_STOK' jadi 'detail_stok' (huruf kecil)
    if (!mysqli_query($conn, "DELETE FROM detail_stok")) {
        throw new Exception(mysqli_error($conn));
    }

    // 2. Hapus header
    // PERBAIKAN: Ganti 'STOK_PERSEDIAAN' jadi 'stok_persediaan' (huruf kecil)
    if (!mysqli_query($conn, "DELETE FROM stok_persediaan")) {
        throw new Exception(mysqli_error($conn));
    }

    // 3. (OPSIONAL) Reset stok barang
    // Kalau mau diaktifkan, pastikan nama tabel 'barang' huruf kecil juga
    // mysqli_query($conn, "UPDATE barang SET stok_akhir = 0");

    mysqli_commit($conn);

    // ✅ Redirect wajib
    header("Location: index.php?msg=hapus_semua_sukses");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    // Gw tambahin error message biar ketahuan kalau gagal kenapa
    header("Location: index.php?msg=hapus_semua_gagal&error=" . urlencode($e->getMessage()));
    exit;
}
?>
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
    mysqli_query($conn, "DELETE FROM DETAIL_STOK");

    // 2. Hapus header
    mysqli_query($conn, "DELETE FROM STOK_PERSEDIAAN");

    // 3. (OPSIONAL) Reset stok barang jika ada kolom stok
    // mysqli_query($conn, "UPDATE BARANG SET STOK = 0");

    mysqli_commit($conn);

    // ✅ Redirect wajib
    header("Location: index.php?msg=hapus_semua_sukses");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?msg=hapus_semua_gagal");
    exit;
}

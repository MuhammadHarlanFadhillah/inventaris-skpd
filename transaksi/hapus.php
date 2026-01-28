<?php
// =======================================================
// HAPUS SATU TRANSAKSI
// =======================================================
include '../config/koneksi.php';

if (!isset($conn)) {
    die('Koneksi database tidak tersedia.');
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_stok = $_GET['id'];

// Gunakan transaksi agar aman
mysqli_begin_transaction($conn);

try {
    // 1. Ambil detail transaksi (untuk rollback stok jika perlu)
    $detail = mysqli_query($conn, "
        SELECT ID_BARANG, KUANTITAS_MASUK, KUANTITAS_KELUAR
        FROM DETAIL_STOK
        WHERE ID_STOK = '$id_stok'
    ");

    // 2. Hapus detail
    mysqli_query($conn, "DELETE FROM DETAIL_STOK WHERE ID_STOK = '$id_stok'");

    // 3. Hapus header
    mysqli_query($conn, "DELETE FROM STOK_PERSEDIAAN WHERE ID_STOK = '$id_stok'");

    mysqli_commit($conn);

    // ✅ WAJIB redirect
    header("Location: index.php?msg=hapus_sukses");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?msg=hapus_gagal");
    exit;
}

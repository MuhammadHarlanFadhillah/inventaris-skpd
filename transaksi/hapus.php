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

$id_stok = mysqli_real_escape_string($conn, $_GET['id']);

// Gunakan transaksi agar aman
mysqli_begin_transaction($conn);

try {
    // 1. Ambil detail transaksi (untuk rollback stok jika perlu nanti)
    // PERBAIKAN: Tabel & Kolom huruf kecil
    $detail = mysqli_query($conn, "
        SELECT id_barang, kuantitas_masuk, kuantitas_keluar
        FROM detail_stok
        WHERE id_stok = '$id_stok'
    ");

    if (!$detail) {
        throw new Exception("Gagal ambil detail: " . mysqli_error($conn));
    }

    // 2. Hapus detail
    // PERBAIKAN: Tabel 'detail_stok' & Kolom 'id_stok' huruf kecil
    if (!mysqli_query($conn, "DELETE FROM detail_stok WHERE id_stok = '$id_stok'")) {
        throw new Exception("Gagal hapus detail: " . mysqli_error($conn));
    }

    // 3. Hapus header
    // PERBAIKAN: Tabel 'stok_persediaan' & Kolom 'id_stok' huruf kecil
    if (!mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok = '$id_stok'")) {
        throw new Exception("Gagal hapus header: " . mysqli_error($conn));
    }

    mysqli_commit($conn);

    // ✅ WAJIB redirect
    header("Location: index.php?msg=hapus_sukses");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    // Tambahin pesan error biar tau kenapa gagal
    header("Location: index.php?msg=hapus_gagal&error=" . urlencode($e->getMessage()));
    exit;
}
?>
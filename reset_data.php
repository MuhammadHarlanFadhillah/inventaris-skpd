<?php
session_start();
// REVISI PATH
include 'config/koneksi.php';

// KEAMANAN: Cek apakah user sudah login? Jika belum, tendang ke login.
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit();
}

// Proses Reset
if(isset($_POST['reset_semua'])){
    // 1. Kosongkan tabel Detail & Header
    mysqli_query($conn, "TRUNCATE TABLE DETAIL_STOK");
    
    // Matikan Foreign Key check sebentar
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($conn, "TRUNCATE TABLE STOK_PERSEDIAAN");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    // 2. Reset Stok Barang jadi 0
    mysqli_query($conn, "UPDATE BARANG SET STOK_AKHIR = 0");

    echo "<script>alert('✅ Sistem berhasil di-reset! Semua transaksi dihapus, Stok kembali 0.'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Reset Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-danger d-flex align-items-center justify-content-center vh-100">

    <div class="card text-center p-5 rounded-4 shadow-lg" style="max-width: 500px;">
        <h1 style="font-size: 50px;">⚠️</h1>
        <h2 class="fw-bold text-danger">ZONA BAHAYA</h2>
        <p class="mb-4">
            Anda akan menghapus <b>SEMUA RIWAYAT TRANSAKSI</b>.<br>
            Data Barang tidak dihapus, tapi <b>Stok akan di-reset menjadi 0</b>.
        </p>
        
        <form method="POST">
            <button type="submit" name="reset_semua" class="btn btn-danger btn-lg w-100 mb-2" onclick="return confirm('Yakin 100%? Data tidak bisa kembali!')">
                YA, RESET SEMUA SEKARANG
            </button>
            <a href="index.php" class="btn btn-light btn-lg w-100 border">Batal / Kembali</a>
        </form>
    </div>

</body>
</html>
<?php 
// 1. BUFFERING (Wajib di Railway biar gak crash header)
ob_start();

// 2. ERROR REPORTING (Biar tau kalau ada error kodingan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/koneksi.php';

// ==========================================
// LOGIKA SIMPAN
// ==========================================
if (isset($_POST['simpan'])) {
    // Tangkap Data
    $id_brg = $_POST['id_barang'];
    $tgl    = $_POST['tanggal'];
    $jenis  = $_POST['jenis'];
    $jml    = (int) $_POST['jumlah'];
    $harga  = str_replace('.', '', $_POST['harga']); // Hapus titik rupiah
    
    // Generate ID Unik
    $id_stok = "TRX-" . time(); 
    $id_skpd = "SKPD-01"; // Default ID SKPD

    // A. INSERT KE HEADER (stok_persediaan)
    // PENTING: Pastikan nama tabel HURUF KECIL
    $q_header = "INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode) VALUES ('$id_stok', '$id_skpd', '$tgl')";
    
    // Cek Error Header
    if (!mysqli_query($conn, $q_header)) {
        die("<h3>FATAL ERROR HEADER:</h3> " . mysqli_error($conn));
    }

    // B. INSERT KE DETAIL (detail_stok)
    $qin  = ($jenis == 'MASUK') ? $jml : 0;
    $qout = ($jenis == 'KELUAR') ? $jml : 0;
    
    $q_detail = "INSERT INTO detail_stok (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar) 
                 VALUES ('$id_stok', '$id_brg', '$harga', '$qin', '$qout')";
    
    // Cek Error Detail
    if (!mysqli_query($conn, $q_detail)) {
        // Kalau detail gagal, hapus header biar bersih
        mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok='$id_stok'");
        die("<h3>FATAL ERROR DETAIL:</h3> " . mysqli_error($conn));
    }

    // C. UPDATE STOK BARANG (Manual PHP)
    // Kita gak ngandelin Trigger database biar gak error
    if ($jenis == 'MASUK') {
        $q_update = "UPDATE barang SET stok_akhir = stok_akhir + $jml WHERE id_barang = '$id_brg'";
    } else {
        $q_update = "UPDATE barang SET stok_akhir = stok_akhir - $jml WHERE id_barang = '$id_brg'";
    }
    
    if (!mysqli_query($conn, $q_update)) {
        echo "<script>alert('Warning: Transaksi tersimpan tapi Update Stok Gagal.');</script>";
    }

    // D. SUKSES & REDIRECT
    // Pake Javascript window.location biar GAK ERROR 500
    echo "<script>
        alert('✅ Transaksi Berhasil Disimpan!'); 
        window.location.href = 'index.php';
    </script>";
    exit;
}
?>

<?php include '../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white py-3"><h5 class="mb-0">Transaksi Baru</h5></div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label>Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">Pilih...</option>
                            <?php
                            // Ambil data barang (Huruf Kecil)
                            $q = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
                            while ($r = mysqli_fetch_assoc($q)) {
                                // Paksa baca lowercase
                                $id = $r['id_barang'] ?? $r['ID_BARANG'];
                                $nm = $r['nama_barang'] ?? $r['NAMA_BARANG'];
                                $st = $r['stok_akhir'] ?? $r['STOK_AKHIR'];
                                echo "<option value='$id'>$nm (Sisa: $st)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><label>Tanggal</label><input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="form-control"></div>
                        <div class="col"><label>Harga</label><input type="number" name="harga" class="form-control" placeholder="0"></div>
                    </div>
                    <div class="mb-3">
                        <label>Jenis</label><br>
                        <input type="radio" name="jenis" value="MASUK" checked> Masuk
                        <input type="radio" name="jenis" value="KELUAR" class="ms-3"> Keluar
                    </div>
                    <div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div>
                    <button type="submit" name="simpan" class="btn btn-primary w-100 rounded-pill">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ob_end_flush(); ?>
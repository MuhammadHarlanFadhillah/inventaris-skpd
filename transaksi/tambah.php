<?php 
// ===============================================================
// 1. MODE DEBUG (WAJIB NYALA BIAR GAK BLANK 500)
// ===============================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
include '../config/koneksi.php';

// Cek Koneksi
if (!$conn) {
    die("<h3>Koneksi Database Gagal: " . mysqli_connect_error() . "</h3>");
}

// ===============================================================
// LOGIKA PENYIMPANAN
// ===============================================================
if (isset($_POST['simpan_transaksi'])) {

    // Tangkap Input
    $id_barang = trim($_POST['id_barang']);
    $tanggal   = $_POST['tanggal'];
    $jenis     = $_POST['jenis'];
    $jumlah    = (int) $_POST['jumlah'];
    $harga_fix = str_replace('.', '', $_POST['harga']);

    // Validasi sederhana
    if(empty($id_barang) || empty($jumlah)) {
        die("Error: Barang atau Jumlah tidak boleh kosong.");
    }

    // Generate ID
    $id_stok = "TRX-" . time() . rand(100, 999);
    
    // PERHATIAN: ID SKPD SEMENTARA KITA SET NULL DULU
    // (Supaya kalau tabel referensi SKPD belum ada, tidak error constraint)
    $id_skpd = "SKPD-01"; 

    // Cek Stok (Khusus Keluar)
    if ($jenis == 'KELUAR') {
        $q_cek = "SELECT stok_akhir, nama_barang FROM barang WHERE id_barang = '$id_barang'";
        $cek = mysqli_query($conn, $q_cek);
        
        if (!$cek) { die("Error Cek Stok: " . mysqli_error($conn)); }
        
        $d = mysqli_fetch_assoc($cek);
        if (!$d) { die("Error: Data barang tidak ditemukan di database."); }

        $stok_db = $d['stok_akhir'] ?? 0;
        
        if ($stok_db < $jumlah) {
            echo "<script>alert('⛔ Stok tidak cukup! Sisa: $stok_db'); window.history.back();</script>";
            exit;
        }
    }

    // -----------------------------------------------------------
    // EKSEKUSI INSERT (DENGAN JEBAKAN ERROR)
    // -----------------------------------------------------------

    // 1. INSERT HEADER
    // Gw coba insert TANPA id_skpd dulu kalau error, 
    // tapi karena struktur lu ada id_skpd, kita coba pakai try-catch manual ala PHP Native
    $q_header = "INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode) VALUES ('$id_stok', '$id_skpd', '$tanggal')";
    
    if (!mysqli_query($conn, $q_header)) {
        // JIKA GAGAL DISINI, TAMPILKAN ERRORNYA
        die("<div style='background:red; color:white; padding:20px;'>
                <h3>GAGAL INSERT HEADER (stok_persediaan)</h3>
                <p><strong>Error SQL:</strong> " . mysqli_error($conn) . "</p>
                <p><strong>Query:</strong> $q_header</p>
                <p><em>Kemungkinan penyebab: Kolom 'id_skpd' tidak boleh sembarang isi, atau nama kolom salah.</em></p>
             </div>");
    }

    // 2. INSERT DETAIL
    $q_masuk  = ($jenis == 'MASUK')  ? $jumlah : 0;
    $q_keluar = ($jenis == 'KELUAR') ? $jumlah : 0;

    $q_detail = "INSERT INTO detail_stok (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar) 
                 VALUES ('$id_stok', '$id_barang', '$harga_fix', '$q_masuk', '$q_keluar')";

    if (!mysqli_query($conn, $q_detail)) {
        // Hapus header biar bersih
        mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok='$id_stok'");
        
        die("<div style='background:red; color:white; padding:20px;'>
                <h3>GAGAL INSERT DETAIL (detail_stok)</h3>
                <p><strong>Error SQL:</strong> " . mysqli_error($conn) . "</p>
             </div>");
    }

    // 3. UPDATE STOK
    if ($jenis == 'MASUK') {
        $q_up = "UPDATE barang SET stok_akhir = stok_akhir + $jumlah WHERE id_barang = '$id_barang'";
    } else {
        $q_up = "UPDATE barang SET stok_akhir = stok_akhir - $jumlah WHERE id_barang = '$id_barang'";
    }
    
    if (!mysqli_query($conn, $q_up)) {
         die("Error Update Stok: " . mysqli_error($conn));
    }

    // 4. SUKSES
    echo "<script>
        alert('✅ BERHASIL! Transaksi tersimpan.');
        window.location.href = 'index.php';
    </script>";
    exit;
}
?>

<?php include '../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">Form Transaksi (Mode Debug)</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <form method="POST">
                    <div class="mb-3">
                        <label class="fw-bold">Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $q = mysqli_query($conn, "SELECT id_barang, nama_barang, stok_akhir, satuan FROM barang ORDER BY nama_barang ASC");
                            while ($r = mysqli_fetch_assoc($q)) {
                                $id = $r['id_barang'] ?? $r['ID_BARANG'];
                                $nm = $r['nama_barang'] ?? $r['NAMA_BARANG'];
                                $st = $r['stok_akhir'] ?? $r['STOK_AKHIR'];
                                echo "<option value='$id'>$nm (Sisa: $st)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold">Harga</label>
                            <input type="text" name="harga" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold d-block">Jenis</label>
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="jenis" id="m" value="MASUK" checked>
                            <label class="btn btn-outline-success" for="m">Masuk</label>
                            <input type="radio" class="btn-check" name="jenis" id="k" value="KELUAR">
                            <label class="btn btn-outline-danger" for="k">Keluar</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <button type="submit" name="simpan_transaksi" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ob_end_flush(); ?>
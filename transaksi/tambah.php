<?php 
// NYALAKAN ERROR REPORTING (SUPAYA GAK BLANK PUTIH)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/koneksi.php';
include '../layout/header.php'; 

// ===============================================================
// LOGIKA PENYIMPANAN TRANSAKSI
// ===============================================================
if (isset($_POST['simpan_transaksi'])) {

    // 1. DATA INPUT
    $id_barang = trim($_POST['id_barang']);
    $tanggal   = $_POST['tanggal'];
    $jenis     = $_POST['jenis']; // MASUK / KELUAR
    $jumlah    = (int) $_POST['jumlah'];

    // Bersihkan format rupiah
    $harga_raw = $_POST['harga'];
    $harga_fix = str_replace('.', '', $harga_raw);

    // 2. GENERATE ID TRANSAKSI
    $id_stok = "TRX-" . time() . rand(100, 999);
    
    // Default SKPD (Sesuaikan kalau tabel SKPD lu kosong/beda)
    // Kalau error constraint, coba ganti NULL atau hapus kolom ini dari query
    $id_skpd = "SKPD-01"; 

    $error = false;

    // 3. VALIDASI STOK (KHUSUS BARANG KELUAR)
    if ($jenis == 'KELUAR') {
        $cek_stok = mysqli_query($conn, "SELECT stok_akhir, nama_barang FROM barang WHERE id_barang = '$id_barang'");
        
        if (!$cek_stok) { die("Error Cek Stok: " . mysqli_error($conn)); }
        
        $data_stok = mysqli_fetch_assoc($cek_stok);

        $stok_db = $data_stok['stok_akhir'] ?? $data_stok['STOK_AKHIR'];
        $nama_db = $data_stok['nama_barang'] ?? $data_stok['NAMA_BARANG'];

        if ($stok_db < $jumlah) {
            echo "<script>alert('⛔ TRANSAKSI DITOLAK! Stok $nama_db cuma sisa $stok_db.');</script>";
            $error = true;
        }
    }

    // 4. PROSES INSERT
    if (!$error) {

        // A. INSERT HEADER (TABEL stok_persediaan)
        // REVISI: Gw hapus kolom 'stok_sisa' disini, karena biasanya itu adanya di tabel barang, bukan di riwayat transaksi.
        // Kalau tabel lu butuh id_skpd, pastikan data 'SKPD-01' ada di tabel master SKPD.
        // Kalau masih error, hapus ", id_skpd" dan ", '$id_skpd'"
        $query_header = "
            INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode)
            VALUES ('$id_stok', '$id_skpd', '$tanggal')
        ";

        // DEBUG: Kalau gagal, langsung matikan program dan kasih tau errornya
        $exec_header = mysqli_query($conn, $query_header);
        if (!$exec_header) {
            die("<div class='alert alert-danger'>
                    <strong>Error Header:</strong> " . mysqli_error($conn) . "<br>
                    Coba cek apakah tabel 'stok_persediaan' punya kolom 'id_skpd'? 
                    Atau ID 'SKPD-01' sudah ada di tabel SKPD?
                 </div>");
        }

        // Tentukan kuantitas detail
        $q_masuk  = ($jenis == 'MASUK')  ? $jumlah : 0;
        $q_keluar = ($jenis == 'KELUAR') ? $jumlah : 0;

        // B. INSERT DETAIL (TABEL detail_stok)
        $query_detail = "
            INSERT INTO detail_stok 
                (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar)
            VALUES 
                ('$id_stok', '$id_barang', '$harga_fix', '$q_masuk', '$q_keluar')
        ";

        $exec_detail = mysqli_query($conn, $query_detail);

        if ($exec_detail) {
            
            // C. UPDATE STOK BARANG OTOMATIS
            if ($jenis == 'MASUK') {
                $q_update = "UPDATE barang SET stok_akhir = stok_akhir + $jumlah WHERE id_barang = '$id_barang'";
            } else {
                $q_update = "UPDATE barang SET stok_akhir = stok_akhir - $jumlah WHERE id_barang = '$id_barang'";
            }
            
            mysqli_query($conn, $q_update);

            echo "<script>
                alert('✅ Transaksi berhasil disimpan!');
                window.location = 'index.php';
            </script>";
        } else {
            // Hapus header kalau detail gagal biar gak nyampah
            mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok='$id_stok'");
            die("<div class='alert alert-danger'>Error Detail: " . mysqli_error($conn) . "</div>");
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Riwayat
            </a>
        </div>

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cart-plus me-2"></i>Form Transaksi</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang</label>
                        <select name="id_barang" class="form-select bg-light" required>
                            <option value="">-- Cari Barang --</option>
                            <?php
                            $b = mysqli_query($conn, "SELECT id_barang, nama_barang, stok_akhir FROM barang ORDER BY nama_barang ASC");
                            while ($row = mysqli_fetch_assoc($b)) {
                                $id   = $row['id_barang'] ?? $row['ID_BARANG'];
                                $nama = $row['nama_barang'] ?? $row['NAMA_BARANG'];
                                $stok = $row['stok_akhir'] ?? $row['STOK_AKHIR'];
                                echo "<option value='{$id}'>{$nama} (Sisa: {$stok})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="text" name="harga" id="rupiah" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Jenis Transaksi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="jenis" id="m" value="MASUK" checked>
                            <label class="btn btn-outline-success py-2" for="m">Barang Masuk</label>

                            <input type="radio" class="btn-check" name="jenis" id="k" value="KELUAR">
                            <label class="btn btn-outline-danger py-2" for="k">Barang Keluar</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="simpan_transaksi" class="btn btn-primary rounded-pill shadow">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script Format Rupiah
var rupiah = document.getElementById('rupiah');
rupiah.addEventListener('keyup', function(e){
    rupiah.value = formatRupiah(this.value);
});
function formatRupiah(angka){
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
    split   = number_string.split(','),
    sisa    = split[0].length % 3,
    rupiah  = split[0].substr(0, sisa),
    ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
    if(ribuan){
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
}
</script>

<?php include '../layout/footer.php'; ?>
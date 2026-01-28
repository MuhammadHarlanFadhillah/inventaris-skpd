<?php 
// 1. MULAI BUFFERING (PENTING: Biar gak error header/blank page)
ob_start();

// 2. INCLUDE KONEKSI
include '../config/koneksi.php';

// ===============================================================
// LOGIKA PENYIMPANAN DATA (DITARUH DI PALING ATAS)
// ===============================================================
if (isset($_POST['simpan_transaksi'])) {

    // A. TANGKAP INPUTAN
    $id_barang = trim($_POST['id_barang']);
    $tanggal   = $_POST['tanggal'];
    $jenis     = $_POST['jenis']; // MASUK / KELUAR
    $jumlah    = (int) $_POST['jumlah'];
    
    // Bersihkan format rupiah (hapus titik)
    $harga_fix = str_replace('.', '', $_POST['harga']);

    // B. GENERATE ID OTOMATIS
    $id_stok = "TRX-" . time() . rand(100, 999);
    $id_skpd = "SKPD-01"; // Default ID SKPD

    $error = false;

    // C. VALIDASI STOK (KHUSUS BARANG KELUAR)
    // Query SELECT pakai huruf kecil semua
    if ($jenis == 'KELUAR') {
        $cek = mysqli_query($conn, "SELECT stok_akhir, nama_barang FROM barang WHERE id_barang = '$id_barang'");
        $d = mysqli_fetch_assoc($cek);
        
        // Ambil data (paksa huruf kecil)
        $stok_db = $d['stok_akhir'] ?? 0;
        $nama_db = $d['nama_barang'] ?? 'Barang';

        if ($stok_db < $jumlah) {
            echo "<script>alert('⛔ TRANSAKSI DITOLAK!\\n\\nStok $nama_db cuma sisa $stok_db.');</script>";
            $error = true;
        }
    }

    // D. EKSEKUSI SIMPAN
    if (!$error) {
        
        // 1. INSERT KE TABEL HEADER (stok_persediaan) -> HURUF KECIL
        $q_header = "INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode) 
                     VALUES ('$id_stok', '$id_skpd', '$tanggal')";
        
        if (mysqli_query($conn, $q_header)) {
            
            // Tentukan masuk/keluar
            $q_masuk  = ($jenis == 'MASUK')  ? $jumlah : 0;
            $q_keluar = ($jenis == 'KELUAR') ? $jumlah : 0;

            // 2. INSERT KE TABEL DETAIL (detail_stok) -> HURUF KECIL
            $q_detail = "INSERT INTO detail_stok (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar) 
                         VALUES ('$id_stok', '$id_barang', '$harga_fix', '$q_masuk', '$q_keluar')";

            if (mysqli_query($conn, $q_detail)) {
                
                // 3. UPDATE STOK BARANG OTOMATIS -> HURUF KECIL
                if ($jenis == 'MASUK') {
                    $q_update = "UPDATE barang SET stok_akhir = stok_akhir + $jumlah WHERE id_barang = '$id_barang'";
                } else {
                    $q_update = "UPDATE barang SET stok_akhir = stok_akhir - $jumlah WHERE id_barang = '$id_barang'";
                }
                mysqli_query($conn, $q_update);

                // 4. SUKSES & REDIRECT (Pakai JS biar gak Blank)
                echo "<script>
                    alert('✅ Transaksi berhasil disimpan!');
                    window.location.href = 'index.php?status=sukses';
                </script>";
                exit; 

            } else {
                // Gagal Detail
                echo "<script>alert('Error Detail: " . mysqli_error($conn) . "');</script>";
                // Hapus header biar gak nyampah
                mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok='$id_stok'");
            }
        } else {
            // Gagal Header
            echo "<script>alert('Error Header: " . mysqli_error($conn) . "');</script>";
        }
    }
}
// AKHIR LOGIKA PHP
?>

<?php include '../layout/header.php'; ?>

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
                            // Query Barang (Huruf Kecil & Alias biar aman)
                            $q_brg = "SELECT id_barang, nama_barang, stok_akhir, satuan FROM barang ORDER BY nama_barang ASC";
                            $b = mysqli_query($conn, $q_brg);
                            
                            while ($row = mysqli_fetch_assoc($b)) {
                                // Ambil data aman (lowercase priority)
                                $id   = $row['id_barang'] ?? $row['ID_BARANG'];
                                $nama = $row['nama_barang'] ?? $row['NAMA_BARANG'];
                                $stok = $row['stok_akhir'] ?? $row['STOK_AKHIR'];
                                $sat  = $row['satuan'] ?? $row['SATUAN'];
                                
                                echo "<option value='{$id}'>{$nama} (Sisa: {$stok} {$sat})</option>";
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
                            <label class="form-label fw-bold">Harga Satuan (Rp)</label>
                            <input type="text" name="harga" id="rupiah" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Jenis Transaksi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="jenis" id="m" value="MASUK" checked>
                            <label class="btn btn-outline-success py-2" for="m">
                                <i class="fas fa-arrow-down me-2"></i>Barang Masuk
                            </label>

                            <input type="radio" class="btn-check" name="jenis" id="k" value="KELUAR">
                            <label class="btn btn-outline-danger py-2" for="k">
                                <i class="fas fa-arrow-up me-2"></i>Barang Keluar
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Jumlah Barang</label>
                        <input type="number" name="jumlah" class="form-control form-control-lg" min="1" placeholder="Masukkan jumlah..." required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="simpan_transaksi" class="btn btn-primary rounded-pill shadow btn-lg">
                            Simpan Transaksi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

<?php 
include '../layout/footer.php'; 
ob_end_flush(); // SELESAI BUFFER
?>
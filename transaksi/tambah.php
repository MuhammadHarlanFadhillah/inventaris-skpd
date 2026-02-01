
<?php 
include '../config/koneksi.php';
include '../layout/header.php'; 

// ===============================================================
// LOGIKA PENYIMPANAN TRANSAKSI (ACID COMPLIANT)
// ===============================================================
if (isset($_POST['simpan_transaksi'])) {

    // 1. SANITASI INPUT
    $id_barang = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $tanggal   = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jenis     = mysqli_real_escape_string($koneksi, $_POST['jenis']); // MASUK / KELUAR
    $jumlah    = (int) $_POST['jumlah'];

    // Bersihkan format rupiah (hapus titik)
    $harga_raw = $_POST['harga'];
    $harga_fix = str_replace('.', '', $harga_raw);

    // 2. GENERATE ID TRANSAKSI UNIK
    $id_stok = "TRX-" . time() . rand(100, 999);
    $id_skpd = "SKPD-01"; // Bisa disesuaikan nanti

    // 3. MULAI TRANSAKSI DATABASE
    // Kita matikan autocommit agar bisa di-Rollback jika error
    mysqli_begin_transaction($koneksi);

    try {
        // -----------------------------------------------------------
        // A. VALIDASI STOK (KHUSUS BARANG KELUAR)
        // -----------------------------------------------------------
        // Kita tetap perlu cek stok di awal untuk mencegah error negatif
        if ($jenis == 'KELUAR') {
            // Cek stok saat ini dulu (For Update mengunci baris agar tidak ditabrak user lain)
            $cek = mysqli_query($conn, "SELECT stok_akhir, nama_barang FROM barang WHERE id_barang = '$id_barang' FOR UPDATE");
            $data_barang = mysqli_fetch_assoc($cek);

            if ($data_barang['stok_akhir'] < $jumlah) {
                throw new Exception("⛔ Stok tidak cukup! (Sisa: {$data_barang['stok_akhir']}, Diminta: $jumlah)");
            }
        }

        // -----------------------------------------------------------
        // B. INSERT KE TABEL HEADER (stok_persediaan)
        // -----------------------------------------------------------
        $sql_header = "INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode, stok_sisa)
                       VALUES ('$id_stok', '$id_skpd', '$tanggal', '$jumlah')";
        
        if (!mysqli_query($conn, $sql_header)) {
            throw new Exception("Gagal simpan header: " . mysqli_error($koneksi));
        }

        // -----------------------------------------------------------
        // C. INSERT KE TABEL DETAIL (detail_stok)
        // -----------------------------------------------------------
        // CATATAN: Saat query ini berjalan, TRIGGER DATABASE akan OTOMATIS
        // mengupdate stok di tabel barang. Jadi PHP tidak perlu update manual lagi.
        
        $q_masuk  = ($jenis == 'MASUK')  ? $jumlah : 0;
        $q_keluar = ($jenis == 'KELUAR') ? $jumlah : 0;

        $sql_detail = "INSERT INTO detail_stok (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar)
                       VALUES ('$id_stok', '$id_barang', '$harga_fix', '$q_masuk', '$q_keluar')";

        if (!mysqli_query($conn, $sql_detail)) {
            throw new Exception("Gagal simpan detail: " . mysqli_error($koneksi));
        }

        // -----------------------------------------------------------
        // D. UPDATE STOK DI TABEL barang (DIHAPUS)
        // -----------------------------------------------------------
        // Bagian update manual dihapus karena sudah ditangani oleh Trigger MySQL
        // agar tidak terjadi perhitungan ganda (double counting).

        // JIKA SEMUA LANCAR -> COMMIT (SIMPAN PERMANEN)
        mysqli_commit($koneksi);
        
        echo "<script>
                alert('✅ Transaksi BERHASIL disimpan!\\nStok barang telah diperbarui otomatis oleh sistem.');
                window.location = 'index.php';
              </script>";

    } catch (Exception $e) {
        // JIKA ADA ERROR -> ROLLBACK (BATALKAN SEMUA)
        mysqli_rollback($koneksi);
        
        echo "<div class='alert alert-danger border-0 shadow-sm mt-3 alert-dismissible fade show'>
                <strong>Transaksi Gagal:</strong> " . $e->getMessage() . "
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">

        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Riwayat
            </a>
        </div>

        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cart-plus me-2"></i>Form Transaksi Barang
                </h5>
            </div>

            <div class="card-body p-4 bg-white">
                <form method="POST" autocomplete="off" id="formTransaksi" onsubmit="return disableButton()">

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">PILIH BARANG</label>
                        <select name="id_barang" class="form-select form-select-lg bg-light border-0 fw-bold" required>
                            <option value="">-- Cari Nama Barang --</option>
                            <?php
                            // Ambil data barang untuk dropdown
                            $b = mysqli_query($conn, "SELECT id_barang, nama_barang, satuan, stok_akhir FROM barang ORDER BY nama_barang ASC");
                            while ($row = mysqli_fetch_assoc($b)) {
                                echo "<option value='{$row['id_barang']}'>
                                        {$row['nama_barang']} (Sisa Stok: {$row['stok_akhir']} {$row['satuan']})
                                      </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-muted">TANGGAL TRANSAKSI</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-muted">HARGA SATUAN</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">Rp</span>
                                <input type="text" name="harga" id="rupiah"
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted d-block">JENIS TRANSAKSI</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="jenis" id="m" value="MASUK" checked>
                            <label class="btn btn-outline-success py-2 fw-bold" for="m">
                                <i class="fas fa-download me-2"></i>Barang Masuk
                            </label>

                            <input type="radio" class="btn-check" name="jenis" id="k" value="KELUAR">
                            <label class="btn btn-outline-danger py-2 fw-bold" for="k">
                                <i class="fas fa-upload me-2"></i>Barang Keluar
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">JUMLAH BARANG</label>
                        <input type="number" name="jumlah"
                               class="form-control form-control-lg fw-bold"
                               placeholder="0" min="1" required>
                    </div>

                    <div class="d-grid pt-2">
                        <button type="submit" name="simpan_transaksi"
                                class="btn btn-primary btn-lg rounded-pill shadow fw-bold">
                            <i class="fas fa-save me-2"></i>PROSES TRANSAKSI
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Format Rupiah
var rupiah = document.getElementById('rupiah');
rupiah.addEventListener('keyup', function () {
    this.value = formatRupiah(this.value);
});

function formatRupiah(angka) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
}

// Mencegah Double Submit
function disableButton() {
    // Ambil tombol submit berdasarkan nama
    var btn = document.querySelector('button[name="simpan_transaksi"]');
    
    // Ubah tampilan tombol biar user tahu sedang proses
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    
    // Matikan tombol agar tidak bisa diklik lagi
    // Timeout kecil diberikan agar data POST sempat terkirim sebelum disabled
    setTimeout(function() {
        btn.disabled = true;
    }, 10);

    return true; // Lanjutkan submit form
}
</script>

<?php include '../layout/footer.php'; ?>
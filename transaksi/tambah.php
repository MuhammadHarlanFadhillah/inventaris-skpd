<?php 
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
    $id_skpd = "SKPD-01"; // default

    $error = false;

    // 3. VALIDASI STOK (KHUSUS BARANG KELUAR)
    if ($jenis == 'KELUAR') {

        $cek_stok = mysqli_query(
            $conn,
            "SELECT STOK_AKHIR, NAMA_BARANG 
             FROM BARANG 
             WHERE ID_BARANG = '$id_barang'"
        );

        $data_stok = mysqli_fetch_assoc($cek_stok);

        if ($data_stok['STOK_AKHIR'] < $jumlah) {
            echo "<script>
                alert('⛔ TRANSAKSI DITOLAK!\\n\\nBarang: {$data_stok['NAMA_BARANG']}\\nSisa Stok: {$data_stok['STOK_AKHIR']}\\nPermintaan: $jumlah');
            </script>";
            $error = true;
        }
    }

    // 4. PROSES INSERT
    if (!$error) {

        // A. INSERT HEADER
        $query_header = "
            INSERT INTO STOK_PERSEDIAAN (ID_STOK, ID_SKPD, TGL_PERIODE, STOK_SISA)
            VALUES ('$id_stok', '$id_skpd', '$tanggal', '$jumlah')
        ";

        $exec_header = mysqli_query($conn, $query_header);

        if ($exec_header) {

            // Tentukan kuantitas
            $q_masuk  = ($jenis == 'MASUK')  ? $jumlah : 0;
            $q_keluar = ($jenis == 'KELUAR') ? $jumlah : 0;

            // B. INSERT DETAIL
            $query_detail = "
                INSERT INTO DETAIL_STOK 
                    (ID_STOK, ID_BARANG, HARGA_SATUAN, KUANTITAS_MASUK, KUANTITAS_KELUAR)
                VALUES 
                    ('$id_stok', '$id_barang', '$harga_fix', '$q_masuk', '$q_keluar')
            ";

            if (mysqli_query($conn, $query_detail)) {
                echo "<script>
                    alert('✅ Transaksi berhasil disimpan!\\nStok otomatis diperbarui.');
                    window.location = 'index.php';
                </script>";
            } else {
                echo "<div class='alert alert-danger'>Gagal simpan detail: " . mysqli_error($conn) . "</div>";
            }

        } else {
            echo "<div class='alert alert-danger'>Gagal simpan header: " . mysqli_error($conn) . "</div>";
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
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cart-plus me-2"></i>Form Transaksi Barang
                </h5>
            </div>

            <div class="card-body p-4 bg-white">
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang</label>
                        <select name="id_barang" class="form-select form-select-lg bg-light" required>
                            <option value="">-- Cari Barang --</option>
                            <?php
                            $b = mysqli_query(
                                $conn,
                                "SELECT ID_BARANG, NAMA_BARANG, SATUAN, STOK_AKHIR 
                                 FROM BARANG 
                                 ORDER BY NAMA_BARANG ASC"
                            );

                            while ($row = mysqli_fetch_assoc($b)) {
                                echo "<option value='{$row['ID_BARANG']}'>
                                        {$row['NAMA_BARANG']} 
                                        (Sisa: {$row['STOK_AKHIR']} {$row['SATUAN']})
                                      </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Harga Satuan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="harga" id="rupiah"
                                       class="form-control" placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Jenis Transaksi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="jenis" id="m"
                                   value="MASUK" checked>
                            <label class="btn btn-outline-success py-2" for="m">
                                <i class="fas fa-download me-2"></i>Barang Masuk
                            </label>

                            <input type="radio" class="btn-check" name="jenis" id="k"
                                   value="KELUAR">
                            <label class="btn btn-outline-danger py-2" for="k">
                                <i class="fas fa-upload me-2"></i>Barang Keluar
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Jumlah Barang</label>
                        <input type="number" name="jumlah"
                               class="form-control form-control-lg"
                               min="1" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="simpan_transaksi"
                                class="btn btn-primary btn-lg rounded-pill shadow">
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php include '../layout/footer.php'; ?>

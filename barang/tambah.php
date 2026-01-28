<?php
include '../config/koneksi.php';
include '../layout/header.php';

// ================================
// PROSES SIMPAN DATA
// ================================
if (isset($_POST['simpan'])) {

    $id     = trim(htmlspecialchars($_POST['id']));
    $nama   = trim(htmlspecialchars($_POST['nama']));
    $satuan = trim(htmlspecialchars($_POST['satuan']));
    $spek   = trim(htmlspecialchars($_POST['spek']));

    // CEK DUPLIKAT ID BARANG
    $cek = mysqli_query($conn, "SELECT ID_BARANG FROM BARANG WHERE ID_BARANG = '$id'");
    if (mysqli_num_rows($cek) > 0) {

        echo "<script>alert('❌ Gagal! ID Barang sudah digunakan.');</script>";

    } else {

        // PANGGIL STORED PROCEDURE
        $sql  = "CALL tambah_barang('$id', '$nama', '$satuan', '$spek')";
        $exec = mysqli_query($conn, $sql);

        if ($exec) {

            // WAJIB: bersihkan result set setelah CALL
            while (mysqli_more_results($conn)) {
                mysqli_next_result($conn);
            }

            echo "<script>
                alert('✅ Data barang berhasil ditambahkan!');
                window.location = 'index.php';
            </script>";

        } else {

            echo "<div class='alert alert-danger mt-3'>
                    <strong>Error:</strong> " . mysqli_error($conn) . "
                  </div>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cube me-2"></i>Form Input Barang
                </h5>
            </div>

            <div class="card-body p-4 bg-white">
                <form method="POST">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">ID Barang (Kode Unik)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-barcode"></i>
                            </span>
                            <input type="text" name="id" class="form-control"
                                   placeholder="Contoh: B001" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Nama Barang</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">Satuan</label>
                            <input type="text" name="satuan" class="form-control"
                                   placeholder="Unit / Pcs" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">Spesifikasi</label>
                            <input type="text" name="spek" class="form-control"
                                   placeholder="Opsional">
                        </div>
                    </div>

                    <button type="submit" name="simpan"
                            class="btn btn-primary w-100 rounded-pill fw-bold">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

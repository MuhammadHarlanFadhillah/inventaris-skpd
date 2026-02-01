<?php
include '../config/koneksi.php';
include '../layout/header.php';

// ================================
// 1. AMBIL ID BARANG DARI URL
// ================================
if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

// Amankan ID dari URL
$id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data barang berdasarkan ID
$query_ambil = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
$data = mysqli_fetch_assoc($query_ambil);

// Jika data tidak ditemukan (misal ID asal-asalan)
if (!$data) {
    echo "<script>
        alert('❌ Data barang tidak ditemukan!');
        window.location = 'index.php';
    </script>";
    exit;
}

// ================================
// 2. PROSES UPDATE DATA
// ================================
if (isset($_POST['update'])) {

    // Sanitasi Input (Wajib pakai $koneksi)
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $spek   = mysqli_real_escape_string($koneksi, $_POST['spek']);

    // Query Update
    $query_update = "UPDATE barang SET 
                        nama_barang = '$nama', 
                        satuan      = '$satuan', 
                        spesifikasi = '$spek' 
                     WHERE id_barang = '$id_barang'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>
            alert('✅ Data barang berhasil diperbarui!');
            window.location = 'index.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger mt-3 alert-dismissible fade show'>
                <strong>Gagal Update:</strong> " . mysqli_error($koneksi) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">

        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-edit me-2"></i>Edit Data Barang
                </h5>
            </div>

            <div class="card-body p-4 bg-white">
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">ID BARANG (Tidak bisa diubah)</label>
                        <input type="text"
                               value="<?= htmlspecialchars($data['id_barang']); ?>"
                               class="form-control bg-light fw-bold text-dark"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">NAMA BARANG</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($data['nama_barang']); ?>"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark small">SATUAN</label>
                            <input type="text" name="satuan" class="form-control"
                                   value="<?= htmlspecialchars($data['satuan']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark small">SPESIFIKASI</label>
                            <input type="text" name="spek" class="form-control"
                                   value="<?= htmlspecialchars($data['SPESIFIKASI']); ?>">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="update"
                                class="btn btn-warning py-2 rounded-pill fw-bold text-dark shadow-sm">
                            <i class="fas fa-save me-2"></i>SIMPAN PERUBAHAN
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
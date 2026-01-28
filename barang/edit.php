<?php
include '../config/koneksi.php';
include '../layout/header.php';

// ================================
// AMBIL ID BARANG
// ================================
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_barang = trim($_GET['id']);

// Ambil data barang
$query_ambil = mysqli_query(
    $conn,
    "SELECT * FROM BARANG WHERE ID_BARANG = '$id_barang'"
);

$data = mysqli_fetch_assoc($query_ambil);

if (!$data) {
    echo "<script>
        alert('❌ Data barang tidak ditemukan!');
        window.location = 'index.php';
    </script>";
    exit;
}

// ================================
// PROSES UPDATE DATA
// ================================
if (isset($_POST['update'])) {

    $nama   = trim(htmlspecialchars($_POST['nama']));
    $satuan = trim(htmlspecialchars($_POST['satuan']));
    $spek   = trim(htmlspecialchars($_POST['spek']));

    $query_update = "
        UPDATE BARANG SET
            NAMA_BARANG = '$nama',
            SATUAN      = '$satuan',
            SPESIFIKASI = '$spek'
        WHERE ID_BARANG = '$id_barang'
    ";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>
            alert('✅ Data barang berhasil diperbarui!');
            window.location = 'index.php';
        </script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>
                <strong>Gagal Update:</strong> " . mysqli_error($conn) . "
              </div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-warning bg-gradient text-dark py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-edit me-2"></i>Edit Data Barang
                </h5>
            </div>

            <div class="card-body p-4">
                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold">ID Barang</label>
                        <input type="text"
                               value="<?= htmlspecialchars($data['ID_BARANG']); ?>"
                               class="form-control bg-light fw-bold text-muted"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= htmlspecialchars($data['NAMA_BARANG']); ?>"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control"
                                   value="<?= htmlspecialchars($data['SATUAN']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Spesifikasi</label>
                            <input type="text" name="spek" class="form-control"
                                   value="<?= htmlspecialchars($data['SPESIFIKASI']); ?>">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="index.php"
                           class="btn btn-outline-secondary px-4 rounded-pill">
                            Batal
                        </a>
                        <button type="submit" name="update"
                                class="btn btn-warning px-5 rounded-pill fw-bold">
                            Update Data
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

<?php
include '../config/koneksi.php';
include '../layout/header.php';
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary">
                <i class="fas fa-boxes me-2"></i>Master Data Barang
            </h3>
            <p class="text-muted small mb-0">
                Kelola daftar aset dan spesifikasi barang.
            </p>
        </div>
        <a href="tambah.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i>Tambah Barang
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="px-4 py-3 small fw-bold">ID</th>
                            <th class="px-4 py-3 small fw-bold">Nama Barang</th>
                            <th class="px-4 py-3 small fw-bold">Satuan</th>
                            <th class="px-4 py-3 small fw-bold">Spesifikasi</th>
                            <th class="px-4 py-3 small fw-bold text-center">Stok</th>
                            <th class="px-4 py-3 small fw-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM BARANG ORDER BY ID_BARANG DESC";
                        $query = mysqli_query($conn, $sql);

                        if ($query && mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td class="px-4 py-3 text-muted">
                                #<?= htmlspecialchars($data['ID_BARANG']); ?>
                            </td>

                            <td class="px-4 py-3 fw-bold text-dark">
                                <?= htmlspecialchars($data['NAMA_BARANG']); ?>
                            </td>

                            <td class="px-4 py-3">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">
                                    <?= htmlspecialchars($data['SATUAN']); ?>
                                </span>
                            </td>

                            <td class="px-4 py-3 small text-muted">
                                <?= htmlspecialchars($data['SPESIFIKASI']); ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <?php if ($data['STOK_AKHIR'] > 0): ?>
                                    <span class="fw-bold text-success">
                                        <?= $data['STOK_AKHIR']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Habis</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=<?= $data['ID_BARANG']; ?>" 
                                       class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $data['ID_BARANG']; ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Belum ada data barang.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

<?php
include '../config/koneksi.php';
include '../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-boxes me-2"></i>Master Data Barang
        </h1>
        <p class="text-muted mb-0">Kelola daftar aset dan spesifikasi barang.</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="tambah.php" class="btn btn-primary rounded-pill shadow-sm px-4">
            <i class="fas fa-plus me-2"></i>Tambah Barang
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="datatable">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">ID Barang</th>
                        <th class="py-3">Nama Barang</th>
                        <th class="py-3 text-center">Satuan</th>
                        <th class="py-3">Spesifikasi</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3 text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ==================================================
                    // PERBAIKAN 1: Query pakai huruf kecil (barang)
                    // ==================================================
                    $query = "SELECT * FROM barang ORDER BY nama_barang ASC";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {

                            // ==================================================
                            // PERBAIKAN 2: Ambil data pakai huruf kecil
                            // Saya kasih backup (??) biar kalau di laptop masih kebaca
                            // ==================================================
                            $id   = $row['id_barang'] ?? $row['ID_BARANG'];
                            $nama = $row['nama_barang'] ?? $row['NAMA_BARANG'];
                            $sat  = $row['satuan'] ?? $row['SATUAN'];
                            $spek = $row['spesifikasi'] ?? $row['SPESIFIKASI'];
                            $stok = $row['stok_akhir'] ?? $row['STOK_AKHIR'];

                            // Logika Warna Stok
                            if ($stok <= 0) {
                                $badge_stok = "<span class='badge bg-danger rounded-pill px-3'>Habis</span>";
                            } elseif ($stok < 10) {
                                $badge_stok = "<span class='badge bg-warning text-dark rounded-pill px-3'>$stok (Menipis)</span>";
                            } else {
                                $badge_stok = "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-3'>$stok</span>";
                            }
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-primary">
                            #<?= htmlspecialchars($id); ?>
                        </td>
                        <td class="fw-bold text-dark">
                            <?= htmlspecialchars($nama); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge border border-secondary text-secondary rounded-pill fw-normal">
                                <?= htmlspecialchars($sat); ?>
                            </span>
                        </td>
                        <td class="text-muted small">
                            <?= htmlspecialchars($spek); ?>
                        </td>
                        <td class="text-center fw-bold">
                            <?= $badge_stok; ?>
                        </td>
                        <td class="text-center pe-4">
                            <a href="edit.php?id=<?= $id; ?>" 
                               class="btn btn-sm btn-outline-warning rounded-pill me-1"
                               title="Edit Barang">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="hapus.php?id=<?= $id; ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill"
                               onclick="return confirm('⚠️ Yakin hapus barang <?= $nama; ?>?\nData yang dihapus tidak bisa dikembalikan.')"
                               title="Hapus Barang">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        // TAMPILAN JIKA KOSONG (Manual 6 kolom biar DataTables gak error)
                    ?>
                    <tr>
                        <td class="text-center">-</td>
                        <td class="text-center">Belum ada data barang.</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
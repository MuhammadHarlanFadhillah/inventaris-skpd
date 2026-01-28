<?php
// =======================================================
// KONEKSI & HEADER
// =======================================================
include '../config/koneksi.php';
include '../layout/header.php';

if (!isset($conn)) {
    die('Koneksi database tidak tersedia.');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-3 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-history me-2"></i>Riwayat Transaksi
        </h1>
        <p class="text-muted mb-0">Daftar transaksi barang masuk dan keluar.</p>
    </div>

    <!-- TOOLBAR -->
    <div class="btn-toolbar gap-2">
        <a href="tambah.php" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i>Transaksi Baru
        </a>

        <a href="hapus_semua.php"
           class="btn btn-danger shadow-sm rounded-pill px-4"
           onclick="return confirm(
               '⚠️ PERINGATAN!\n\nSemua transaksi akan dihapus.\nStok semua barang akan di-reset ke 0.\n\nYakin ingin melanjutkan?'
           )">
            <i class="fas fa-trash-alt me-2"></i>Hapus Semua
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="datatable">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3 ps-4">Tanggal</th>
                        <th class="py-3">Barang</th>
                        <th class="py-3 text-center">Jenis</th>
                        <th class="py-3 text-center">Jumlah</th>
                        <th class="py-3 text-end">Harga Satuan</th>
                        <th class="py-3 text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // =======================================================
                    // QUERY TRANSAKSI
                    // =======================================================
                    $query = "
                        SELECT 
                            h.ID_STOK,
                            h.TGL_PERIODE,
                            b.ID_BARANG,
                            b.NAMA_BARANG,
                            d.KUANTITAS_MASUK,
                            d.KUANTITAS_KELUAR,
                            d.HARGA_SATUAN
                        FROM STOK_PERSEDIAAN h
                        JOIN DETAIL_STOK d ON h.ID_STOK = d.ID_STOK
                        JOIN BARANG b ON d.ID_BARANG = b.ID_BARANG
                        ORDER BY h.TGL_PERIODE DESC, h.ID_STOK DESC
                    ";

                    $result = mysqli_query($conn, $query);

                    // =======================================================
                    // JIKA DATA KOSONG (6 TD - AMAN DATATABLES)
                    // =======================================================
                    if (!$result || mysqli_num_rows($result) == 0) {
                        echo "
                            <tr>
                                <td class='text-center text-muted'>-</td>
                                <td class='text-center text-muted'>Belum ada transaksi</td>
                                <td class='text-center text-muted'>-</td>
                                <td class='text-center text-muted'>-</td>
                                <td class='text-center text-muted'>-</td>
                                <td class='text-center text-muted'>-</td>
                            </tr>
                        ";
                    } else {

                        while ($row = mysqli_fetch_assoc($result)) {

                            if ($row['KUANTITAS_MASUK'] > 0) {
                                $jenis = "
                                    <span class='badge bg-success bg-opacity-10 text-success px-3 rounded-pill'>
                                        <i class='fas fa-arrow-down me-1'></i>Masuk
                                    </span>
                                ";
                                $qty = $row['KUANTITAS_MASUK'];
                                $warna_qty = "text-success";
                            } else {
                                $jenis = "
                                    <span class='badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill'>
                                        <i class='fas fa-arrow-up me-1'></i>Keluar
                                    </span>
                                ";
                                $qty = $row['KUANTITAS_KELUAR'];
                                $warna_qty = "text-danger";
                            }
                    ?>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <?= date('d M Y', strtotime($row['TGL_PERIODE'])); ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        <?= htmlspecialchars($row['NAMA_BARANG']); ?>
                                    </span><br>
                                    <small class="text-muted">
                                        Kode: <?= htmlspecialchars($row['ID_BARANG']); ?>
                                    </small>
                                </td>
                                <td class="text-center"><?= $jenis; ?></td>
                                <td class="text-center fw-bold <?= $warna_qty; ?>"><?= $qty; ?></td>
                                <td class="text-end fw-bold text-secondary">
                                    Rp <?= number_format($row['HARGA_SATUAN'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="hapus.php?id=<?= $row['ID_STOK']; ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Yakin hapus transaksi ini?\nStok akan otomatis dikembalikan!')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

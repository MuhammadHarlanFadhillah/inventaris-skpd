
<?php
// =======================================================
// KONEKSI & HEADER
// =======================================================
include '../config/koneksi.php';
include '../layout/header.php';

// Cek koneksi menggunakan variabel $koneksi
if (!isset($koneksi)) {
    die('Koneksi database tidak tersedia. Pastikan config/koneksi.php benar.');
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-primary">
                <i class="fas fa-exchange-alt me-2"></i>Riwayat Transaksi
            </h3>
            <p class="text-muted mb-0">Monitor arus keluar-masuk barang.</p>
        </div>

        <div class="btn-toolbar gap-2">
            <a href="tambah.php" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Transaksi Baru
            </a>

            <a href="hapus_semua.php"
               class="btn btn-outline-danger shadow-sm rounded-pill px-4"
               onclick="return confirm('⚠️ PERINGATAN KERAS!\n\nSemua riwayat transaksi akan dihapus permanen.\nStok barang akan menjadi tidak sinkron atau reset ke 0.\n\nAnda yakin?')">
                <i class="fas fa-trash-alt me-2"></i>Reset Data
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="datatable">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-4">Tanggal</th>
                            <th class="py-3">Barang</th>
                            <th class="py-3 text-center">Jenis</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-end">Harga Satuan</th>
                            <th class="py-3 text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // =======================================================
                        // QUERY TRANSAKSI (JOIN 3 TABEL)
                        // =======================================================
                        $query = "
                            SELECT 
                                h.ID_STOK,
                                h.TGL_PERIODE,
                                b.id_barang,
                                b.nama_barang,
                                b.satuan,
                                d.kuantitas_masuk,
                                d.kuantitas_keluar,
                                d.harga_satuan
                            FROM stok_persediaan h
                            JOIN detail_stok d ON h.id_stok = d.id_stok
                            JOIN barang b ON d.id_barang = b.id_barang
                            ORDER BY h.tgl_periode DESC, h.id_stok DESC
                        ";

                        $result = mysqli_query($conn, $query);

                        if (!$result || mysqli_num_rows($result) == 0) {
                            // TAMPILAN JIKA KOSONG
                            echo "<tr>
                                    <td colspan='6' class='text-center py-5 text-muted'>
                                        <img src='https://cdn-icons-png.flaticon.com/512/4076/4076432.png' width='60' class='mb-3 opacity-50'>
                                        <p>Belum ada riwayat transaksi.</p>
                                    </td>
                                  </tr>";
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                
                                // Logika Badge Masuk/Keluar
                                if ($row['KUANTITAS_MASUK'] > 0) {
                                    $badge = "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-3'><i class='fas fa-arrow-down me-1'></i>Masuk</span>";
                                    $qty = "+" . $row['KUANTITAS_MASUK'];
                                    $text_class = "text-success";
                                } else {
                                    $badge = "<span class='badge bg-danger bg-opacity-10 text-danger rounded-pill px-3'><i class='fas fa-arrow-up me-1'></i>Keluar</span>";
                                    $qty = "-" . $row['KUANTITAS_KELUAR'];
                                    $text_class = "text-danger";
                                }
                        ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-medium">
                                        <?= date('d M Y', strtotime($row['TGL_PERIODE'])); ?>
                                    </td>
                                    
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['NAMA_BARANG']); ?></div>
                                        <small class="text-muted" style="font-size: 0.8em;">
                                            ID: <?= htmlspecialchars($row['ID_BARANG']); ?>
                                        </small>
                                    </td>

                                    <td class="text-center"><?= $badge; ?></td>

                                    <td class="text-center fw-bold <?= $text_class; ?>">
                                        <?= $qty; ?> <small class="text-muted fw-normal"><?= $row['SATUAN']; ?></small>
                                    </td>

                                    <td class="text-end fw-medium text-secondary">
                                        Rp <?= number_format($row['HARGA_SATUAN'], 0, ',', '.'); ?>
                                    </td>

                                    <td class="text-center pe-4">
                                        <a href="hapus.php?id=<?= $row['ID_STOK']; ?>"
                                           class="btn btn-sm btn-light text-danger border"
                                           data-bs-toggle="tooltip" title="Hapus Riwayat"
                                           onclick="return confirm('⚠️ Yakin hapus transaksi ini?\n\nStok barang akan otomatis dikembalikan (Rollback).')">
                                            <i class="fas fa-trash-alt"></i>
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
</div>

<?php include '../layout/footer.php'; ?>
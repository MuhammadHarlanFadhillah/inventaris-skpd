<?php
// =======================================================
// KONEKSI & HEADER
// =======================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/koneksi.php';
include '../layout/header.php';

if (!isset($conn)) {
    die('<div class="alert alert-danger">Koneksi database tidak tersedia.</div>');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-3 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-history me-2"></i>Riwayat Transaksi
        </h1>
        <p class="text-muted mb-0">Daftar transaksi barang masuk dan keluar.</p>
    </div>

    <div class="btn-toolbar gap-2">
        <a href="tambah.php" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i>Transaksi Baru
        </a>

        <a href="hapus_semua.php"
           class="btn btn-danger shadow-sm rounded-pill px-4"
           onclick="return confirm('⚠️ PERINGATAN!\n\nSemua riwayat transaksi akan dihapus permanen.\nStok barang tidak akan dikembalikan.\n\nYakin ingin melanjutkan?')">
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
                    // QUERY (FULL LOWERCASE + ALIAS)
                    // =======================================================
                    $query = "
                        SELECT 
                            h.id_stok AS id_stok, 
                            h.tgl_periode AS tgl_periode,
                            b.id_barang AS id_barang, 
                            b.nama_barang AS nama_barang,
                            d.kuantitas_masuk AS kuantitas_masuk, 
                            d.kuantitas_keluar AS kuantitas_keluar, 
                            d.harga_satuan AS harga_satuan
                        FROM stok_persediaan h
                        JOIN detail_stok d ON h.id_stok = d.id_stok
                        JOIN barang b ON d.id_barang = b.id_barang
                        ORDER BY h.tgl_periode DESC, h.id_stok DESC
                    ";

                    $result = mysqli_query($conn, $query);

                    // =======================================================
                    // CEK ERROR & TAMPILKAN DATA
                    // =======================================================
                    if (!$result) {
                        // FIX: JANGAN PAKAI COLSPAN, TAPI 6 TD TERPISAH BIAR DATATABLES GAK ERROR
                        echo "<tr>";
                        echo "<td class='text-center text-danger'>Error</td>";
                        echo "<td class='text-danger'>Gagal Query: " . mysqli_error($conn) . "</td>";
                        echo "<td>-</td><td>-</td><td>-</td><td>-</td>";
                        echo "</tr>";
                    } 
                    elseif (mysqli_num_rows($result) == 0) {
                        // FIX: JANGAN PAKAI COLSPAN, TAPI 6 TD TERPISAH
                        echo "<tr>";
                        echo "<td class='text-center text-muted'>-</td>";
                        echo "<td class='text-muted text-center'>Belum ada transaksi yang tercatat.</td>";
                        echo "<td class='text-center text-muted'>-</td>";
                        echo "<td class='text-center text-muted'>-</td>";
                        echo "<td class='text-center text-muted'>-</td>";
                        echo "<td class='text-center text-muted'>-</td>";
                        echo "</tr>";
                    } 
                    else {
                        // Loop Data
                        while ($row = mysqli_fetch_assoc($result)) {
                            
                            // LOGIKA JENIS TRANSAKSI
                            if ($row['kuantitas_masuk'] > 0) {
                                $jenis = "<span class='badge bg-success bg-opacity-10 text-success px-3 rounded-pill'><i class='fas fa-arrow-down me-1'></i>Masuk</span>";
                                $qty = $row['kuantitas_masuk'];
                                $warna_qty = "text-success";
                            } else {
                                $jenis = "<span class='badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill'><i class='fas fa-arrow-up me-1'></i>Keluar</span>";
                                $qty = $row['kuantitas_keluar'];
                                $warna_qty = "text-danger";
                            }
                    ?>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <?= date('d M Y', strtotime($row['tgl_periode'])); ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        <?= htmlspecialchars($row['nama_barang']); ?>
                                    </span><br>
                                    <small class="text-muted">
                                        Kode: <?= htmlspecialchars($row['id_barang']); ?>
                                    </small>
                                </td>
                                <td class="text-center"><?= $jenis; ?></td>
                                <td class="text-center fw-bold <?= $warna_qty; ?>">
                                    <?= $qty; ?>
                                </td>
                                <td class="text-end fw-bold text-secondary">
                                    Rp <?= number_format($row['harga_satuan'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="hapus.php?id=<?= $row['id_stok']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Yakin ingin menghapus riwayat transaksi ini?\nStok barang akan otomatis disesuaikan kembali.')">
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
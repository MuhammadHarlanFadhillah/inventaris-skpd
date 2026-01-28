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
                    // QUERY TRANSAKSI (FIXED: Lowercase Tables & Columns)
                    // =======================================================
                    $query = "
                        SELECT 
                            h.id_stok,
                            h.tgl_periode,
                            b.id_barang,
                            b.nama_barang,
                            d.kuantitas_masuk,
                            d.kuantitas_keluar,
                            d.harga_satuan
                        FROM stok_persediaan h
                        JOIN detail_stok d ON h.id_stok = d.id_stok
                        JOIN barang b ON d.id_barang = b.id_barang
                        ORDER BY h.tgl_periode DESC, h.id_stok DESC
                    ";

                    $result = mysqli_query($conn, $query);

                    // =======================================================
                    // JIKA DATA KOSONG
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

                            // FIX: Access array keys using lowercase or fallback to uppercase
                            $qty_masuk  = $row['kuantitas_masuk'] ?? $row['KUANTITAS_MASUK'];
                            $qty_keluar = $row['kuantitas_keluar'] ?? $row['KUANTITAS_KELUAR'];
                            $tgl        = $row['tgl_periode'] ?? $row['TGL_PERIODE'];
                            $nama_brg   = $row['nama_barang'] ?? $row['NAMA_BARANG'];
                            $id_brg     = $row['id_barang'] ?? $row['ID_BARANG'];
                            $harga      = $row['harga_satuan'] ?? $row['HARGA_SATUAN'];
                            $id_stok    = $row['id_stok'] ?? $row['ID_STOK'];

                            if ($qty_masuk > 0) {
                                $jenis = "
                                    <span class='badge bg-success bg-opacity-10 text-success px-3 rounded-pill'>
                                        <i class='fas fa-arrow-down me-1'></i>Masuk
                                    </span>
                                ";
                                $qty = $qty_masuk;
                                $warna_qty = "text-success";
                            } else {
                                $jenis = "
                                    <span class='badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill'>
                                        <i class='fas fa-arrow-up me-1'></i>Keluar
                                    </span>
                                ";
                                $qty = $qty_keluar;
                                $warna_qty = "text-danger";
                            }
                    ?>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <?= date('d M Y', strtotime($tgl)); ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        <?= htmlspecialchars($nama_brg); ?>
                                    </span><br>
                                    <small class="text-muted">
                                        Kode: <?= htmlspecialchars($id_brg); ?>
                                    </small>
                                </td>
                                <td class="text-center"><?= $jenis; ?></td>
                                <td class="text-center fw-bold <?= $warna_qty; ?>"><?= $qty; ?></td>
                                <td class="text-end fw-bold text-secondary">
                                    Rp <?= number_format($harga, 0, ',', '.'); ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="hapus.php?id=<?= $id_stok; ?>"
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
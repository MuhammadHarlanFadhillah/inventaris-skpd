<?php
include '../config/koneksi.php';
include '../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-3 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-history me-2"></i>Riwayat Transaksi
        </h1>
        <p class="text-muted mb-0">Daftar transaksi barang masuk dan keluar.</p>
    </div>
    <div class="btn-toolbar gap-2">
        <a href="tambah.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i>Baru
        </a>
        <a href="hapus_semua.php" class="btn btn-danger rounded-pill px-4 shadow-sm" onclick="return confirm('⚠️ Hapus SEMUA riwayat transaksi?')">
            <i class="fas fa-trash-alt me-2"></i>Reset
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="datatable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th class="py-3">Barang</th>
                        <th class="text-center py-3">Jenis</th>
                        <th class="text-center py-3">Jumlah</th>
                        <th class="text-end py-3">Harga</th>
                        <th class="text-center py-3 pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // QUERY KHUSUS TRANSAKSI (JOIN 3 TABEL)
                    // Menggunakan ALIAS (AS ...) agar terbaca di Railway (Linux)
                    $q = "SELECT 
                            h.id_stok AS id_stok, 
                            h.tgl_periode AS tgl,
                            b.nama_barang AS nama, 
                            b.id_barang AS id_brg,
                            d.kuantitas_masuk AS q_in, 
                            d.kuantitas_keluar AS q_out, 
                            d.harga_satuan AS harga
                          FROM stok_persediaan h
                          JOIN detail_stok d ON h.id_stok = d.id_stok
                          JOIN barang b ON d.id_barang = b.id_barang
                          ORDER BY h.tgl_periode DESC, h.id_stok DESC";
                    
                    $res = mysqli_query($conn, $q);

                    if ($res && mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            // TENTUKAN JENIS (MASUK / KELUAR)
                            if ($row['q_in'] > 0) {
                                $jenis = "<span class='badge bg-success bg-opacity-10 text-success px-3 rounded-pill'>Masuk</span>";
                                $qty   = $row['q_in'];
                                $cls   = "text-success fw-bold";
                            } else {
                                $jenis = "<span class='badge bg-danger bg-opacity-10 text-danger px-3 rounded-pill'>Keluar</span>";
                                $qty   = $row['q_out'];
                                $cls   = "text-danger fw-bold";
                            }
                    ?>
                    <tr>
                        <td class="ps-4 text-muted">
                            <?= date('d/m/Y', strtotime($row['tgl'])) ?>
                        </td>
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></span><br>
                            <small class="text-muted">Kode: <?= htmlspecialchars($row['id_brg']) ?></small>
                        </td>
                        <td class="text-center"><?= $jenis ?></td>
                        <td class="text-center <?= $cls ?>"><?= $qty ?></td>
                        <td class="text-end fw-bold text-secondary">
                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </td>
                        <td class="text-center pe-4">
                            <a href="hapus.php?id=<?= $row['id_stok'] ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill" 
                               onclick="return confirm('Yakin hapus transaksi ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                        // JIKA KOSONG: Tampilkan 6 kolom manual (Biar DataTables Gak Error)
                    ?>
                    <tr>
                        <td class="text-center">-</td>
                        <td class="text-center">Belum ada data</td>
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
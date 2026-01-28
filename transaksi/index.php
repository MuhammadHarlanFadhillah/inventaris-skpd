<?php
include '../config/koneksi.php';
include '../layout/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-3 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold"><i class="fas fa-history me-2"></i>Riwayat Transaksi</h1>
    </div>
    <div class="btn-toolbar gap-2">
        <a href="tambah.php" class="btn btn-primary rounded-pill px-4"><i class="fas fa-plus-circle me-2"></i>Baru</a>
        <a href="hapus_semua.php" class="btn btn-danger rounded-pill px-4" onclick="return confirm('Hapus semua?')"><i class="fas fa-trash-alt me-2"></i>Reset</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="datatable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Barang</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // QUERY (Alias Huruf Kecil biar aman terbaca)
                    $q = "SELECT 
                            h.id_stok AS id_stok, h.tgl_periode AS tgl_periode,
                            b.nama_barang AS nama_barang, b.id_barang AS id_barang,
                            d.kuantitas_masuk AS q_in, d.kuantitas_keluar AS q_out, d.harga_satuan AS harga
                          FROM stok_persediaan h
                          JOIN detail_stok d ON h.id_stok = d.id_stok
                          JOIN barang b ON d.id_barang = b.id_barang
                          ORDER BY h.tgl_periode DESC";
                    
                    $res = mysqli_query($conn, $q);

                    if ($res && mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            // Logic Jenis
                            if ($row['q_in'] > 0) {
                                $jenis = "<span class='badge bg-success bg-opacity-10 text-success'>Masuk</span>";
                                $qty = $row['q_in'];
                                $cls = "text-success";
                            } else {
                                $jenis = "<span class='badge bg-danger bg-opacity-10 text-danger'>Keluar</span>";
                                $qty = $row['q_out'];
                                $cls = "text-danger";
                            }
                    ?>
                    <tr>
                        <td class="ps-4"><?= date('d/m/Y', strtotime($row['tgl_periode'])) ?></td>
                        <td>
                            <b><?= htmlspecialchars($row['nama_barang']) ?></b><br>
                            <small class="text-muted"><?= $row['id_barang'] ?></small>
                        </td>
                        <td class="text-center"><?= $jenis ?></td>
                        <td class="text-center fw-bold <?= $cls ?>"><?= $qty ?></td>
                        <td class="text-end">Rp <?= number_format($row['harga'],0,',','.') ?></td>
                        <td class="text-center pe-4">
                            <a href="hapus.php?id=<?= $row['id_stok'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                        // SOLUSI POPUP ERROR:
                        // Jangan pakai colspan="6", tapi buat 6 td manual.
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
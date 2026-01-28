<?php
include '../config/koneksi.php';
include '../layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-3 mb-3 border-bottom">
    <h1 class="h2 text-primary fw-bold">Riwayat Transaksi</h1>
    <div class="btn-toolbar gap-2">
        <a href="tambah.php" class="btn btn-primary rounded-pill px-4">Baru</a>
        <a href="hapus_semua.php" class="btn btn-danger rounded-pill px-4" onclick="return confirm('Reset semua?')">Reset</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="datatable">
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
                    // QUERY DENGAN ALIAS (SOLUSI DATA GAK MUNCUL)
                    // Kita paksa outputnya jadi 'id_stok', 'tgl', dll.
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

                    // Debugging kalau query error
                    if (!$res) {
                        echo "<tr><td colspan='6' class='text-danger text-center'>Query Error: ".mysqli_error($conn)."</td></tr>";
                    }
                    elseif (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            // TAMPILKAN DATA (Pake kunci ALIAS tadi)
                            $jenis = ($row['q_in'] > 0) ? "<span class='badge bg-success'>Masuk</span>" : "<span class='badge bg-danger'>Keluar</span>";
                            $qty   = ($row['q_in'] > 0) ? $row['q_in'] : $row['q_out'];
                    ?>
                    <tr>
                        <td class="ps-4"><?= date('d/m/Y', strtotime($row['tgl'])) ?></td>
                        <td>
                            <b><?= htmlspecialchars($row['nama']) ?></b><br>
                            <small class="text-muted"><?= $row['id_brg'] ?></small>
                        </td>
                        <td class="text-center"><?= $jenis ?></td>
                        <td class="text-center fw-bold"><?= $qty ?></td>
                        <td class="text-end"><?= number_format($row['harga']) ?></td>
                        <td class="text-center">
                            <a href="hapus.php?id=<?= $row['id_stok'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else { 
                        // TAMPILAN KOSONG (6 KOLOM MANUAL BIAR GAK POPUP ERROR)
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
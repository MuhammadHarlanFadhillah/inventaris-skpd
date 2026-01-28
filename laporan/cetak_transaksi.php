<?php
// PATH BENAR KE FILE KONEKSI
include '../config/koneksi.php';

// VALIDASI KONEKSI
if (!isset($conn)) {
    die('Koneksi database gagal.');
}

// VALIDASI INPUT
if (!isset($_GET['tgl_awal']) || !isset($_GET['tgl_akhir'])) {
    die('Periode tanggal tidak valid.');
}

$tgl_awal  = $_GET['tgl_awal'];
$tgl_akhir = $_GET['tgl_akhir'];
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Transaksi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>

<body class="p-5" onload="window.print()">

<!-- ==============================
     JUDUL LAPORAN
============================== -->
<div class="text-center mb-4">
    <h4 class="fw-bold text-uppercase">Laporan Riwayat Transaksi Barang</h4>
    <p class="mb-0">
        Periode:
        <strong><?= date('d-m-Y', strtotime($tgl_awal)); ?></strong>
        s/d
        <strong><?= date('d-m-Y', strtotime($tgl_akhir)); ?></strong>
    </p>
</div>

<hr class="border border-dark border-2 opacity-100 mb-4">

<!-- ==============================
     TABEL DATA
============================== -->
<table class="table table-bordered border-dark table-sm">
    <thead class="table-light border-dark text-center">
        <tr>
            <th width="5%">No</th>
            <th width="12%">Tanggal</th>
            <th width="18%">ID Transaksi</th>
            <th>Nama Barang</th>
            <th width="10%">Jenis</th>
            <th width="15%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;

        // ==============================
        // QUERY SESUAI STRUKTUR DATABASE
        // ==============================
        $query = "
            SELECT 
                s.TGL_PERIODE,
                s.ID_STOK,
                b.NAMA_BARANG,
                b.SATUAN,
                d.KUANTITAS_MASUK,
                d.KUANTITAS_KELUAR
            FROM DETAIL_STOK d
            JOIN STOK_PERSEDIAAN s ON d.ID_STOK = s.ID_STOK
            JOIN BARANG b ON d.ID_BARANG = b.ID_BARANG
            WHERE s.TGL_PERIODE BETWEEN '$tgl_awal' AND '$tgl_akhir'
            ORDER BY s.TGL_PERIODE ASC, s.ID_STOK ASC
        ";

        $exec = mysqli_query($conn, $query);

        if (!$exec || mysqli_num_rows($exec) == 0) {
            echo "
                <tr>
                    <td colspan='6' class='text-center fst-italic py-3'>
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            ";
        } else {

            while ($d = mysqli_fetch_assoc($exec)) {

                if ($d['KUANTITAS_MASUK'] > 0) {
                    $jenis = 'MASUK';
                    $qty   = $d['KUANTITAS_MASUK'];
                } else {
                    $jenis = 'KELUAR';
                    $qty   = $d['KUANTITAS_KELUAR'];
                }
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($d['TGL_PERIODE'])); ?></td>
            <td><?= $d['ID_STOK']; ?></td>
            <td><?= htmlspecialchars($d['NAMA_BARANG']); ?></td>
            <td class="text-center"><?= $jenis; ?></td>
            <td class="text-center"><?= $qty . ' ' . $d['SATUAN']; ?></td>
        </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>

<!-- ==============================
     TANDA TANGAN
============================== -->
<div class="row mt-5">
    <div class="col-4 offset-8 text-center">
        <p>
            Bandung, <?= date('d F Y'); ?>
        </p>
        <br><br><br>
        <p class="fw-bold text-decoration-underline mb-0">
            Petugas Gudang
        </p>
    </div>
</div>

<div class="no-print fixed-bottom p-3 text-center bg-light border-top">
    <button onclick="window.close()" class="btn btn-secondary rounded-pill px-4">
        Tutup Halaman
    </button>
</div>

</body>
</html>

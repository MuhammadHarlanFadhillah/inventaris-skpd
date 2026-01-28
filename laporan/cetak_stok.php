<?php
// =======================================================
// KONEKSI DATABASE (TANPA HEADER)
// =======================================================
include '../config/koneksi.php';

if (!isset($conn)) {
    die('Koneksi database tidak tersedia.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* ==============================
           STYLE KHUSUS CETAK
        ============================== */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }

        /* Garis kop surat */
        .garis-kop {
            border-bottom: 3px solid #000;
            margin-top: 10px;
            margin-bottom: 2px;
        }
        .garis-kop-2 {
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }
    </style>
</head>

<body class="bg-white text-dark p-5">

<div class="row align-items-center mb-3">
    <div class="col-2 text-center">
        <img src="../assets/img/gudang.png"
             width="90" alt="Logo Gudang" onerror="this.style.display='none'">
    </div>
    <div class="col-8 text-center">
        <h5 class="mb-0 fw-bold">PEMERINTAH KOTA BANDUNG</h5>
        <h4 class="mb-0 fw-bold">DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
        <p class="mb-0 small">Jl. Jendral Sudirman No. 123, Kota Bandung</p>
        <p class="mb-0 small">Telp: (022) 1234567 | Email: info@disdik.bandung.go.id</p>
    </div>
    <div class="col-2"></div>
</div>

<div class="garis-kop"></div>
<div class="garis-kop-2"></div>

<div class="text-center mb-4">
    <h5 class="fw-bold text-uppercase text-decoration-underline">
        Laporan Data Stok Barang
    </h5>
    <p class="small text-muted">
        Per Tanggal: <?= date('d F Y'); ?>
    </p>
</div>

<table class="table table-bordered border-dark table-sm">
    <thead class="table-light text-center border-dark">
        <tr>
            <th width="5%">No</th>
            <th width="15%">ID Barang</th>
            <th>Nama Barang</th>
            <th>Spesifikasi</th>
            <th width="15%">Stok Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        // PERBAIKAN: Query pakai huruf kecil semua
        $query = mysqli_query($conn, "
            SELECT 
                id_barang,
                nama_barang,
                spesifikasi,
                stok_akhir,
                satuan
            FROM barang
            ORDER BY nama_barang ASC
        ");

        if (!$query || mysqli_num_rows($query) == 0) {
            echo "
                <tr>
                    <td colspan='5' class='text-center text-muted'>
                        Data barang belum tersedia
                    </td>
                </tr>
            ";
        } else {
            while ($row = mysqli_fetch_assoc($query)) {
                // PERBAIKAN: Ambil data pakai key huruf kecil (dengan backup huruf besar)
                $id   = $row['id_barang'] ?? $row['ID_BARANG'];
                $nama = $row['nama_barang'] ?? $row['NAMA_BARANG'];
                $spek = $row['spesifikasi'] ?? $row['SPESIFIKASI'];
                $stok = $row['stok_akhir'] ?? $row['STOK_AKHIR'];
                $sat  = $row['satuan'] ?? $row['SATUAN'];
        ?>
        <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td class="text-center"><?= htmlspecialchars($id); ?></td>
            <td><?= htmlspecialchars($nama); ?></td>
            <td><?= htmlspecialchars($spek); ?></td>
            <td class="text-center fw-bold">
                <?= $stok; ?> <?= htmlspecialchars($sat); ?>
            </td>
        </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table>

<div class="row mt-5">
    <div class="col-4 offset-8 text-center">
        <p class="mb-5">
            Bandung, <?= date('d F Y'); ?><br>
            Kepala Dinas / Penanggung Jawab
        </p>

        <br><br>

        <p class="fw-bold text-decoration-underline mb-0">
            DR. H. NAMA PEJABAT, M.Pd
        </p>
        <p class="mb-0">
            NIP. 19800101 200001 1 001
        </p>
    </div>
</div>

<div class="fixed-bottom p-3 bg-light border-top no-print text-center">
    <button onclick="window.print()"
            class="btn btn-primary btn-lg rounded-pill px-5 shadow">
        <i class="fas fa-print me-2"></i>Cetak Laporan
    </button>

    <button onclick="window.close()"
            class="btn btn-secondary btn-lg rounded-pill px-5 ms-2">
        Tutup
    </button>
</div>

</body>
</html>
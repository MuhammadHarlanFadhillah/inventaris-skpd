<?php
// =======================================================
// CETAK RIWAYAT TRANSAKSI (Filter Tanggal)
// =======================================================
include '../config/koneksi.php';

// 1. Cek Koneksi
if (!isset($koneksi)) {
    die("Koneksi database bermasalah.");
}

// 2. Cek Input Tanggal
if (!isset($_GET['tgl_awal']) || !isset($_GET['tgl_akhir'])) {
    die("Periode tanggal belum dipilih. Silakan kembali ke menu laporan.");
}

// 3. Amankan Input
$tgl_awal  = mysqli_real_escape_string($koneksi, $_GET['tgl_awal']);
$tgl_akhir = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']);

// 4. Fungsi Tanggal Indo
function tgl_indo($tanggal){
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - <?= date('d-m-Y'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS KHUSUS PRINT */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; }
            .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            background-color: white;
        }

        /* Garis Kop */
        .garis-kop { border-bottom: 4px solid #000; margin-top: 2px; }
        .garis-kop-tipis { border-bottom: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }
    </style>
</head>

<body class="p-4">

    <div class="row align-items-center mb-2">
        <div class="col-2 text-center">
            <img src="../assets/img/logo.png" width="90" alt="Logo" onerror="this.style.display='none'">
        </div>
        <div class="col-8 text-center header-text">
            <h5 class="fw-bold text-uppercase" style="font-family: Arial;">PEMERINTAH KOTA BANDUNG</h5>
            <h4 class="fw-bold text-uppercase" style="font-family: Arial;">DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
            <p class="mb-0 small" style="font-family: Arial;">Jl. Jendral Sudirman No. 123, Kota Bandung, Jawa Barat</p>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="garis-kop"></div>
    <div class="garis-kop-tipis"></div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase text-decoration-underline">LAPORAN RIWAYAT TRANSAKSI</h4>
        <p class="mb-0">
            Periode: <strong><?= tgl_indo($tgl_awal); ?></strong> 
            s/d <strong><?= tgl_indo($tgl_akhir); ?></strong>
        </p>
    </div>

    <table class="table table-bordered border-dark table-sm align-middle">
        <thead class="table-light text-center border-dark fw-bold">
            <tr>
                <th width="5%" style="background-color: #eee;">No</th>
                <th width="15%" style="background-color: #eee;">Tanggal</th>
                <th width="15%" style="background-color: #eee;">ID Transaksi</th>
                <th style="background-color: #eee;">Nama Barang</th>
                <th width="10%" style="background-color: #eee;">Jenis</th>
                <th width="15%" style="background-color: #eee;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            
            // QUERY JOIN: Menggabungkan 3 Tabel
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
                echo "<tr><td colspan='6' class='text-center py-4 fst-italic text-muted'>Tidak ada transaksi pada periode ini.</td></tr>";
            } else {
                while ($d = mysqli_fetch_assoc($exec)) {
                    
                    // Logika tampilan jenis transaksi
                    if ($d['KUANTITAS_MASUK'] > 0) {
                        $jenis = 'MASUK';
                        $qty   = $d['KUANTITAS_MASUK'];
                        $style = 'font-weight:bold;'; // Tebal jika masuk
                    } else {
                        $jenis = 'KELUAR';
                        $qty   = $d['KUANTITAS_KELUAR'];
                        $style = 'font-style:italic;'; // Miring jika keluar
                    }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= tgl_indo($d['TGL_PERIODE']); ?></td>
                <td class="text-center font-monospace small"><?= $d['ID_STOK']; ?></td>
                <td><?= htmlspecialchars($d['NAMA_BARANG']); ?></td>
                <td class="text-center" style="<?= $style; ?>"><?= $jenis; ?></td>
                <td class="text-center">
                    <?= $qty . ' ' . htmlspecialchars($d['SATUAN']); ?>
                </td>
            </tr>
            <?php 
                } // end while
            } // end else
            ?>
        </tbody>
    </table>

    <div class="row mt-5">
        <div class="col-4 offset-8 text-center">
            <p class="mb-5">
                Bandung, <?= tgl_indo(date('Y-m-d')); ?> <br>
                Mengetahui, <br>
                <strong>Kepala Gudang</strong>
            </p>
            <br><br>
            <p class="fw-bold text-decoration-underline mb-0">BUDI SANTOSO, S.Kom</p>
            <p class="mb-0">NIP. 19850101 201001 1 009</p>
        </div>
    </div>

    <div class="fixed-bottom p-3 bg-white border-top shadow-lg no-print text-center">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-5 shadow me-2">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-5">
            Tutup
        </button>
    </div>

</body>
</html>
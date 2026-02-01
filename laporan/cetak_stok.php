
<?php
// =======================================================
// CETAK DATA STOK (Full Page)
// =======================================================
include '../config/koneksi.php';

// Cek koneksi
if (!isset($koneksi)) {
    die("Koneksi database bermasalah. Pastikan config/koneksi.php benar.");
}

// FUNGSI TANGGAL INDONESIA
// Agar tanggal muncul "30 Januari 2024" bukan "30 January 2024"
function tgl_indo($tanggal){
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    // $pecahkan[0] = tanggal
    // $pecahkan[1] = bulan
    // $pecahkan[2] = tahun
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Stok - <?= date('d-m-Y'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS KHUSUS PRINT */
        @media print {
            .no-print { display: none !important; } /* Sembunyikan tombol saat print */
            body { 
                margin: 0; 
                padding: 0; 
                -webkit-print-color-adjust: exact; /* Paksa warna background tercetak */
            }
            .table-bordered th, 
            .table-bordered td {
                border: 1px solid #000 !important; /* Garis tabel hitam pekat */
            }
        }

        body {
            font-family: 'Times New Roman', Times, serif; /* Font resmi surat dinas */
            background-color: white;
            font-size: 12pt;
        }

        /* Garis Kop Surat */
        .garis-kop {
            border-bottom: 4px solid #000;
            margin-top: 2px;
        }
        .garis-kop-tipis {
            border-bottom: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 20px;
        }
        
        .header-text h4, .header-text h5 {
            margin-bottom: 5px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="p-4">

    <div class="row align-items-center mb-2">
        <div class="col-2 text-center">
            <img src="../assets/img/logo.png" 
                 width="90" 
                 alt="Logo" 
                 onerror="this.style.display='none'"> 
        </div>
        <div class="col-8 text-center header-text">
            <h5 class="fw-bold text-uppercase">PEMERINTAH KOTA BANDUNG</h5>
            <h4 class="fw-bold text-uppercase">DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
            <p class="mb-0 small" style="font-family: Arial;">Jl. Jendral Sudirman No. 123, Kota Bandung, Jawa Barat</p>
            <p class="mb-0 small" style="font-family: Arial;">Telp: (022) 1234567 | Email: info@disdik.bandung.go.id</p>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="garis-kop"></div>
    <div class="garis-kop-tipis"></div>

    <div class="text-center mb-4 mt-4">
        <h4 class="fw-bold text-uppercase text-decoration-underline">LAPORAN DATA STOK BARANG</h4>
        <p class="mb-0">Per Tanggal: <?= tgl_indo(date('Y-m-d')); ?></p>
    </div>

    <table class="table table-bordered border-dark table-sm align-middle">
        <thead class="table-light text-center border-dark fw-bold">
            <tr>
                <th width="5%" style="background-color: #eee;">No.</th>
                <th width="15%" style="background-color: #eee;">ID Barang</th>
                <th style="background-color: #eee;">Nama Barang</th>
                <th width="25%" style="background-color: #eee;">Spesifikasi</th>
                <th width="15%" style="background-color: #eee;">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Query ambil semua barang
            $query = mysqli_query($conn, "
                SELECT * FROM barang 
                ORDER BY nama_barang ASC
            ");

            if (!$query || mysqli_num_rows($query) == 0) {
                echo "
                <tr>
                    <td colspan='5' class='text-center py-4 fst-italic text-muted'>
                        - Data barang belum tersedia di database -
                    </td>
                </tr>";
            } else {
                while ($row = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center font-monospace small"><?= htmlspecialchars($row['id_barang']); ?></td>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td><?= htmlspecialchars($row['spesifikasi']); ?></td>
                <td class="text-center fw-bold">
                    <?= $row['stok_akhir']; ?> 
                    <span class="fw-normal small"><?= htmlspecialchars($row['satuan']); ?></span>
                </td>
            </tr>
            <?php 
                } // End while
            } // End else
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

            <p class="fw-bold text-decoration-underline mb-0">
                BUDI SANTOSO, S.Kom
            </p>
            <p class="mb-0">
                NIP. 19850101 201001 1 009
            </p>
        </div>
    </div>

    <div class="fixed-bottom p-3 bg-white border-top shadow-lg no-print text-center">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-5 shadow me-2">
            <i class="fas fa-print me-2"></i> Cetak Sekarang
        </button>

        <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-5">
            Tutup
        </button>
    </div>

</body>
</html>
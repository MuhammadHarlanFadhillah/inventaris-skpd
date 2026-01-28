<?php
// =======================================================
// HEADER
// =======================================================
include '../config/koneksi.php';
include '../layout/header.php';
?>

<style>
    .hover-top {
        transition: all 0.3s ease;
        cursor: default;
    }
    .hover-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<!-- ==============================
     JUDUL HALAMAN
============================== -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 text-primary fw-bold">
            <i class="fas fa-print me-2"></i>Pusat Laporan
        </h1>
        <p class="text-muted mb-0">
            Silakan pilih jenis laporan yang ingin dicetak.
        </p>
    </div>
</div>

<div class="row">

    <!-- ==============================
         LAPORAN STOK
    ============================== -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-top">
            <div class="card-body text-center p-4">
                <div class="mb-3 text-primary opacity-75">
                    <i class="fas fa-boxes fa-4x"></i>
                </div>

                <h4 class="fw-bold">Laporan Aset & Stok</h4>
                <p class="text-muted small">
                    Cetak daftar seluruh barang beserta jumlah stok terkini di gudang.
                </p>

                <hr class="w-25 mx-auto my-4">

                <a href="cetak_stok.php"
                   target="_blank"
                   class="btn btn-primary rounded-pill px-5 shadow-sm w-75">
                    <i class="fas fa-file-pdf me-2"></i>Preview & Cetak
                </a>
            </div>
        </div>
    </div>

    <!-- ==============================
         LAPORAN PERIODE
    ============================== -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-top">
            <div class="card-body p-4">

                <div class="text-center">
                    <div class="mb-3 text-success opacity-75">
                        <i class="fas fa-calendar-alt fa-4x"></i>
                    </div>

                    <h4 class="fw-bold">Laporan Periode</h4>
                    <p class="text-muted small">
                        Pilih rentang tanggal untuk mencetak riwayat transaksi.
                    </p>
                </div>

                <hr>

                <form action="cetak_transaksi.php" method="GET" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">
                                Dari Tanggal
                            </label>
                            <input type="date"
                                   name="tgl_awal"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-bold small">
                                Sampai Tanggal
                            </label>
                            <input type="date"
                                   name="tgl_akhir"
                                   class="form-control"
                                   value="<?= date('Y-m-d'); ?>"
                                   required>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit"
                                class="btn btn-success rounded-pill px-4 shadow-sm w-100">
                            <i class="fas fa-print me-2"></i>Cetak Laporan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<?php include '../layout/footer.php'; ?>

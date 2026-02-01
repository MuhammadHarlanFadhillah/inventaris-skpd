
<?php
// =======================================================
// HEADER & KONEKSI
// =======================================================
include '../config/koneksi.php';
include '../layout/header.php';

// Cek koneksi
if (!isset($conn)) {
    die("Koneksi database bermasalah.");
}
?>

<style>
    .card-laporan {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid rgba(0,0,0,0.05);
        cursor: default;
    }
    .card-laporan:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
        border-color: rgba(13, 110, 253, 0.3);
    }
    .icon-circle {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold text-primary">
                <i class="fas fa-print me-2"></i>Pusat Laporan
            </h3>
            <p class="text-muted mb-0">
                Menu cetak laporan inventaris dan rekapitulasi transaksi.
            </p>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card card-laporan h-100 rounded-4 shadow-sm bg-white">
                <div class="card-body text-center p-5">
                    
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-boxes fa-3x"></i>
                    </div>

                    <h4 class="fw-bold mb-3">Laporan Aset & Stok</h4>
                    <p class="text-muted px-3 mb-4">
                        Cetak daftar lengkap seluruh barang aset, persediaan, dan jumlah stok terkini yang tersedia di gudang.
                    </p>

                    <hr class="w-50 mx-auto my-4 text-muted opacity-25">

                    <a href="cetak_stok.php" 
                       target="_blank" 
                       class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-bold w-100">
                        <i class="fas fa-file-pdf me-2"></i>Preview & Cetak
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-laporan h-100 rounded-4 shadow-sm bg-white">
                <div class="card-body p-5">
                    
                    <div class="text-center">
                        <div class="icon-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-history fa-3x"></i>
                        </div>

                        <h4 class="fw-bold mb-3">Laporan Transaksi</h4>
                        <p class="text-muted small px-3">
                            Rekapitulasi barang masuk dan keluar berdasarkan rentang tanggal tertentu.
                        </p>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <form action="cetak_transaksi.php" method="GET" target="_blank">
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Dari Tanggal</label>
                                <input type="date" name="tgl_awal" class="form-control bg-light" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Sampai Tanggal</label>
                                <input type="date" name="tgl_akhir" class="form-control bg-light" value="<?= date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 shadow-sm fw-bold w-100">
                                <i class="fas fa-print me-2"></i>Cetak Laporan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../layout/footer.php'; ?>
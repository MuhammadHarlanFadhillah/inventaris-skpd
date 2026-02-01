
<?php 
// PANGGIL KONEKSI DULUAN
include 'config/koneksi.php'; 

// Panggil header (Navbar & Session Check ada di sini)
include 'layout/header.php'; 

// --- [LOGIKA PHP UNTUK DASHBOARD] ---

// 1. Hitung Jumlah Barang
$jumlah_barang = 0;
// Menggunakan variabel $conn (sesuai config)
$query_barang = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");

if($query_barang) {
    $data_barang = mysqli_fetch_assoc($query_barang);
    $jumlah_barang = $data_barang['total'];
}

// 2. Hitung Jumlah Transaksi
$jumlah_transaksi = 0;
$query_trx = mysqli_query($conn, "SELECT COUNT(*) as total FROM stok_persediaan");

if($query_trx){
    $data_trx = mysqli_fetch_assoc($query_trx);
    $jumlah_transaksi = $data_trx['total'];
}

// 3. Cek Host (Info Server)
$server_host = $_SERVER['SERVER_NAME'];
?>

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
    }
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        cursor: pointer;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    .badge-pulse { animation: pulse-green 2s infinite; }
    .ls-1 { letter-spacing: 1px; }
</style>

<div class="p-5 mb-5 hero-gradient rounded-4 shadow-lg text-white">
    <div class="container-fluid py-2">
        <h1 class="display-5 fw-bold">Selamat Datang, <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin'; ?>!</h1> 
        <p class="col-md-8 fs-5 opacity-75">
            Sistem Informasi Inventaris Aset Daerah (SKPD). 
            Pantau stok dan aset secara real-time.
        </p>
        
        <div class="mt-4">
            <?php if($conn): ?>
                <span class="badge bg-white text-success fw-bold p-2 px-3 rounded-pill shadow-sm badge-pulse">
                    <i class="fas fa-check-circle me-1"></i> Database Terhubung
                </span>
                <span class="badge border border-white p-2 px-3 ms-2 rounded-pill bg-white bg-opacity-25">
                    <i class="fas fa-server me-1"></i> Host: <?php echo $server_host; ?>
                </span>
            <?php else: ?>
                <span class="badge bg-danger text-white p-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-exclamation-triangle me-1"></i> Koneksi Gagal
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row align-items-md-stretch">
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
            <div class="card-body text-center p-4 d-flex flex-column">
                <div class="mb-3 text-primary opacity-75"><i class="fas fa-box fa-3x"></i></div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;"><?php echo $jumlah_barang; ?></h2>
                <h6 class="text-secondary fw-bold text-uppercase ls-1 mb-3">Unit Barang</h6>
                <p class="card-text text-dark opacity-75 mb-4 small">Kelola master data barang dan stok awal.</p>
                <a href="barang/index.php" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-medium mt-auto stretched-link">
                    Buka Gudang <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
            <div class="card-body text-center p-4 d-flex flex-column">
                <div class="mb-3 text-success opacity-75"><i class="fas fa-exchange-alt fa-3x"></i></div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;"><?php echo $jumlah_transaksi; ?></h2>
                <h6 class="text-secondary fw-bold text-uppercase ls-1 mb-3">Riwayat Transaksi</h6>
                <p class="card-text text-dark opacity-75 mb-4 small">Catat barang masuk dan keluar.</p>
                <a href="transaksi/index.php" class="btn btn-outline-success w-100 rounded-pill py-2 fw-medium mt-auto stretched-link">
                    Input Transaksi <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
            <div class="card-body text-center p-4 d-flex flex-column">
                <div class="mb-3 text-info opacity-75"><i class="fas fa-print fa-3x"></i></div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;">Cetak</h2>
                <h6 class="text-secondary fw-bold text-uppercase ls-1 mb-3">Laporan Akhir</h6>
                <p class="card-text text-dark opacity-75 mb-4 small">Rekapitulasi stok dan cetak PDF.</p>
                <a href="laporan/laporan.php" class="btn btn-outline-info text-dark w-100 rounded-pill py-2 fw-medium mt-auto stretched-link">
                    Lihat Laporan <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="alert bg-white border-0 shadow-sm d-flex align-items-center rounded-3 p-4" role="alert" style="border-left: 5px solid #0d6efd !important;">
            <i class="fas fa-info-circle fa-2x text-primary me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold text-primary mb-1">Panduan Singkat</h5>
                <p class="mb-0 text-muted small">
                    Pastikan mengisi <strong>Master Barang</strong> sebelum melakukan transaksi. Sistem otomatis menghitung <strong>Sisa Stok</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<?php 
// Panggil Footer
include 'layout/footer.php'; 
?>
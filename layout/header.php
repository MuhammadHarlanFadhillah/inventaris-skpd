<?php
// Cek Session: Apakah user sudah login?
// Jika session belum dimulai, start session.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login, paksa kembali ke halaman login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Kita gunakan script javascript untuk redirect agar lebih aman dari error header
    echo "<script>window.location='". $base_url ."login.php';</script>";
    exit;
}

// Logika Menu Aktif (Mendeteksi URL saat ini)
// Kita cek apakah URL mengandung kata 'barang', 'transaksi', atau 'laporan'
$uri = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris SKPD</title>
    
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2897/2897785.png" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { background: white; border-bottom: 3px solid #0d6efd; }
        .nav-link { font-weight: 500; color: #555 !important; }
        .nav-link:hover { color: #0d6efd !important; }
        .nav-link.active { color: #0d6efd !important; font-weight: 700; }
        .container-utama { margin-top: 100px; min-height: 80vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?php echo $base_url; ?>index.php">
        <i class="fa-solid fa-box-open me-2"></i>SIMBAR
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($uri == '/inventaris_skpd/' || strpos($uri, 'index.php') !== false) && strpos($uri, 'barang') === false && strpos($uri, 'transaksi') === false ? 'active' : ''; ?>" 
               href="<?php echo $base_url; ?>index.php">Dashboard</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (strpos($uri, '/barang/') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_url; ?>barang/index.php">Data Barang</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (strpos($uri, '/transaksi/') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_url; ?>transaksi/index.php">Transaksi</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (strpos($uri, '/laporan/') !== false) ? 'active' : ''; ?>" 
               href="<?php echo $base_url; ?>laporan/laporan.php">Laporan</a>
        </li>

        <li class="nav-item ms-lg-3 d-none d-lg-block">|</li>
        
        <li class="nav-item dropdown ms-lg-2">
            <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['nama']; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>logout.php" onclick="return confirm('Yakin ingin keluar?');"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>

<div class="container container-utama">
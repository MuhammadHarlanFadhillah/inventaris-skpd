
<?php
include '../config/koneksi.php';
include '../layout/header.php';

// ================================
// PROSES SIMPAN DATA
// ================================
if (isset($_POST['simpan'])) {

    // 1. Tangkap Input & Amankan dari SQL Injection
    $id     = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $spek   = mysqli_real_escape_string($koneksi, $_POST['spek']);

    // 2. CEK DUPLIKAT ID BARANG
    // Menggunakan variabel $koneksi
    $cek = mysqli_query($conn, "SELECT ID_BARANG FROM BARANG WHERE ID_BARANG = '$id'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Jika ID sudah ada
        echo "<script>
                alert('❌ Gagal! ID Barang ($id) sudah digunakan. Silakan gunakan ID lain.');
              </script>";
    } else {

        // 3. SIMPAN KE DATABASE (Query Standard)
        // Set STOK_AKHIR default 0 saat barang baru dibuat
        $sql  = "INSERT INTO BARANG (ID_BARANG, NAMA_BARANG, SATUAN, SPESIFIKASI, STOK_AKHIR) 
                 VALUES ('$id', '$nama', '$satuan', '$spek', 0)";
        
        $exec = mysqli_query($conn, $sql);

        if ($exec) {
            echo "<script>
                    alert('✅ Data barang berhasil ditambahkan!');
                    window.location = 'index.php';
                  </script>";
        } else {
            echo "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'>
                    <strong>Error Database:</strong> " . mysqli_error($koneksi) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                  </div>";
        }
    }
}
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-6 col-md-8">

        <div class="mb-3">
            <a href="index.php" class="text-decoration-none text-muted fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cube me-2"></i>Form Input Barang
                </h5>
            </div>

            <div class="card-body p-4 bg-white">
                <form method="POST" autocomplete="off">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">ID BARANG (KODE UNIK)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-barcode text-muted"></i></span>
                            <input type="text" name="id" class="form-control"
                                   placeholder="Contoh: B001" 
                                   onkeyup="this.value = this.value.toUpperCase()" 
                                   required autofocus>
                        </div>
                        <div class="form-text text-muted small">ID tidak boleh sama dengan barang lain.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">NAMA BARANG</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Laptop Asus, Kertas A4" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark small">SATUAN</label>
                            <input type="text" name="satuan" class="form-control"
                                   placeholder="Unit / Pcs / Rim" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark small">SPESIFIKASI</label>
                            <input type="text" name="spek" class="form-control"
                                   placeholder="Warna, Merk, dll (Opsional)">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="simpan" class="btn btn-primary rounded-pill fw-bold py-2">
                            <i class="fas fa-save me-2"></i>SIMPAN DATA
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
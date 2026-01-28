<?php 
ob_start(); // Anti Blank
include '../config/koneksi.php';

// --- OBAT ERROR 500: HAPUS TRIGGER BAWAAN ---
// Kita hapus trigger lama yg mungkin maksa pake huruf besar
mysqli_query($conn, "DROP TRIGGER IF EXISTS update_stok_masuk");
mysqli_query($conn, "DROP TRIGGER IF EXISTS update_stok_keluar");
mysqli_query($conn, "DROP TRIGGER IF EXISTS barang_masuk");
mysqli_query($conn, "DROP TRIGGER IF EXISTS barang_keluar");
// --------------------------------------------

if (isset($_POST['simpan'])) {
    $id_brg = $_POST['id_barang'];
    $tgl    = $_POST['tanggal'];
    $jenis  = $_POST['jenis'];
    $jml    = (int) $_POST['jumlah'];
    $harga  = str_replace('.', '', $_POST['harga']);
    
    $id_stok = "TRX-" . time();
    $id_skpd = "SKPD-01"; // Default

    // 1. INSERT HEADER
    $q1 = "INSERT INTO stok_persediaan (id_stok, id_skpd, tgl_periode) VALUES ('$id_stok', '$id_skpd', '$tgl')";
    // Cek error header
    if (!mysqli_query($conn, $q1)) {
        die("<h3>Gagal Simpan Header: " . mysqli_error($conn) . "</h3>");
    }

    // 2. INSERT DETAIL
    $qin  = ($jenis == 'MASUK') ? $jml : 0;
    $qout = ($jenis == 'KELUAR') ? $jml : 0;
    
    $q2 = "INSERT INTO detail_stok (id_stok, id_barang, harga_satuan, kuantitas_masuk, kuantitas_keluar) 
           VALUES ('$id_stok', '$id_brg', '$harga', '$qin', '$qout')";
    
    // Cek error detail
    if (!mysqli_query($conn, $q2)) {
        mysqli_query($conn, "DELETE FROM stok_persediaan WHERE id_stok='$id_stok'"); // Batalkan header
        die("<h3>Gagal Simpan Detail: " . mysqli_error($conn) . "</h3>");
    }

    // 3. UPDATE STOK (PHP MENGAMBIL ALIH TUGAS TRIGGER)
    // Karena trigger udah kita hapus, kita update stok manual pake PHP (Huruf Kecil Aman)
    if ($jenis == 'MASUK') {
        mysqli_query($conn, "UPDATE barang SET stok_akhir = stok_akhir + $jml WHERE id_barang = '$id_brg'");
    } else {
        mysqli_query($conn, "UPDATE barang SET stok_akhir = stok_akhir - $jml WHERE id_barang = '$id_brg'");
    }

    // SUKSES
    echo "<script>alert('Berhasil!'); window.location='index.php';</script>";
}
?>

<?php include '../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white py-3"><h5 class="mb-0">Transaksi Baru</h5></div>
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label>Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <option value="">Pilih...</option>
                            <?php
                            $q = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
                            while ($r = mysqli_fetch_assoc($q)) {
                                // Paksa ambil data (huruf besar/kecil aman)
                                $id = $r['id_barang'] ?? $r['ID_BARANG'];
                                $nm = $r['nama_barang'] ?? $r['NAMA_BARANG'];
                                $st = $r['stok_akhir'] ?? $r['STOK_AKHIR'];
                                echo "<option value='$id'>$nm (Sisa: $st)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col"><label>Tanggal</label><input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="form-control"></div>
                        <div class="col"><label>Harga</label><input type="number" name="harga" class="form-control"></div>
                    </div>
                    <div class="mb-3">
                        <label>Jenis</label><br>
                        <input type="radio" name="jenis" value="MASUK" checked> Masuk
                        <input type="radio" name="jenis" value="KELUAR" class="ms-3"> Keluar
                    </div>
                    <div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div>
                    <button type="submit" name="simpan" class="btn btn-primary w-100 rounded-pill">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ob_end_flush(); ?>
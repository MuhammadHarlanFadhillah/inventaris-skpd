<?php
include 'koneksi.php';

echo "<h2>🛠️ PEMBERSIHAN DATABASE & TRIGGER</h2><hr>";

// 1. Cek Daftar Trigger yang Ada
$q_show = mysqli_query($conn, "SHOW TRIGGERS");
if (mysqli_num_rows($q_show) > 0) {
    echo "⚠️ Ditemukan Trigger Aktif:<br><ul>";
    while ($row = mysqli_fetch_assoc($q_show)) {
        $trig = $row['Trigger'];
        echo "<li>Hapus Trigger: <b>$trig</b>... ";
        
        // 2. Hapus Trigger
        $q_drop = mysqli_query($conn, "DROP TRIGGER IF EXISTS $trig");
        if ($q_drop) {
            echo "✅ BERHASIL DIHAPUS</li>";
        } else {
            echo "❌ GAGAL: " . mysqli_error($conn) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "✅ Tidak ada trigger yang mengganggu.<br>";
}

echo "<hr>";

// 3. Cek Tabel BARANG vs barang
$cek_kecil = mysqli_query($conn, "DESCRIBE barang");
$cek_besar = mysqli_query($conn, "DESCRIBE BARANG");

if ($cek_kecil) {
    echo "✅ Tabel <b>'barang'</b> (huruf kecil) DITEMUKAN. Aman.<br>";
} else {
    echo "❌ Tabel <b>'barang'</b> (huruf kecil) TIDAK ADA.<br>";
}

if ($cek_besar) {
    echo "⚠️ Tabel <b>'BARANG'</b> (huruf besar) DITEMUKAN. (Ini penyebab masalah jika ada)<br>";
} else {
    echo "✅ Tabel <b>'BARANG'</b> (huruf besar) TIDAK ADA. (Sesuai harapan)<br>";
}

echo "<hr><h3>🎉 Selesai! Coba input transaksi lagi sekarang.</h3>";
echo "<a href='../transaksi/tambah.php'>Kembali ke Form Transaksi</a>";
?>
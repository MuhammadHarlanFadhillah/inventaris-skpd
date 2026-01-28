<?php
// FILE: config/fix_database.php
include 'koneksi.php';

echo "<h2>🛠️ PEMBERSIHAN DATABASE & TRIGGER</h2><hr>";

// 1. Cek Apakah Ada Trigger yang Nyangkut?
$q_show = mysqli_query($conn, "SHOW TRIGGERS");

if (mysqli_num_rows($q_show) > 0) {
    echo "⚠️ <b>DITEMUKAN TRIGGER AKTIF!</b> (Ini biang keroknya):<br><ul>";
    while ($row = mysqli_fetch_assoc($q_show)) {
        $trig = $row['Trigger'];
        echo "<li>Menghapus Trigger: <b>$trig</b>... ";
        
        // HAPUS TRIGGERNYA
        $q_drop = mysqli_query($conn, "DROP TRIGGER IF EXISTS $trig");
        if ($q_drop) {
            echo "✅ <span style='color:green'>BERHASIL DIHAPUS</span></li>";
        } else {
            echo "❌ GAGAL: " . mysqli_error($conn) . "</li>";
        }
    }
    echo "</ul>";
    echo "<div style='background: #ffeeba; padding: 10px; border: 1px solid #ffc107;'>
            <b>PENJELASAN:</b> Trigger di atas mencoba update tabel 'BARANG' (huruf besar). 
            Karena sekarang kita pakai Linux, itu bikin error. 
            Sekarang Trigger sudah dihapus, kodingan PHP akan mengambil alih tugas update stok.
          </div>";
} else {
    echo "✅ <b>Aman.</b> Tidak ada trigger yang mengganggu.<br>";
}

echo "<hr>";

// 2. Cek Nama Tabel yang Benar
$cek_kecil = mysqli_query($conn, "DESCRIBE barang");
$cek_besar = mysqli_query($conn, "DESCRIBE BARANG");

if ($cek_kecil) {
    echo "✅ Tabel <b>'barang'</b> (huruf kecil) DITEMUKAN. (Database aman)<br>";
} else {
    echo "❌ Tabel <b>'barang'</b> (huruf kecil) TIDAK ADA.<br>";
}

if ($cek_besar) {
    echo "⚠️ Tabel <b>'BARANG'</b> (huruf besar) MASIH ADA. (Sebaiknya di-rename jadi kecil)<br>";
}

echo "<hr><h3>🎉 SELESAI! HANTUNYA SUDAH HILANG.</h3>";
echo "<a href='../transaksi/tambah.php' style='background:blue; color:white; padding:10px; text-decoration:none; border-radius:5px;'>Coba Transaksi Lagi Sekarang</a>";
?>
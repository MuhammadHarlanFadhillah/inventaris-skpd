<?php
/**
 * RAILWAY AUTO-FIX DATABASE
 * File ini harus di-run sekali untuk fix database di Railway
 * Cara: Buka di browser: http://yourrailwayapp.com/config/auto_fix_railway.php
 */

include 'koneksi.php';

echo "<h2>🔧 Railway Database Auto-Fix</h2>";
echo "<hr>";

// ==================================================
// STEP 1: DROP SEMUA TRIGGER YANG BERMASALAH
// ==================================================
echo "<h3>Step 1: Drop Trigger Bermasalah</h3>";

$triggers = [
    'update_stok_after_insert',
    'update_stok_after_delete',
    'update_stok_masuk',
    'update_stok_keluar',
    'barang_masuk',
    'barang_keluar',
    'TG_STOK_UPDATE'
];

foreach ($triggers as $trigger) {
    $sql = "DROP TRIGGER IF EXISTS $trigger";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "✅ Dropped: $trigger<br>";
    } else {
        echo "⚠️ Skip: $trigger (tidak ada atau sudah terhapus)<br>";
    }
}

// ==================================================
// STEP 2: ALTER TABLE BARANG (JIKA ADA COLUMN HURUF BESAR)
// ==================================================
echo "<h3>Step 2: Standardize Column Names (Lowercase)</h3>";

// Cek struktur table barang
$check = mysqli_query($conn, "SHOW COLUMNS FROM barang");
$columns = [];
while ($row = mysqli_fetch_assoc($check)) {
    $columns[] = $row['Field'];
}

// Jika ada kolom huruf besar, rename ke lowercase
$rename_cols = [
    'ID_BARANG' => 'id_barang',
    'NAMA_BARANG' => 'nama_barang',
    'SATUAN' => 'satuan',
    'SPESIFIKASI' => 'spesifikasi',
    'STOK_AKHIR' => 'stok_akhir'
];

foreach ($rename_cols as $old => $new) {
    if (in_array($old, $columns)) {
        $sql = "ALTER TABLE barang CHANGE COLUMN `$old` `$new` VARCHAR(255) NOT NULL DEFAULT ''";
        if ($old === 'STOK_AKHIR') {
            $sql = "ALTER TABLE barang CHANGE COLUMN `$old` `$new` INT(11) NOT NULL DEFAULT 0";
        }
        
        $result = @mysqli_query($conn, $sql);
        if ($result) {
            echo "✅ Renamed: $old → $new<br>";
        } else {
            echo "⚠️ Skip: $old (sudah lowercase atau error)<br>";
        }
    }
}

// ==================================================
// STEP 3: ALTER TABLE DETAIL_STOK
// ==================================================
echo "<h3>Step 3: Standardize detail_stok</h3>";

$check = mysqli_query($conn, "SHOW COLUMNS FROM detail_stok");
$columns = [];
while ($row = mysqli_fetch_assoc($check)) {
    $columns[] = $row['Field'];
}

$detail_renames = [
    'ID_DETAIL' => 'id_detail',
    'ID_STOK' => 'id_stok',
    'ID_BARANG' => 'id_barang',
    'HARGA_SATUAN' => 'harga_satuan',
    'KUANTITAS_MASUK' => 'kuantitas_masuk',
    'KUANTITAS_KELUAR' => 'kuantitas_keluar'
];

foreach ($detail_renames as $old => $new) {
    if (in_array($old, $columns)) {
        if (in_array($old, ['HARGA_SATUAN'])) {
            $sql = "ALTER TABLE detail_stok CHANGE COLUMN `$old` `$new` DECIMAL(15,2) NOT NULL";
        } else {
            $sql = "ALTER TABLE detail_stok CHANGE COLUMN `$old` `$new` VARCHAR(255) NOT NULL";
        }
        
        $result = @mysqli_query($conn, $sql);
        if ($result) {
            echo "✅ Renamed: $old → $new<br>";
        } else {
            echo "⚠️ Skip: $old<br>";
        }
    }
}

// ==================================================
// STEP 4: ALTER TABLE STOK_PERSEDIAAN
// ==================================================
echo "<h3>Step 4: Standardize stok_persediaan</h3>";

$check = mysqli_query($conn, "SHOW COLUMNS FROM stok_persediaan");
$columns = [];
while ($row = mysqli_fetch_assoc($check)) {
    $columns[] = $row['Field'];
}

$stok_renames = [
    'ID_STOK' => 'id_stok',
    'ID_SKPD' => 'id_skpd',
    'TGL_PERIODE' => 'tgl_periode',
    'STOK_SISA' => 'stok_sisa'
];

foreach ($stok_renames as $old => $new) {
    if (in_array($old, $columns)) {
        $sql = "ALTER TABLE stok_persediaan CHANGE COLUMN `$old` `$new` VARCHAR(255) NOT NULL DEFAULT ''";
        
        $result = @mysqli_query($conn, $sql);
        if ($result) {
            echo "✅ Renamed: $old → $new<br>";
        } else {
            echo "⚠️ Skip: $old<br>";
        }
    }
}

// ==================================================
// STEP 5: VERIFY
// ==================================================
echo "<h3>Step 5: Verifikasi</h3>";

echo "<h4>Tabel yang ada:</h4>";
$tables = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($tables)) {
    echo "  • " . $row[0] . "<br>";
}

echo "<h4>Trigger yang ada:</h4>";
$triggers_check = mysqli_query($conn, "SHOW TRIGGERS");
if (mysqli_num_rows($triggers_check) > 0) {
    echo "  ⚠️ Masih ada " . mysqli_num_rows($triggers_check) . " trigger!<br>";
    while ($row = mysqli_fetch_assoc($triggers_check)) {
        echo "    - {$row['Trigger']}<br>";
    }
} else {
    echo "  ✅ Tidak ada trigger (aman!)<br>";
}

echo "<h4>Data barang:</h4>";
$count = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
$row = mysqli_fetch_assoc($count);
echo "  Total: " . $row['total'] . " barang<br>";

echo "<hr>";
echo "<h2>✅ Selesai!</h2>";
echo "<p>Database Railway sudah di-fix. Silakan test aplikasi Anda.</p>";
echo "<p><a href='../'>← Kembali ke aplikasi</a></p>";
?>

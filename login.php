<?php
session_start();
// Pastikan path ini benar mengarah ke file koneksi.php
include 'config/koneksi.php';

if (isset($_POST['btn_login'])) {
    // Ambil input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    // Pastikan enkripsi password di database memang MD5. Kalau plain text, hapus md5().
    $password = md5($_POST['password']); 

    // PERBAIKAN 1: Tabel 'user' huruf kecil (Wajib buat Railway/Linux)
    // PERBAIKAN 2: Kolom 'username' & 'password' asumsi huruf kecil
    $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    
    // Cek error query buat debugging kalau masih gagal
    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }

    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);

        // Regenerate ID biar aman (Security Best Practice)
        session_regenerate_id(true);

        // PERBAIKAN 3: Key array huruf kecil (sesuai output mysqli_fetch_assoc standar)
        // Gunakan Null Coalescing (??) buat jaga-jaga kalau di DB lu kolomnya kapital
        $_SESSION['status']   = "login";
        $_SESSION['id_user']  = $data['id_user'] ?? $data['ID_USER']; 
        $_SESSION['nama']     = $data['nama_lengkap'] ?? $data['NAMA_LENGKAP'];
        $_SESSION['level']    = $data['level'] ?? $data['LEVEL'];

        echo "<script>
                alert('Selamat Datang, " . ($_SESSION['nama']) . "!');
                window.location='index.php';
              </script>";
    } else {
        echo "<script>
                alert('Login Gagal! Username atau Password salah.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMBAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .btn-login {
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        .form-control { border-radius: 10px; padding: 12px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-login bg-white">
                    <div class="card-header pt-4 bg-white border-0 text-center">
                        <div class="text-primary mb-2"><i class="fas fa-boxes fa-3x"></i></div>
                        <h4 class="fw-bold text-dark">S I M B A R</h4>
                        <p class="text-muted small">Silakan login untuk melanjutkan</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">USERNAME</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="username" class="form-control border-start-0 bg-light" placeholder="Username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 bg-light" placeholder="Password" required>
                                </div>
                            </div>

                            <button type="submit" name="btn_login" class="btn btn-primary w-100 py-2 btn-login mb-3">
                                MASUK SEKARANG <i class="fas fa-sign-in-alt ms-2"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">&copy; <?php echo date('Y'); ?> IF-3 UNIKOM</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
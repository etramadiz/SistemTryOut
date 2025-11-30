<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek user di database
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Cek password (sesuai data dummy di database kamu)
        if ($data['password_hash'] == $password) {
            // Buat Session
            $_SESSION['status'] = "login";
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['role'] = $data['role'];

            // Redirect berdasarkan Role
            if ($data['role'] == 'admin') {
                // Jika admin, bisa diarahkan ke halaman admin khusus (nanti dibuat)
                // Untuk sekarang kita arahkan ke index dulu dengan notifikasi
                echo "<script>alert('Login Admin Berhasil!'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Selamat Datang, Peserta!'); window.location='index.php';</script>";
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Tryout</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { width: 100%; max-width: 400px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card card-login bg-white p-4">
        <h3 class="text-center fw-bold mb-4 text-primary">🎓 Tryout Login</h3>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Contoh: budi@gmail.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">Masuk Sekarang</button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">Belum punya akun? Hubungi Admin.</small>
        </div>
        
        <div class="alert alert-info mt-3 small">
            <strong>Info Login (Sesuai DB):</strong><br>
            Email: budi@gmail.com<br>
            Pass: hash_rahasia_budi
        </div>
    </div>
</body>
</html>
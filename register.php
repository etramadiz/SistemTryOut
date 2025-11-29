<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $konfirmasi = mysqli_real_escape_string($koneksi, $_POST['konfirmasi_password']);

    // 1. Cek apakah password dan konfirmasi sama
    if ($password != $konfirmasi) {
        $error = "Password dan Konfirmasi Password tidak sama!";
    } else {
        // 2. Cek apakah email sudah ada di database
        $cek_email = mysqli_query($koneksi, "SELECT email FROM user WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar! Silakan login.";
        } else {
            // 3. Masukkan data ke database
            // Role otomatis di-set sebagai 'peserta'
            // Password disimpan langsung (sesuai sistem login kamu saat ini)
            $query_insert = "INSERT INTO user (nama_lengkap, email, password_hash, role) VALUES ('$nama', '$email', '$password', 'peserta')";
            
            if (mysqli_query($koneksi, $query_insert)) {
                echo "<script>
                        alert('Registrasi Berhasil! Silakan Login.');
                        window.location='login.php';
                      </script>";
            } else {
                $error = "Terjadi kesalahan sistem: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Sistem Tryout</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card-login { width: 100%; max-width: 450px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="card card-login bg-white p-4">
        <h3 class="text-center fw-bold mb-2 text-primary">📝 Daftar Akun</h3>
        <p class="text-center text-muted mb-4">Buat akun untuk mulai Tryout</p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required placeholder="Nama Kamu">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Buat Password">
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" class="form-control" required placeholder="Ulangi Password">
            </div>
            
            <button type="submit" name="register" class="btn btn-success w-100 fw-bold">Daftar Sekarang</button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login disini</a></small>
        </div>
    </div>
</body>
</html>
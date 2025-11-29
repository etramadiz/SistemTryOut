<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { die("Akses Ditolak"); }

$id_user = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user'");
$data = mysqli_fetch_assoc($query);

// Proses Update
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password_baru = $_POST['password'];

    // Cek apakah password diubah?
    if (!empty($password_baru)) {
        // Jika diisi, update password (langsung string biasa sesuai database kamu)
        $sql = "UPDATE user SET nama_lengkap='$nama', email='$email', role='$role', password_hash='$password_baru' WHERE id_user='$id_user'";
    } else {
        // Jika kosong, jangan ubah password
        $sql = "UPDATE user SET nama_lengkap='$nama', email='$email', role='$role' WHERE id_user='$id_user'";
    }
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Data User Berhasil Diupdate!'); window.location='data_user.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0 text-dark">Edit Data User</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= $data['nama_lengkap'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Role / Peran</label>
                                <select name="role" class="form-select">
                                    <option value="peserta" <?= ($data['role'] == 'peserta') ? 'selected' : '' ?>>Peserta</option>
                                    <option value="admin" <?= ($data['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            
                            <hr>
                            <div class="mb-3">
                                <label class="fw-bold text-danger">Reset Password (Opsional)</label>
                                <input type="text" name="password" class="form-control" placeholder="Isi hanya jika ingin mengganti password">
                                <small class="text-muted">Biarkan kosong jika password tidak ingin diubah.</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                                <a href="data_user.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
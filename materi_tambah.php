<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { header("location:login.php"); die(); }

if (isset($_POST['upload'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Proses Upload File
    $ekstensi_boleh = array('pdf', 'doc', 'docx');
    $nama_file = $_FILES['file']['name'];
    $x = explode('.', $nama_file);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['file']['tmp_name'];
    
    // Beri nama unik agar tidak bentrok
    $nama_baru = date('YmdHis') . '-' . $nama_file;

    if (in_array($ekstensi, $ekstensi_boleh) === true) {
        if (move_uploaded_file($file_tmp, 'file_materi/' . $nama_baru)) {
            // Simpan ke DB
            $query = "INSERT INTO materi (judul, deskripsi, nama_file) VALUES ('$judul', '$deskripsi', '$nama_baru')";
            if(mysqli_query($koneksi, $query)){
                echo "<script>alert('Berhasil Upload Materi!'); window.location='admin_materi.php';</script>";
            } else {
                echo "Gagal database: " . mysqli_error($koneksi);
            }
        } else {
            echo "<script>alert('Gagal upload file!');</script>";
        }
    } else {
        echo "<script>alert('Hanya boleh upload file PDF atau Word!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Materi Baru</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Upload Materi / Modul Belajar</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Materi</label>
                                <input type="text" name="judul" class="form-control" required placeholder="Contoh: Rangkuman Rumus Matematika">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">File Materi (PDF/Word)</label>
                                <input type="file" name="file" class="form-control" required>
                                <small class="text-muted">Maksimal ukuran file tergantung settingan PHP (biasanya 2MB).</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="upload" class="btn btn-success fw-bold">Upload Sekarang</button>
                                <a href="admin_materi.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
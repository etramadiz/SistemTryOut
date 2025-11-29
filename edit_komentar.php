<?php
session_start();
include 'koneksi.php';

// Cek Login & Validasi Kepemilikan
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

$id_user = $_SESSION['id_user'];
$id_komentar = $_GET['id'];
$id_paket = $_GET['id_paket']; // Untuk redirect kembali

// Ambil data komentar lama (Hanya jika milik user tersebut)
$query = mysqli_query($koneksi, "SELECT * FROM diskusi_soal WHERE id_komentar='$id_komentar' AND id_user='$id_user'");
$data = mysqli_fetch_assoc($query);

// Jika komentar tidak ditemukan (atau bukan miliknya), tendang balik
if (!$data) {
    echo "<script>alert('Akses ditolak!'); window.location='diskusi.php?id=$id_paket';</script>";
    exit;
}

// Proses Update
if (isset($_POST['update'])) {
    $isi_baru = mysqli_real_escape_string($koneksi, $_POST['isi']);
    mysqli_query($koneksi, "UPDATE diskusi_soal SET isi_komentar='$isi_baru' WHERE id_komentar='$id_komentar'");
    header("location:diskusi.php?id=$id_paket");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Komentar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    
    <div class="card shadow border-0" style="width: 100%; max-width: 500px;">
        <div class="card-header bg-warning text-dark fw-bold">
            ✏️ Edit Komentar
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted small">Isi Komentar</label>
                    <textarea name="isi" class="form-control" rows="4" required><?= $data['isi_komentar'] ?></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="diskusi.php?id=<?= $id_paket ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="update" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
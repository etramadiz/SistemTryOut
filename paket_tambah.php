<?php
session_start();
include 'koneksi.php';

// Cek Admin (Wajib Login)
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}

// Proses Simpan Data ke Database
if (isset($_POST['simpan'])) {
    $nama_paket = mysqli_real_escape_string($koneksi, $_POST['nama_paket']);
    $id_kategori = $_POST['id_kategori'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga']; // 0 jika gratis
    $status = $_POST['status']; // 1 = publish, 0 = draft

    $query = "INSERT INTO paket_tryout (id_kategori, nama_paket, deskripsi, durasi_menit, harga, status_publish) 
              VALUES ('$id_kategori', '$nama_paket', '$deskripsi', '$durasi', '$harga', '$status')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Paket Berhasil Ditambahkan!'); window.location='admin_paket.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Paket Baru</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
      <div class="container">
        <span class="navbar-brand fw-bold mb-0 h1">⚙️ Admin Panel</span>
        <a href="admin_paket.php" class="btn btn-sm btn-light text-primary fw-bold">Kembali</a>
      </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 fw-bold text-primary">Tambah Paket Tryout Baru</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Paket</label>
                                <input type="text" name="nama_paket" class="form-control" required placeholder="Contoh: Tryout UTBK Saintek 2025">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori Ujian</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php
                                    // Ambil data kategori dari database
                                    $kat = mysqli_query($koneksi, "SELECT * FROM kategori_ujian");
                                    while($k = mysqli_fetch_assoc($kat)){
                                        echo "<option value='{$k['id_kategori']}'>{$k['nama_kategori']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Jelaskan tentang materi ujian ini..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Durasi (Menit)</label>
                                    <input type="number" name="durasi" class="form-control" required value="90">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" required value="0">
                                    <div class="form-text">Isi 0 jika Gratis.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Publikasi</label>
                                <select name="status" class="form-select">
                                    <option value="1">Publish (Tampil di User)</option>
                                    <option value="0">Draft (Sembunyikan)</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" name="simpan" class="btn btn-success fw-bold py-2">💾 Simpan Paket</button>
                                <a href="admin_paket.php" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { header("location:login.php"); die(); }

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM paket_tryout WHERE id_paket = '$id'");
$data = mysqli_fetch_assoc($query);

// Proses Update
if (isset($_POST['update'])) {
    $nama_paket = $_POST['nama_paket'];
    $id_kategori = $_POST['id_kategori'];
    $deskripsi = $_POST['deskripsi'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    $sql = "UPDATE paket_tryout SET 
            id_kategori='$id_kategori', 
            nama_paket='$nama_paket', 
            deskripsi='$deskripsi', 
            durasi_menit='$durasi', 
            harga='$harga', 
            status_publish='$status' 
            WHERE id_paket='$id'";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Paket Berhasil Diupdate!'); window.location='admin_paket.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Paket</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0 text-dark">Edit Paket Tryout</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Paket</label>
                                <input type="text" name="nama_paket" class="form-control" value="<?= $data['nama_paket'] ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kategori Ujian</label>
                                <select name="id_kategori" class="form-select" required>
                                    <?php
                                    $kat = mysqli_query($koneksi, "SELECT * FROM kategori_ujian");
                                    while($k = mysqli_fetch_assoc($kat)){
                                        $selected = ($k['id_kategori'] == $data['id_kategori']) ? 'selected' : '';
                                        echo "<option value='{$k['id_kategori']}' $selected>{$k['nama_kategori']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3" required><?= $data['deskripsi'] ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Durasi (Menit)</label>
                                    <input type="number" name="durasi" class="form-control" value="<?= $data['durasi_menit'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status Publikasi</label>
                                <select name="status" class="form-select">
                                    <option value="1" <?= ($data['status_publish'] == 1) ? 'selected' : '' ?>>Publish</option>
                                    <option value="0" <?= ($data['status_publish'] == 0) ? 'selected' : '' ?>>Draft</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update" class="btn btn-warning fw-bold">Update Paket</button>
                                <a href="admin_paket.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
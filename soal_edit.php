<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php"); die();
}

// Ambil ID dari URL
$id_soal = $_GET['id'];
$id_paket = $_GET['id_paket'];

// Ambil Data Soal Lama
$query = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_soal = '$id_soal'");
$data = mysqli_fetch_assoc($query);

// Proses Update Data
if (isset($_POST['update'])) {
    $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    $opsi_a = mysqli_real_escape_string($koneksi, $_POST['opsi_a']);
    $opsi_b = mysqli_real_escape_string($koneksi, $_POST['opsi_b']);
    $opsi_c = mysqli_real_escape_string($koneksi, $_POST['opsi_c']);
    $opsi_d = mysqli_real_escape_string($koneksi, $_POST['opsi_d']);
    $opsi_e = mysqli_real_escape_string($koneksi, $_POST['opsi_e']);
    $kunci = $_POST['kunci'];
    $bobot = $_POST['bobot'];

    // LOGIKA GAMBAR:
    // Cek apakah user upload gambar baru?
    if ($_FILES['gambar']['name'] != "") {
        // 1. Hapus gambar lama dulu biar server gak penuh
        if (!empty($data['gambar']) && file_exists("foto_soal/" . $data['gambar'])) {
            unlink("foto_soal/" . $data['gambar']);
        }

        // 2. Upload gambar baru
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $nama_baru = rand(100,999)."-".$nama_file;
        move_uploaded_file($tmp_file, "foto_soal/".$nama_baru);

        // Update database DENGAN gambar baru
        $sql = "UPDATE paket_soal SET 
                pertanyaan='$pertanyaan', 
                opsi_a='$opsi_a', opsi_b='$opsi_b', opsi_c='$opsi_c', opsi_d='$opsi_d', opsi_e='$opsi_e', 
                kunci_jawaban='$kunci', bobot='$bobot', gambar='$nama_baru' 
                WHERE id_soal='$id_soal'";
    } else {
        // Update database TANPA ganti gambar (pakai gambar lama)
        $sql = "UPDATE paket_soal SET 
                pertanyaan='$pertanyaan', 
                opsi_a='$opsi_a', opsi_b='$opsi_b', opsi_c='$opsi_c', opsi_d='$opsi_d', opsi_e='$opsi_e', 
                kunci_jawaban='$kunci', bobot='$bobot' 
                WHERE id_soal='$id_soal'";
    }

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Soal Berhasil Diupdate!'); window.location='kelola_soal.php?id=$id_paket';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Soal</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0 text-dark fw-bold">Edit Soal</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="fw-bold">Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="3" required><?= $data['pertanyaan'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Gambar Saat Ini</label><br>
                        <?php if(!empty($data['gambar'])): ?>
                            <img src="foto_soal/<?= $data['gambar'] ?>" height="100" class="mb-2 rounded border">
                            <br><small class="text-muted">Jika ingin mengganti, silakan upload baru di bawah.</small>
                        <?php else: ?>
                            <small class="text-muted">Tidak ada gambar.</small>
                        <?php endif; ?>
                        
                        <input type="file" name="gambar" class="form-control mt-2">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Opsi A</label>
                            <input type="text" name="opsi_a" class="form-control" value="<?= $data['opsi_a'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Opsi B</label>
                            <input type="text" name="opsi_b" class="form-control" value="<?= $data['opsi_b'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Opsi C</label>
                            <input type="text" name="opsi_c" class="form-control" value="<?= $data['opsi_c'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Opsi D</label>
                            <input type="text" name="opsi_d" class="form-control" value="<?= $data['opsi_d'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Opsi E</label>
                            <input type="text" name="opsi_e" class="form-control" value="<?= $data['opsi_e'] ?>" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Kunci Jawaban</label>
                            <select name="kunci" class="form-select" required>
                                <option value="A" <?= ($data['kunci_jawaban'] == 'A') ? 'selected' : '' ?>>A</option>
                                <option value="B" <?= ($data['kunci_jawaban'] == 'B') ? 'selected' : '' ?>>B</option>
                                <option value="C" <?= ($data['kunci_jawaban'] == 'C') ? 'selected' : '' ?>>C</option>
                                <option value="D" <?= ($data['kunci_jawaban'] == 'D') ? 'selected' : '' ?>>D</option>
                                <option value="E" <?= ($data['kunci_jawaban'] == 'E') ? 'selected' : '' ?>>E</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Bobot Nilai</label>
                            <input type="number" name="bobot" class="form-control" value="<?= $data['bobot'] ?>" required>
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" name="update" class="btn btn-warning fw-bold">Update Perubahan</button>
                        <a href="kelola_soal.php?id=<?= $id_paket ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
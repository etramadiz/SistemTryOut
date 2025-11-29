<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { header("location:login.php"); die(); }

$id_paket = $_GET['id_paket'];

// Proses Simpan Soal
if (isset($_POST['simpan'])) {
    $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    $opsi_a = mysqli_real_escape_string($koneksi, $_POST['opsi_a']);
    $opsi_b = mysqli_real_escape_string($koneksi, $_POST['opsi_b']);
    $opsi_c = mysqli_real_escape_string($koneksi, $_POST['opsi_c']);
    $opsi_d = mysqli_real_escape_string($koneksi, $_POST['opsi_d']);
    $opsi_e = mysqli_real_escape_string($koneksi, $_POST['opsi_e']);
    $kunci = $_POST['kunci'];
    $bobot = $_POST['bobot'];

    // Cek ada gambar atau tidak
    $nama_gambar = "";
    if ($_FILES['gambar']['name'] != "") {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        // Validasi Ekstensi
        if(in_array($ekstensi, ['jpg', 'jpeg', 'png'])){
            $nama_gambar = rand(100,999)."-".$nama_file;
            move_uploaded_file($tmp_file, "foto_soal/".$nama_gambar);
        } else {
            echo "<script>alert('Format gambar harus JPG/PNG!');</script>";
        }
    }

    $query = "INSERT INTO paket_soal (id_paket, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, gambar) 
              VALUES ('$id_paket', '$pertanyaan', '$opsi_a', '$opsi_b', '$opsi_c', '$opsi_d', '$opsi_e', '$kunci', '$bobot', '$nama_gambar')";

    if (mysqli_query($koneksi, $query)) {
        header("location:kelola_soal.php?id=$id_paket");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Input Soal Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="fw-bold">Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Gambar Pendukung (Opsional)</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2"><input type="text" name="opsi_a" class="form-control" placeholder="Opsi A" required></div>
                        <div class="col-md-6 mb-2"><input type="text" name="opsi_b" class="form-control" placeholder="Opsi B" required></div>
                        <div class="col-md-6 mb-2"><input type="text" name="opsi_c" class="form-control" placeholder="Opsi C" required></div>
                        <div class="col-md-6 mb-2"><input type="text" name="opsi_d" class="form-control" placeholder="Opsi D" required></div>
                        <div class="col-md-6 mb-2"><input type="text" name="opsi_e" class="form-control" placeholder="Opsi E" required></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Kunci Jawaban</label>
                            <select name="kunci" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Bobot Nilai</label>
                            <input type="number" name="bobot" class="form-control" value="10">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="simpan" class="btn btn-success w-100 fw-bold">Simpan Soal</button>
                        <a href="kelola_soal.php?id=<?= $id_paket ?>" class="btn btn-secondary w-100 mt-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
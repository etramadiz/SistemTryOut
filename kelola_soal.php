<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}

// Ambil ID Paket dari URL
if(!isset($_GET['id'])) {
    header("location:admin_paket.php");
}
$id_paket = $_GET['id'];

// Ambil Info Paket
$q_paket = mysqli_query($koneksi, "SELECT * FROM paket_tryout WHERE id_paket = '$id_paket'");
$paket = mysqli_fetch_assoc($q_paket);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Soal - <?= $paket['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
      <div class="container">
        <span class="navbar-brand fw-bold mb-0 h1">⚙️ Bank Soal</span>
        <a href="admin_paket.php" class="btn btn-sm btn-light text-primary fw-bold">Kembali ke Paket</a>
      </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-white">
                <h4 class="mb-1">Paket: <span class="text-primary fw-bold"><?= $paket['nama_paket'] ?></span></h4>
                <p class="text-muted small mb-0">Kelola butir soal untuk paket ujian ini.</p>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <a href="admin_paket.php" class="btn btn-secondary">← Kembali</a>
            <a href="soal_tambah.php?id_paket=<?= $id_paket ?>" class="btn btn-success fw-bold">+ Tambah Soal Baru</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Pertanyaan</th>
                                <th width="10%">Kunci</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query_soal = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_paket = '$id_paket' ORDER BY id_soal ASC");
                            
                            $no = 1;
                            if(mysqli_num_rows($query_soal) > 0){
                                while($row = mysqli_fetch_assoc($query_soal)){
                                    // Tampilkan gambar kecil jika ada
                                    $img = "";
                                    if(!empty($row['gambar'])){
                                        $img = "<br><img src='foto_soal/{$row['gambar']}' style='max-height:50px; margin-top:5px;'>";
                                    }

                                    // Potong pertanyaan panjang
                                    $tanya = substr(strip_tags($row['pertanyaan']), 0, 100) . "...";
                                    
                                    echo "<tr>
                                        <td class='text-center'>{$no}</td>
                                        <td>
                                            <div class='fw-bold'>{$tanya}</div>
                                            $img
                                        </td>
                                        <td class='text-center fw-bold text-success'>{$row['kunci_jawaban']}</td>
                                        <td class='text-center'>
                                            <a href='soal_hapus.php?id={$row['id_soal']}&id_paket={$id_paket}' class='btn btn-sm btn-danger' onclick=\"return confirm('Hapus soal ini?')\">Hapus</a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>
                                    <h4>Belum ada soal.</h4>
                                    <p>Klik tombol hijau di atas untuk mulai membuat soal.</p>
                                </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
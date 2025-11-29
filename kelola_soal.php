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

// Ambil Info Paket (untuk Judul Halaman)
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
        <div class="d-flex">
            <a href="admin_paket.php" class="btn btn-sm btn-light text-primary fw-bold">Kembali ke Daftar Paket</a>
        </div>
      </div>
    </nav>

    <div class="container mt-4 mb-5">
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-white">
                <h4 class="mb-1">Paket: <span class="text-primary fw-bold"><?= $paket['nama_paket'] ?></span></h4>
                <p class="text-muted small mb-0">Kelola butir soal, kunci jawaban, dan bobot nilai untuk paket ujian ini.</p>
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
                                <th width="5%" class="text-center">No</th>
                                <th>Pertanyaan</th>
                                <th width="10%" class="text-center">Kunci</th>
                                <th width="10%" class="text-center">Bobot</th>
                                <th width="18%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil soal berdasarkan ID Paket
                            $query_soal = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_paket = '$id_paket' ORDER BY id_soal ASC");
                            
                            $no = 1;
                            if(mysqli_num_rows($query_soal) > 0){
                                while($row = mysqli_fetch_assoc($query_soal)){
                                    
                                    // Cek apakah ada gambar?
                                    $img = "";
                                    if(!empty($row['gambar'])){
                                        $img = "<div class='mt-2'><img src='foto_soal/{$row['gambar']}' style='max-height:60px; border-radius:5px; border:1px solid #ddd;'></div>";
                                    }

                                    // Potong pertanyaan jika terlalu panjang (biar tabel rapi)
                                    $pertanyaan_pendek = substr(strip_tags($row['pertanyaan']), 0, 150);
                                    if(strlen($row['pertanyaan']) > 150) {
                                        $pertanyaan_pendek .= "...";
                                    }
                                    
                                    echo "<tr>
                                        <td class='text-center fw-bold'>{$no}</td>
                                        <td>
                                            <div class='fw-bold text-dark'>{$pertanyaan_pendek}</div>
                                            $img
                                            <div class='mt-1 text-muted small'>
                                                <span class='me-2'>A: ".substr($row['opsi_a'],0,15)."..</span>
                                                <span class='me-2'>B: ".substr($row['opsi_b'],0,15)."..</span>
                                            </div>
                                        </td>
                                        <td class='text-center fw-bold text-success fs-5'>{$row['kunci_jawaban']}</td>
                                        <td class='text-center'>{$row['bobot']}</td>
                                        <td class='text-center'>
                                            <a href='soal_edit.php?id={$row['id_soal']}&id_paket={$id_paket}' class='btn btn-sm btn-warning text-dark fw-bold me-1'>Edit</a>
                                            
                                            <a href='soal_hapus.php?id={$row['id_soal']}&id_paket={$id_paket}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus soal ini secara permanen?')\">Hapus</a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                    <h4 class='fw-bold'>Belum ada soal.</h4>
                                    <p>Silakan klik tombol hijau di atas untuk mulai mengisi bank soal.</p>
                                </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
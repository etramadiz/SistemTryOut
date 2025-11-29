<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php"); die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Materi Belajar</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚙️ Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="admin_materi.php">Materi Belajar</a></li>
                <li class="nav-item"><a class="nav-link" href="data_user.php">Data User</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
            </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📚 Manajemen Modul & Materi</h2>
            <a href="materi_tambah.php" class="btn btn-success fw-bold">+ Upload Materi Baru</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul Materi</th>
                                <th>Deskripsi</th>
                                <th>File</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($koneksi, "SELECT * FROM materi ORDER BY id_materi DESC");

                            if(mysqli_num_rows($query) > 0){
                                while($row = mysqli_fetch_assoc($query)){
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td class='fw-bold'>{$row['judul']}</td>
                                        <td>{$row['deskripsi']}</td>
                                        <td>
                                            <a href='file_materi/{$row['nama_file']}' target='_blank' class='btn btn-sm btn-outline-primary'>
                                                📄 Download PDF
                                            </a>
                                        </td>
                                        <td>".date('d/m/Y', strtotime($row['tgl_upload']))."</td>
                                        <td>
                                            <a href='materi_hapus.php?id={$row['id_materi']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus materi ini?')\">Hapus</a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4'>Belum ada materi yang diupload.</td></tr>";
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
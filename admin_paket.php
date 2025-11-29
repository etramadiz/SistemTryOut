<?php
session_start();
include 'koneksi.php';

// Cek Login & Role Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Paket Tryout - Admin</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active fw-bold" href="admin_paket.php">Kelola Paket</a></li>
                <li class="nav-item"><a class="nav-link" href="data_user.php">Data User</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
            </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📦 Manajemen Paket Tryout</h2>
            <a href="paket_tambah.php" class="btn btn-success fw-bold">+ Tambah Paket Baru</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Nama Paket</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Ambil data paket join kategori
                            $query = mysqli_query($koneksi, "
                                SELECT p.*, k.nama_kategori 
                                FROM paket_tryout p 
                                JOIN kategori_ujian k ON p.id_kategori = k.id_kategori 
                                ORDER BY p.id_paket DESC
                            ");

                            if(mysqli_num_rows($query) > 0){
                                while($row = mysqli_fetch_assoc($query)){
                                    $status = ($row['status_publish'] == 1) 
                                        ? '<span class="badge bg-primary">Publish</span>' 
                                        : '<span class="badge bg-secondary">Draft</span>';
                                    
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td><span class='badge bg-info text-dark'>{$row['nama_kategori']}</span></td>
                                        <td class='fw-bold'>{$row['nama_paket']}</td>
                                        <td>{$row['durasi_menit']} Menit</td>
                                        <td>{$status}</td>
                                        <td>
                                            <a href='paket_edit.php?id={$row['id_paket']}' class='btn btn-sm btn-warning text-white'>Edit</a>
                                            <a href='paket_hapus.php?id={$row['id_paket']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus paket ini? Semua soal di dalamnya akan ikut terhapus.')\">Hapus</a>
                                            <a href='kelola_soal.php?id={$row['id_paket']}' class='btn btn-sm btn-primary'>Lihat Soal</a>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Belum ada paket ujian.</td></tr>";
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
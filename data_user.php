<?php
session_start();
include 'koneksi.php';

// Cek Admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data User</title>
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
                <li class="nav-item"><a class="nav-link active fw-bold" href="data_user.php">Data User</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
            </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">👥 Manajemen Pengguna</h2>
            </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY role ASC, nama_lengkap ASC");
                            
                            while($row = mysqli_fetch_assoc($query)) {
                                $badge = ($row['role'] == 'admin') ? 'bg-danger' : 'bg-success';
                                
                                echo "<tr>
                                    <td>{$no}</td>
                                    <td class='fw-bold'>{$row['nama_lengkap']}</td>
                                    <td>{$row['email']}</td>
                                    <td><span class='badge $badge'>".strtoupper($row['role'])."</span></td>
                                    <td>
                                        <a href='user_edit.php?id={$row['id_user']}' class='btn btn-sm btn-warning text-white'>Edit</a>
                                        
                                        "; 
                                        if($row['id_user'] != $_SESSION['id_user']){
                                            echo "<a href='user_hapus.php?id={$row['id_user']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus user ini? Riwayat ujiannya juga akan hilang.')\">Hapus</a>";
                                        }
                                echo "
                                    </td>
                                </tr>";
                                $no++;
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
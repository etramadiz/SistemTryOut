<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("location:login.php");
    die();
}
$nama_user = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna (User) Sistem Tryout</title>
    
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 TryoutOnline</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_paket.php">Kelola Paket</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_materi.php">Materi Belajar</a></li>
            <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
            <li class="nav-item">
                <a class="nav-link active fw-bold" aria-current="page" href="data_user.php">Kelola User</a>
            </li>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Hi, <?= explode(' ', $nama_user)[0] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profil.php">👤 Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">🚪 Logout</a></li>
                </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Data Pengguna (User)</h1>
        
        <a href="index.php" class="btn btn-secondary mb-3">← Kembali ke Beranda</a>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil semua data dari tabel user
                    $sql = "SELECT id_user, nama_lengkap, email, role, tanggal_daftar FROM user";
                    $result = mysqli_query($koneksi, $sql);

                    // Cek apakah ada data
                    if (mysqli_num_rows($result) > 0) {
                        // Output data dari setiap baris
                        while($row = mysqli_fetch_assoc($result)) {
                            $badge_color = ($row["role"] == 'admin') ? 'bg-danger' : 'bg-success';
                            
                            echo "<tr>";
                            echo "<td>" . $row["id_user"] . "</td>";
                            echo "<td>" . $row["nama_lengkap"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td><span class='badge " . $badge_color . "'>" . strtoupper($row["role"]) . "</span></td>";
                            echo "<td>" . $row["tanggal_daftar"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // Tampilkan alert jika tidak ada data
                        echo "<tr><td colspan='5'><div class='alert alert-warning' role='alert'>Tidak ada data pengguna</div></td></tr>";
                    }

                    // Tutup koneksi
                    mysqli_close($koneksi);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
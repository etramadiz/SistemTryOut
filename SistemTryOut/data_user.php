<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna (User) Sistem Tryout</title>
    
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <a class="navbar-brand" href="index.php">Sistem Tryout</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="data_user.php">Data User</a>
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
</body>
</html>
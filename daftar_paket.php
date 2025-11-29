<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Paket - Sistem Tryout</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 TryoutOnline</a>
        <div class="collapse navbar-collapse"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li></ul></div>
      </div>
    </nav>

    <div class="container mt-5">
        <h2 class="fw-bold mb-4">📚 Daftar Paket Tryout Tersedia</h2>
        
        <div class="row">
            <?php
            // Query mengambil paket + nama kategorinya
            $query = "SELECT p.*, k.nama_kategori 
                      FROM paket_tryout p 
                      JOIN kategori_ujian k ON p.id_kategori = k.id_kategori 
                      WHERE p.status_publish = 1";
            $result = mysqli_query($koneksi, $query);

            while($paket = mysqli_fetch_assoc($result)) {
                // Logika harga gratis/bayar
                $harga_display = ($paket['harga'] == 0) ? '<span class="badge bg-success">GRATIS</span>' : '<span class="badge bg-warning text-dark">Rp '.number_format($paket['harga']).'</span>';
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-0 pt-4">
                            <span class="badge bg-info text-dark mb-2"><?= $paket['nama_kategori'] ?></span>
                            <h4 class="card-title fw-bold"><?= $paket['nama_paket'] ?></h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small"><?= $paket['deskripsi'] ?></p>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>⏱ Durasi:</span>
                                <span class="fw-bold"><?= $paket['durasi_menit'] ?> Menit</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>🏷 Harga:</span>
                                <?= $harga_display ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pb-4">
                            <a href="detail_paket.php?id=<?= $paket['id_paket'] ?>" class="btn btn-primary w-100 fw-bold">Lihat Detail & Kerjakan</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
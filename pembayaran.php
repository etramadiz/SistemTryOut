<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

$id_paket = $_GET['id'];
$id_user = $_SESSION['id_user'];

// Ambil Detail Paket
$query = mysqli_query($koneksi, "SELECT * FROM paket_tryout WHERE id_paket='$id_paket'");
$paket = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - <?= $paket['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">🎓 TryoutOnline</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h4 class="mb-3 fw-bold">Metode Pembayaran</h4>
                
                <form action="proses_beli.php" method="POST">
                    <input type="hidden" name="id_paket" value="<?= $paket['id_paket'] ?>">
                    
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Item yang dibeli:</h6>
                            <h5 class="fw-bold"><?= $paket['nama_paket'] ?></h5>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Total Tagihan:</span>
                                <span class="fw-bold text-success fs-5">Rp <?= number_format($paket['harga']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold">Pilih E-Wallet / Bank</div>
                        <div class="list-group list-group-flush">
                            
                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                <div>
                                    <input class="form-check-input me-2" type="radio" name="metode" value="ShopeePay" required checked>
                                    <span class="fw-bold text-warning">ShopeePay</span>
                                </div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg" height="20" alt="Shopee">
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <input class="form-check-input me-2" type="radio" name="metode" value="DANA">
                                    <span class="fw-bold text-primary">DANA</span>
                                </div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" height="20" alt="DANA">
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <input class="form-check-input me-2" type="radio" name="metode" value="GoPay">
                                    <span class="fw-bold text-success">GoPay</span>
                                </div>
                                <span class="badge bg-success">GOPAY</span>
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <input class="form-check-input me-2" type="radio" name="metode" value="Transfer Bank">
                                    <span class="fw-bold text-dark">Transfer Bank (BCA/Mandiri)</span>
                                </div>
                                <span class="badge bg-secondary">BANK</span>
                            </label>

                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="bayar" class="btn btn-primary btn-lg fw-bold">Bayar Sekarang</button>
                        <a href="detail_paket.php?id=<?= $id_paket ?>" class="btn btn-link text-muted">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>
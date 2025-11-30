<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    die();
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
$role = $_SESSION['role'];
$id_paket = $_GET['id'];

// --- LOGIKA KIRIM KOMENTAR ---
if(isset($_POST['kirim_komentar'])){
    $id_soal = $_POST['id_soal'];
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    mysqli_query($koneksi, "INSERT INTO diskusi_soal (id_user, id_soal, isi_komentar) VALUES ('$id_user', '$id_soal', '$isi')");
}

// --- LOGIKA HAPUS KOMENTAR ---
if(isset($_GET['hapus_komentar'])){
    $id_kom = $_GET['hapus_komentar'];
    // Cek apakah komentar ini milik user yang sedang login (Security Check)
    $cek_milik = mysqli_query($koneksi, "SELECT * FROM diskusi_soal WHERE id_komentar='$id_kom' AND id_user='$id_user'");
    
    if(mysqli_num_rows($cek_milik) > 0){
        mysqli_query($koneksi, "DELETE FROM diskusi_soal WHERE id_komentar='$id_kom'");
        echo "<script>alert('Komentar dihapus!'); window.location='diskusi.php?id=$id_paket';</script>";
    }
}

// Ambil Soal-soal
$soal_list = mysqli_query($koneksi, "SELECT s.* FROM paket_soal ps JOIN soal s ON ps.id_soal = s.id_soal WHERE ps.id_paket='$id_paket'");
$info_paket = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_paket FROM paket_tryout WHERE id_paket='$id_paket'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Diskusi - <?= $info_paket['nama_paket'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🎓 TryoutOnline</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
            <?php if($role == 'peserta'): ?>
                <li class="nav-item"><a class="nav-link" href="daftar_paket.php">Daftar Tryout</a></li>
                <li class="nav-item"><a class="nav-link" href="riwayat.php">Riwayat</a></li>
            <?php endif; ?>
            <li class="nav-item dropdown ms-2">
                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" data-bs-toggle="dropdown">Hi, <?= explode(' ', $nama_user)[0] ?></a>
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

    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0">💬 Diskusi Soal</h3>
                <small class="text-muted"><?= $info_paket['nama_paket'] ?></small>
            </div>
            <a href="riwayat.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <?php $no=1; while($s = mysqli_fetch_assoc($soal_list)): ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-bottom">
                    Soal No. <?= $no++ ?>
                </div>
                <div class="card-body">
                    <p class="fs-5 mb-3"><?= $s['pertanyaan'] ?></p>
                    
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-chat-text"></i> Komentar</h6>

                    <div class="list-group mb-3">
                        <?php
                        $komen = mysqli_query($koneksi, "SELECT d.*, u.nama_lengkap FROM diskusi_soal d JOIN user u ON d.id_user=u.id_user WHERE id_soal='".$s['id_soal']."' ORDER BY d.tanggal_posting ASC");
                        
                        if(mysqli_num_rows($komen) > 0){
                            while($k = mysqli_fetch_assoc($komen)){
                                $tgl = date('d M, H:i', strtotime($k['tanggal_posting']));
                                // Cek apakah ini komentar user yang sedang login?
                                $is_me = ($k['id_user'] == $id_user);
                                $bg_class = $is_me ? "bg-primary bg-opacity-10 border-primary" : "bg-light";
                        ?>
                            <div class="list-group-item <?= $bg_class ?> border-0 mb-2 rounded-3 p-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold <?= $is_me ? 'text-primary' : 'text-dark' ?>">
                                        <?= $k['nama_lengkap'] ?> <?= $is_me ? '(Saya)' : '' ?>
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?= $tgl ?></small>
                                </div>
                                <p class="mb-1 mt-1 text-secondary"><?= $k['isi_komentar'] ?></p>
                                
                                <?php if($is_me): ?>
                                    <div class="mt-2 text-end">
                                        <a href="edit_komentar.php?id=<?= $k['id_komentar'] ?>&id_paket=<?= $id_paket ?>" class="text-decoration-none small text-warning me-2 fw-bold">Edit</a>
                                        <a href="diskusi.php?id=<?= $id_paket ?>&hapus_komentar=<?= $k['id_komentar'] ?>" class="text-decoration-none small text-danger fw-bold" onclick="return confirm('Hapus komentar ini?')">Hapus</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<div class='text-muted small text-center py-3'>Belum ada diskusi.</div>";
                        }
                        ?>
                    </div>

                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="id_soal" value="<?= $s['id_soal'] ?>">
                        <input type="text" name="isi" class="form-control rounded-pill px-3" placeholder="Tulis pertanyaan atau balasan..." required>
                        <button class="btn btn-primary rounded-pill px-4" type="submit" name="kirim_komentar"><i class="bi bi-send-fill"></i></button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

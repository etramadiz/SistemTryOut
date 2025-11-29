<?php
session_start();
include 'koneksi.php';
$id_paket = $_GET['id'];

// Proses kirim komentar
if(isset($_POST['kirim_komentar'])){
    $id_soal = $_POST['id_soal'];
    $isi = $_POST['isi'];
    $user = $_SESSION['id_user'];
    mysqli_query($koneksi, "INSERT INTO diskusi_soal (id_user, id_soal, isi_komentar) VALUES ('$user', '$id_soal', '$isi')");
}

$soal_list = mysqli_query($koneksi, "SELECT s.* FROM paket_soal ps JOIN soal s ON ps.id_soal = s.id_soal WHERE ps.id_paket='$id_paket'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Diskusi & Pembahasan</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2>💬 Diskusi & Pembahasan</h2>
        <a href="index.php">Kembali</a>
        <hr>

        <?php $no=1; while($s = mysqli_fetch_assoc($soal_list)): ?>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Soal No. <?= $no++ ?> (Kunci: <b><?= $s['kunci_jawaban'] ?></b>)</div>
                <div class="card-body">
                    <p><?= $s['pertanyaan'] ?></p>
                    <div class="alert alert-warning p-2"><small><b>Pembahasan:</b> <?= $s['teks_pembahasan'] ?? 'Belum ada pembahasan.' ?></small></div>
                    
                    <h6>Komentar:</h6>
                    <ul class="list-group mb-3">
                        <?php
                        $komen = mysqli_query($koneksi, "SELECT d.*, u.nama_lengkap FROM diskusi_soal d JOIN user u ON d.id_user=u.id_user WHERE id_soal='".$s['id_soal']."'");
                        while($k = mysqli_fetch_assoc($komen)){
                            echo "<li class='list-group-item py-1'><small><b>".$k['nama_lengkap'].":</b> ".$k['isi_komentar']."</small></li>";
                        }
                        ?>
                    </ul>

                    <form method="POST">
                        <input type="hidden" name="id_soal" value="<?= $s['id_soal'] ?>">
                        <div class="input-group input-group-sm">
                            <input type="text" name="isi" class="form-control" placeholder="Tulis pertanyaan/diskusi..." required>
                            <button class="btn btn-secondary" type="submit" name="kirim_komentar">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
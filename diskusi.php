<?php
session_start();
include 'koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status'])) { header("location:login.php"); exit; }

$id_paket = $_GET['id'];
$id_user = $_SESSION['id_user'];
$role = $_SESSION['role']; // Ambil Role User

// 2. [KEAMANAN] Cek Hak Akses
// Logika: User boleh masuk JIKA (Sudah Selesai Ujian) ATAU (Dia adalah Admin)
$cek_hak_akses = mysqli_query($koneksi, "
    SELECT * FROM percobaan_tryout 
    WHERE id_user='$id_user' 
    AND id_paket='$id_paket' 
    AND status_pengerjaan='SELESAI'
");

// Jika TIDAK ditemukan data selesai DAN bukan Admin, maka tolak.
if (mysqli_num_rows($cek_hak_akses) == 0 && $role != 'admin') {
    echo "<script>
            alert('Akses Ditolak! Anda harus menyelesaikan ujian ini terlebih dahulu untuk melihat pembahasan.');
            window.location='detail_paket.php?id=$id_paket';
          </script>";
    exit; // Stop script
}

// 3. Proses Kirim Komentar
if(isset($_POST['kirim_komentar'])){
    $id_soal = $_POST['id_soal'];
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    
    // Insert komentar
    mysqli_query($koneksi, "INSERT INTO diskusi_soal (id_user, id_soal, isi_komentar) VALUES ('$id_user', '$id_soal', '$isi')");
    
    // Refresh halaman biar komentar muncul
    header("Location: diskusi.php?id=$id_paket");
}

// 4. Ambil Data Soal & Info Paket
// [REVISI: Tidak perlu JOIN ke tabel 'soal' lagi karena tabelnya sudah dihapus]
$soal_list = mysqli_query($koneksi, "SELECT * FROM paket_soal WHERE id_paket='$id_paket' ORDER BY id_soal ASC");

$paket_info = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_paket FROM paket_tryout WHERE id_paket='$id_paket'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Diskusi - <?= $paket_info['nama_paket'] ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        .komentar-box { background-color: #f8f9fa; border-radius: 10px; padding: 15px; }
        .user-name { font-weight: bold; color: #0d6efd; }
        .time-stamp { font-size: 0.8rem; color: #6c757d; }
    </style>
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4 sticky-top">
        <div class="container">
            <span class="navbar-brand fw-bold fs-5">💬 Diskusi: <?= $paket_info['nama_paket'] ?></span>
            <a href="detail_paket.php?id=<?= $id_paket ?>" class="btn btn-sm btn-light text-primary fw-bold">Kembali</a>
        </div>
    </nav>

    <div class="container pb-5">
        <?php 
        $no=1; 
        while($s = mysqli_fetch_assoc($soal_list)): 
            // Pastikan kolom gambar ada (handle error jika kolom gambar blm ada di paket_soal)
            $gambar = isset($s['gambar']) ? $s['gambar'] : null;
        ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
                    <span>Soal No. <?= $no++ ?></span>
                    <span class="badge bg-success rounded-pill px-3">Kunci: <?= $s['kunci_jawaban'] ?></span>
                </div>
                <div class="card-body">
                    <p class="fs-5 mb-3"><?= $s['pertanyaan'] ?></p>
                    
                    <?php if(!empty($gambar)): ?>
                        <img src="foto_soal/<?= $gambar ?>" class="img-fluid mb-3 rounded" style="max-height: 200px;">
                    <?php endif; ?>
                    
                    <ul class="list-group list-group-flush mb-3 border rounded">
                        <li class="list-group-item <?php if($s['kunci_jawaban']=='A') echo 'bg-success text-white'; ?>">A. <?= $s['opsi_a'] ?></li>
                        <li class="list-group-item <?php if($s['kunci_jawaban']=='B') echo 'bg-success text-white'; ?>">B. <?= $s['opsi_b'] ?></li>
                        <li class="list-group-item <?php if($s['kunci_jawaban']=='C') echo 'bg-success text-white'; ?>">C. <?= $s['opsi_c'] ?></li>
                        <li class="list-group-item <?php if($s['kunci_jawaban']=='D') echo 'bg-success text-white'; ?>">D. <?= $s['opsi_d'] ?></li>
                        <?php if(!empty($s['opsi_e'])): ?>
                            <li class="list-group-item <?php if($s['kunci_jawaban']=='E') echo 'bg-success text-white'; ?>">E. <?= $s['opsi_e'] ?></li>
                        <?php endif; ?>
                    </ul>

                    <div class="alert alert-info border-0 d-flex align-items-start">
                        <span class="fs-4 me-2">💡</span>
                        <div>
                            <strong>Pembahasan:</strong><br>
                            <?= isset($s['teks_pembahasan']) ? $s['teks_pembahasan'] : 'Belum ada pembahasan untuk soal ini.' ?>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-secondary mb-3">Diskusi Peserta:</h6>
                    <div class="komentar-box mb-3">
                        <?php
                        // Ambil ID Soal dari paket_soal
                        $id_soal_curr = $s['id_soal']; // Sesuaikan nama kolom ID di tabel paket_soal kamu
                        
                        $komen = mysqli_query($koneksi, "
                            SELECT d.*, u.nama_lengkap, u.role 
                            FROM diskusi_soal d 
                            JOIN user u ON d.id_user=u.id_user 
                            WHERE id_soal='$id_soal_curr'
                            ORDER BY tanggal_posting ASC
                        ");
                        
                        if(mysqli_num_rows($komen) > 0){
                            while($k = mysqli_fetch_assoc($komen)){
                                $badge_role = ($k['role'] == 'admin') ? '<span class="badge bg-danger ms-1" style="font-size:0.6rem">ADMIN</span>' : '';
                                $hapus_btn = ($k['id_user'] == $id_user || $role == 'admin') ? "<a href='hapus_komentar.php?id=".$k['id_komentar']."&paket=$id_paket' class='text-danger ms-2 text-decoration-none' style='font-size:0.8rem' onclick=\"return confirm('Hapus komentar?')\">[Hapus]</a>" : "";
                                
                                echo "<div class='mb-3 border-bottom pb-2'>
                                        <div class='d-flex justify-content-between'>
                                            <div>
                                                <span class='user-name'>".$k['nama_lengkap']."</span> $badge_role
                                                <span class='time-stamp ms-2'>".date('d/m H:i', strtotime($k['tanggal_posting']))."</span>
                                            </div>
                                            <div>$hapus_btn</div>
                                        </div>
                                        <div class='mt-1 text-dark'>".nl2br(htmlspecialchars($k['isi_komentar']))."</div>
                                      </div>";
                            }
                        } else {
                            echo "<div class='text-center text-muted py-2'>Belum ada pertanyaan/diskusi. Jadilah yang pertama bertanya!</div>";
                        }
                        ?>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="id_soal" value="<?= $s['id_soal'] ?>">
                        <div class="input-group">
                            <input type="text" name="isi" class="form-control" placeholder="Tulis pertanyaan atau diskusi di sini..." required autocomplete="off">
                            <button class="btn btn-primary fw-bold" type="submit" name="kirim_komentar">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'koneksi.php';

// Ambil ID Paket
$id_paket = $_GET['paket'];

// Ambil Nama Paket untuk Nama File
$q_info = mysqli_query($koneksi, "SELECT nama_paket FROM paket_tryout WHERE id_paket='$id_paket'");
$info = mysqli_fetch_assoc($q_info);
$nama_file = "Laporan_Nilai_" . str_replace(" ", "_", $info['nama_paket']) . ".xls";

// --- HEADER AJAIB PENGUBAH HTML JADI EXCEL ---
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$nama_file");
?>

<h3>Laporan Nilai: <?= $info['nama_paket'] ?></h3>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Peserta</th>
            <th>Email</th>
            <th>Waktu Mulai</th>
            <th>Skor Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($koneksi, "
            SELECT u.nama_lengkap, u.email, pt.waktu_mulai, pt.skor_total
            FROM percobaan_tryout pt
            JOIN user u ON pt.id_user = u.id_user
            WHERE pt.id_paket = '$id_paket' AND pt.status_pengerjaan = 'SELESAI'
            ORDER BY pt.skor_total DESC
        ");

        $no = 1;
        while($row = mysqli_fetch_assoc($query)){
            echo "<tr>
                <td>$no</td>
                <td>{$row['nama_lengkap']}</td>
                <td>{$row['email']}</td>
                <td>{$row['waktu_mulai']}</td>
                <td>{$row['skor_total']}</td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>


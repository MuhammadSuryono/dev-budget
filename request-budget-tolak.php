<?php

session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$id = $_GET['id'];
$alasan = ($_GET['alasan']);

$queryGetData = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id");
$data = mysqli_fetch_assoc($queryGetData);
$waktu = $data['waktu'];
$pembuat = $data['pembuat'];
$pengaju = $data['pengaju'];
$namaProject = $data['nama'];
$divisi = $data['divisi'];
$totalbudget = $data['totalbudget'];

$updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET status_request='Ditolak', waktu='$waktu', declined_note='$alasan' WHERE waktu='$waktu'") or die(mysqli_error($koneksi));

$queryGetAllId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request WHERE waktu='$waktu'");
while ($row = mysqli_fetch_array($queryGetAllId)) {
    $id = $row['id'];
    $updateSelesaiRequest = mysqli_query($koneksi, "UPDATE selesai_request SET waktu='$waktu' WHERE id_pengajuan_request=$id");
}
if ($updatePengajuanRequest) {;

    $email = [];
    $nama = [];
    $queryGetEmail = mysqli_query($koneksi, "SELECT email,divisi,nama_user from tb_user WHERE nama_user='$pengaju' AND aktif='Y'");
    $data = mysqli_fetch_assoc($queryGetEmail);
    $divisi = $data['divisi'];
    array_push($email, $data['email']);
    array_push($nama, $data['nama_user']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $msg = "Dear $pengaju, <br><br>
        Budget dengan keterangan berikut:<br><br>
        Nama Project    : <strong>$namaProject</strong><br>
        Pengaju         : <strong>$pengaju</strong><br>
        Divisi          : <strong>$divisi</strong><br>
        Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br><br>
        
        Telah Ditolak oleh <strong> $pembuat </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong> dengan keterangan <strong>$alasan</strong><br><br>
        ";

    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";

    $subject = "Notifikasi Untuk Penolakan Budget";
    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    $notification = 'Budget Berhasil Ditolak. Pemberitahuan via email telah terkirim ke ';
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }

    // $notification = "Budget Berhasil Ditolak. Pemberitahuan via email telah terkirim ke $pengaju ($email)";

    if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    }
} else {

    if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Budget Gagal Ditolak')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Budget Gagal Ditolak')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    }
}

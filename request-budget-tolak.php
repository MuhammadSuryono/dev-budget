<?php

session_start();
require "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";

$wa = new Whastapp();
$messageHelpepr = new Message();

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

    $phoneNumber = [];
    $nama = [];
    $queryGetEmail = mysqli_query($koneksi, "SELECT * from tb_user WHERE nama_user='$pengaju' AND aktif='Y'");
    $data = mysqli_fetch_assoc($queryGetEmail);
    $divisi = $data['divisi'];
    array_push($phoneNumber, $data['phone_number']);
    array_push($nama, $data['nama_user']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    array_push($phoneNumber, $user['phone_number']);
    array_push($nama, $user['nama_user']);

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $message = $messageHelpepr->messageTolakPengajuanBudget($pengaju, $namaProject, $divisi, $totalbudget, $_SESSION['nama_user'], $alasan);

    $notification = 'Budget Berhasil Ditolak. Pemberitahuan via whatsapp telah terkirim ke ';
    for ($i = 0; $i < count($phoneNumber); $i++) {
        $notification .= ($nama[$i] . ' (' . $phoneNumber[$i] . ')');

        if ($phoneNumber[$i] != "") {
            $wa->sendMessage($phoneNumber[$i], $message);
        }

        if ($i < count($phoneNumber) - 1) $notification .= ', ';
        else $notification .= '.';
    }

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

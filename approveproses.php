<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$noid        = $_POST['noid'];
$totalbudget = $_POST['totalbudget'];
$penyetuju   = $_SESSION['nama_user'];

if ($_SESSION['divisi'] == 'Direksi') {
  $baliknya = "list-direksi.php";
} else {
  $baliknya = "list-finance-budewi.php";
}


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $queryGetPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$noid'");
  $pengajuan = mysqli_fetch_assoc($queryGetPengajuan);
  $time = $pengajuan['waktu'];
  $pengaju = $pengajuan['pengaju'];
  $pembuat = $pengajuan['pembuat'];
  $namaProject = $pengajuan['nama'];
  $divisi = $pengajuan['divisi'];
  $totalbudget = $pengajuan['totalbudget'];

  $update = mysqli_query($koneksi, "UPDATE pengajuan SET status = 'Disetujui', totalbudgetnow = '$totalbudget', penyetuju = '$penyetuju', on_revision_status = '0' WHERE waktu ='$time'");

  if ($update) {

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $email = [];
    $nama = [];
    $querUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user='$pengaju' AND aktif='Y'");
    $user = mysqli_fetch_assoc($querUser);
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = $user[divisi] AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);


    $msg = "Dear $pengaju, <br><br>
        Budget dengan keterangan berikut:<br><br>
        Nama Project    : <strong>$namaProject</strong><br>
        Pengaju         : <strong>$pengaju</strong><br>
        Divisi          : <strong>$divisi</strong><br>
        Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br><br>
        
        Telah disetujui oleh <strong> $pembuat </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong><br><br>
        ";

    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Untuk Penyetujuan Budget";
    if ($email) {
      $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    // $notifikasi = "Persetujuan Budget Berhasil, Budget Telah Disetujui. Pemberitahuan via email telah terkirim ke $pengaju ($email)";
    $notifikasi = 'Persetujuan Budget Berhasil, Budget Telah Disetujui. Pemberitahuan via email telah terkirim ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
      $notifikasi .= ($nama[$i] . ' (' . $email[$i] . ')');
      if ($i < count($email) - 1) $notifikasi .= ', ';
      else $notifikasi .= '.';
    }

    $queryDocument = mysqli_query($koneksi, "SELECT noid, document FROM pengajuan WHERE waktu = '$time' ORDER BY noid DESC LIMIT 1");
    $document = mysqli_fetch_assoc($queryDocument);
    $doc = unserialize($document['document']);
    $noid = $document['noid'];

    if (is_array($doc)) {
      $name = $doc[count($doc) - 1];
    } else {
      $name = $doc;
    }

    saveDocApproved($koneksi, $noid, $name);

    echo "<script language='javascript'>";
    echo "alert('$notifikasi')";
    echo "</script>";
    echo "<script> document.location.href='$baliknya'; </script>";
  } else {
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Gagal')";
    echo "</script>";
    echo "<script> document.location.href='$baliknya'; </script>";
  }
}

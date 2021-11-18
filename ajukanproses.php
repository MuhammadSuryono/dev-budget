<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$noid   = $_GET['id'];
$ket = $_GET['ket'];

$queryGetPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$noid'");
$pengajuan = mysqli_fetch_assoc($queryGetPengajuan);
$time = $pengajuan['waktu'];
$pembuat = $pengajuan['pembuat'];
$pengaju = $pengajuan['pengaju'];
$namaProject = $pengajuan['nama'];
$divisi = $pengajuan['divisi'];
$totalbudget = $pengajuan['totalbudget'];

$queryGetEmail = mysqli_query($koneksi, "SELECT email from tb_user WHERE nama_user='$pembuat' AND aktif='Y'");
$email = mysqli_fetch_array($queryGetEmail)[0];

$name = random_bytes(15);
if (@unserialize($pengajuan['document'])) {

  $document = unserialize($pengajuan['document']);
  if (is_array($document)) {
    $arrDocument = [];
    foreach ($document as $d) {
      array_push($arrDocument, $d);
    }
    array_push($arrDocument, $name);
    $document = serialize($arrDocument);
  } else {
    $arrDocument = [$document];
    array_push($arrDocument, $name);
    $document = serialize($arrDocument);
  }
} else {
  $document = serialize($name);
}

$update = mysqli_query($koneksi, "UPDATE pengajuan SET status='Pending', document='$document',submission_note='$ket',on_revision_status='1' WHERE waktu='$time'");
saveDocBudget($koneksi, $noid, $name);

if ($update) {

  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $url = explode('/', $url);
  $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

  $msg = "Dear $pembuat, <br><br>
    Budget telah diajukan dengan keterangan sebagai berikut:<br><br>
    Nama Project    : <strong>$namaProject</strong><br>
    Pengaju         : <strong>$pengaju</strong><br>
    Divisi          : <strong>$divisi</strong><br>
    Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br>
    ";
  if ($ket) {
    $msg .= "Keterangan:<strong> $ket </strong><br><br>";
  } else {
    $msg .= "<br>";
  }
  $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
  // $msg .= "Regards, <br> $pengaju";
  $subject = "Notifikasi Untuk Pengajuan Budget";
  if ($email) {
    $message = sendEmail($msg, $subject, $email, $name);
  }

  $notifikasi = "Pengajuan Telah Di Ajukan, Status Berubah Menjadi Pending. Pemberitahuan via email telah terkirim ke $pembuat ($email)";

  echo "<script language='javascript'>";
  echo "alert('$notifikasi')";
  echo "</script>";
  echo "<script> document.location.href='list.php'; </script>";
} else {
  echo "<script language='javascript'>";
  echo "alert('Pengajuan Gagal Di Ajukan')";
  echo "</script>";
  echo "<script> document.location.href='view-disapprove.php?code=$noid'; </script>";
}


function random_bytes($length = 6)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $characters_length = strlen($characters);
  $output = '';
  for ($i = 0; $i < $length; $i++)
    $output .= $characters[rand(0, $characters_length - 1)];
  return $output;
}

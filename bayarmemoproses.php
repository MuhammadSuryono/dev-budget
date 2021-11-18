<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$pembayar     = $_POST['pembayar'];
$divisi       = $_POST['divisi'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $selbay = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $s = mysqli_fetch_assoc($selbay);
  $noid = $s['noid'];

  $sellagi = mysqli_query($koneksi, "SELECT MAX(noid) FROM upload WHERE waktu='$waktu'");
  $sel = mysqli_fetch_assoc($sellagi);
  $nomax = $sel['MAX(noid)'];

  $update = mysqli_query($koneksi, "UPDATE upload SET status ='Telah Dibayar', pembayar='$pembayar', divpemb='$divisi' WHERE waktu='$waktu' AND no='$no' AND noid='$nomax'");
}

if ($update) {
  echo "<script language='javascript'>";
  echo "alert('Pembayaran Memo Berhasil')";
  echo "</script>";
  echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
} else {
  echo "Pembayaran Memo Gagal";
}

<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no               = $_POST['no'];
$waktu            = $_POST['waktu'];
$term             = $_POST['term'];
$realisasi        = $_POST['realisasi'];
$uangkembali      = $_POST['uangkembali'];
$tanggalrealisasi = $_POST['tanggalrealisasi'];
$status           = $_POST['status'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $update = mysqli_query($koneksi, "UPDATE bpu SET realisasi ='$realisasi',
                                          uangkembali ='$uangkembali',
                                          tanggalrealisasi ='$tanggalrealisasi',
                                          status ='$status' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

  //jika sudah berhasil
  if ($update) {
    $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];
    echo "<script language='javascript'>";
    echo "alert('Realisasi Berhasil!!')";
    echo "</script>";
    echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
  } else {
    echo "Edit Budget Gagal";
  }
}

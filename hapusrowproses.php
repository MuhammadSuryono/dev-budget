<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiBridge = $con->connect();

$no     = $_POST['no'];
$waktu  = $_POST['waktu'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $update = mysqli_query($koneksi, "DELETE FROM selesai WHERE no ='$no' AND waktu ='$waktu'");

  $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no ='$no' AND waktu ='$waktu'");
  while ($dataBpu = mysqli_fetch_assoc($queryBpu)) {
    $idBpu = $dataBpu['noid'];
    $delete = mysqli_query($koneksiBridge, "DELETE FROM data_transfer WHERE noid_bpu ='$idBpu'");
  }


  if ($update) {
    $hitung = mysqli_query($koneksi, "SELECT sum(total) AS sumi FROM selesai WHERE waktu='$waktu'");
    $ht = mysqli_fetch_array($hitung);
    $total = $ht['sumi'];

    $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
    $countData = mysqli_fetch_array($queryCountData)[0];

    $total /= $countData;

    $update2 = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget='$total' WHERE waktu='$waktu'");
  }


  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];

  //jika sudah berhasil
  if ($update2) {
    echo "<script language='javascript'>";
    echo "alert('Item Budget Telah Di Hapus')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {
    echo "Edit Budget Gagal";
  }
}

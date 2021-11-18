<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$id_user    = $_POST['id_user'];
$jabatan    = $_POST['jabatan'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  if ($jabatan == 'Koordinator') {
    $limit = "5000000";
  } else if ($jabatan == 'Supervisor') {
    $limit = "10000000";
  } else if ($jabatan == 'Senior Supervisor') {
    $limit = "15000000";
  } else if ($jabatan == 'ARA') {
    $limit = "10000000";
  } else if ($jabatan == 'RA') {
    $limit = "15000000";
  } else if ($jabatan == 'Senior RA') {
    $limit = "20000000";
  } else if ($jabatan == 'Associate Manager') {
    $limit = "25000000";
  } else if ($jabatan == 'Manager') {
    $limit = "35000000";
  } else if ($jabatan == 'Senior Manager') {
    $limit = "50000000";
  } else if ($jabatan == 'Associate Director') {
    $limit = "60000000";
  }

  $update = mysqli_query($koneksi, "UPDATE tb_user SET level='$jabatan', saldo='$limit' WHERE id_user='$id_user'");

  if ($update) {
    echo "<script language='javascript'>";
    echo "alert('Input Jabatan Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='saldobpu.php'; </script>";
  }
}

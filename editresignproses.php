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
$resign     = $_POST['resign'];


//periksa apakah udah submit
if (isset($_POST['submit']) && $id_user != null) {
  $getUser = mysqli_query($koneksi, "SELECT nama_user FROM tb_user WHERE id_user='$id_user'");
  $user = mysqli_fetch_assoc($getUser)['nama_user'];

  $getUm = mysqli_query($koneksi, "SELECT SUM(jumlah) AS jumlah FROM bpu WHERE namapenerima='$user' AND status !='Realisasi (Direksi)' AND statusbpu IN ('UM', 'UM Burek')");
  $um = (int)mysqli_fetch_assoc($getUm)['jumlah'];
  if ($um) {
    echo "<script language='javascript'>";
    echo "alert('Edit Data Gagal! $user masih mempunyai Oustanding UM!')";
    echo "</script>";
    echo "<script> document.location.href='saldobpu.php'; </script>";
  } else {
    var_dump($id_user);
    $update = mysqli_query($koneksi, "UPDATE tb_user SET resign='$resign', aktif='N' WHERE id_user='$id_user'");

    if ($update) {
      echo "<script language='javascript'>";
      echo "alert('Edit Data Berhasil!!')";
      echo "</script>";
      echo "<script> document.location.href='saldobpu.php'; </script>";
    }
  }
} else {
  echo "<script language='javascript'>";
  echo "alert('Edit Data Gagal! Silahkan Pilih User!')";
  echo "</script>";
  echo "<script> document.location.href='saldobpu.php'; </script>";
}

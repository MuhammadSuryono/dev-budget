<?php
//error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiBridge = $con->connect();

$no     = $_POST['no'];
$waktu  = $_POST['waktu'];
$term   = $_POST['term'];

$aksesSes = $_SESSION['hak_akses'];

$sel1 = mysqli_query($koneksi, "SELECT noid,jenis FROM pengajuan WHERE waktu='$waktu'");
$uc = mysqli_fetch_assoc($sel1);
$numb = $uc['noid'];
$jenis = $uc['jenis'];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no ='$no' AND waktu ='$waktu' AND term ='$term'");
$dataBpu = mysqli_fetch_assoc($queryBpu);

if ($uc['jenis'] == 'Non Rutin') {
  $isNonRutin = '-nonrutin';
} else {
  $isNonRutin = '';
}

$idBpu = $dataBpu['noid'];

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $update = mysqli_query($koneksi, "DELETE FROM bpu WHERE no ='$no' AND waktu ='$waktu' AND term ='$term'");
  $delete = mysqli_query($koneksiBridge, "DELETE FROM data_transfer WHERE noid_bpu ='$idBpu'");

  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];

  //jika sudah berhasil
  if ($update) {
    if ($_SESSION['divisi'] == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('BPU Berhasil di hapus.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('BPU Berhasil di hapus.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "alert('BPU Berhasil di hapus.')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  } else {
    if ($_SESSION['divisi'] == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('BPU Gagal di hapus.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('BPU Gagal di hapus.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "alert('BPU Gagal di hapus.')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  }
}

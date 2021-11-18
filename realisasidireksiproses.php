<?php
//error_reporting(0);

session_start();
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

$aksesSes = $_SESSION['hak_akses'];

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $cekreal = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
  $sr = mysqli_fetch_assoc($cekreal);
  $stt = $sr['status'];
  $jb = $sr['jumlahbayar'];
  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];


  if ($stt == 'Realisasi (Direksi)' or $stt == 'Realisasi (Finance)') {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! BPU sudah di realiasisai. Tidak bisa Realisasi dua kali')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
  } else if ($stt == 'Belum Di Bayar') {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! BPU belum di Bayar !!')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
  } else if ($realisasi > $jb) {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! Realisasi Lebih Besar Dari Jumlah Pembayaran !!')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
  } else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET realisasi ='$realisasi', uangkembali ='$uangkembali',
                                              tanggalrealisasi ='$tanggalrealisasi',
                                              status ='$status' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

    //jika sudah berhasil
    if ($update) {
      echo "<script language='javascript'>";
      echo "alert('Realisasi Budget Berhasil')";
      echo "</script>";
      if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
      else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
    }
  }
}

<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no        = $_POST['no'];
$waktu     = $_POST['waktu'];
$rincian   = $_POST['rincian'];
$kota      = $_POST['kota'];
$status    = $_POST['status'];
$penerima  = $_POST['penerima'];
$harga     = $_POST['harga'];
$quantity  = $_POST['quantity'];
$total     = $_POST['total'];

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $checktotal = mysqli_query($koneksi, "SELECT sum(total) AS sumt FROM selesai WHERE waktu='$waktu' AND no !='$no'");
  $st = mysqli_fetch_array($checktotal);
  $tn = $st['sumt'];
  $jadi = $total + $tn;

  $cekbudgettotal = mysqli_query($koneksi, "SELECT totalbudgetnow FROM pengajuan WHERE waktu='$waktu'");
  $cb = mysqli_fetch_assoc($cekbudgettotal);
  $totbud = $cb['totalbudgetnow'];

  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];

  if ($jadi > $totbud) {
    echo "<script language='javascript'>";
    echo "alert('GAGAL!! , Total Budget Lebih Besar Dari Yang Disetujui')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {

    $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                              total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
  }

  //jika sudah berhasil
  if ($update) {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);

    $totaljadi = $total = $row[0];

    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");
  }

  if ($updatetotal) {
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {
    echo "Edit Budget Gagal";
  }
}

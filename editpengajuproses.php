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

  $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',                                             total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");

  //jika sudah berhasil
  if ($update && $status != 'UM Burek') {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu' AND status <> 'UM Burek'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);

    $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
    $countData = mysqli_fetch_array($queryCountData)[0];

    $totaljadi = $total = $row[0];
    $totaljadi /= $countData;
    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");

    $sel1 = mysqli_query($koneksi, "SELECT noid,status FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];
    $statuspeng = $uc['status'];
  } else if ($update && $status == 'UM Burek') {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);

    $totaljadi =  $row[0] - $total;
    var_dump($totaljadi);
    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");

    $sel1 = mysqli_query($koneksi, "SELECT noid,status FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];
    $statuspeng = $uc['status'];
  }

  if ($updatetotal && $statuspeng == 'Disapprove') {
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='view-disapprove.php?code=" . $numb . "'; </script>";
  } else {
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='view.php?code=" . $numb . "'; </script>";
  }
}

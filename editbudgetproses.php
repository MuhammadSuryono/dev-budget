<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no       = $_POST['no'];
$waktu    = $_POST['waktu'];
$penerima = $_POST['penerima'];
$harga    = $_POST['harga'];
$quantity = $_POST['quantity'];
$total    = $_POST['total'];
$komentar = $_POST['komentar'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $update = mysqli_query($koneksi, "UPDATE selesai SET penerima ='$penerima',
                                              harga ='$harga',
                                              quantity ='$quantity',
                                              total ='$total', komentar ='$komentar' WHERE no='$no' AND waktu='$waktu'");

  //jika sudah berhasil
  if ($update) {

    $result = mysqli_query($koneksi, "SELECT SUM(total) AS value_sum FROM selesai WHERE waktu ='$waktu'");
    $row = mysqli_fetch_assoc($result);
    $sum = $row['value_sum'];

    $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
    $countData = mysqli_fetch_array($queryCountData)[0];

    $sum /= $countData;

    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget ='$sum' WHERE waktu='$waktu'");

    $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='view-direksi.php?code=" . $numb . "'; </script>";
  } else {
    echo "Edit Budget Gagal";
  }
}

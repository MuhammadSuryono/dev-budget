<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$waktu = $_POST['waktu'];
$no    = $_POST['no'];
$term  = $_POST['term'];

if (isset($_POST['submit'])) {

  $updatebpu = mysqli_query($koneksi, "UPDATE bpu SET statusrtp ='Moved' WHERE waktu='$waktu' AND no='$no' AND term='$term'");


  if ($updatebpu) {
    echo "<script language='javascript'>";
    echo "alert('BPU Berhasil Di Pindahkan Ke Pengajuan Kas!!')";
    echo "</script>";
    echo "<script> document.location.href='rekap-finance.php'; </script>";
  } else {
    echo "Move BPU Gagal";
  }
}

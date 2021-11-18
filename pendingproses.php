<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$divisi       = $_POST['divisi'];

if (isset($_POST['pending'])) {

  $selbay = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $s = mysqli_fetch_assoc($selbay);
  $noid = $s['noid'];

  $selfirst = mysqli_query($koneksi, "SELECT MIN(noid) FROM bpu WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui'");
  $selsec = mysqli_fetch_assoc($selfirst);
  $numb = $selsec['MIN(noid)'];


  $update = mysqli_query($koneksi, "UPDATE bpu SET persetujuan = 'Pending'
                            WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND noid='$numb'");
}


if ($update) {
  echo "<script language='javascript'>";
  echo "alert('Status BPU Menjadi Pending')";
  echo "</script>";
  echo "<script> document.location.href='views-direksi.php?code=" . $noid . "'; </script>";
} else {
  echo "Gagal, Harap Coba Lagi";
}

?>
?>
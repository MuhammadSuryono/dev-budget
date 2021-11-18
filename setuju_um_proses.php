<?php
//error_reporting(0);
include('koneksi.php');

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$namapenerima = $_POST['namapenerima'];
$tglcair      = $_POST['tanggalbayar'];
$term         = $_POST['term'];
$divisi       = $_SESSION['divisi'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  if ($divisi == 'FINANCE') {

    $update = mysqli_query($koneksi, "UPDATE bpu SET persetujuan = 'Disetujui (Sri Dewi Marpaung)', tglcair = '$tglcair'
                                      WHERE no='$no' AND waktu='$waktu' AND namapenerima='$namapenerima' AND term='$term'");
  } else {

    $update = mysqli_query($koneksi, "UPDATE bpu SET persetujuan = 'Disetujui (Direksi)', tglcair = '$tglcair'
                                      WHERE no='$no' AND waktu='$waktu' AND namapenerima='$namapenerima' AND term='$term'");
  }
}


if ($update) {
  if ($divisi == 'FINANCE') {
    echo "<script language='javascript'>";
    echo "alert('BPU Telah Disetujui(Sri Dewi Marpaung)')";
    echo "</script>";
    echo "<script> document.location.href='list-finance-budewi.php'; </script>";
  } else {
    echo "<script language='javascript'>";
    echo "alert('BPU Telah Disetujui')";
    echo "</script>";
    echo "<script> document.location.href='list-direksi.php'; </script>";
  }
} else {
  echo "Gagal menyetujui BPU";
}

<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no           = $_POST['no'];
$minterm      = $_POST['minterm'];
$namabank     = $_POST['namabank'];
$norek        = $_POST['norek'];
$namapenerima = $_POST['namapenerima'];
$jumlahbayar  = $_POST['jumlahbayar'];
$nomorvoucher = $_POST['nomorvoucher'];
$tanggalbayar = $_POST['tanggalbayar'];
$waktu        = $_POST['waktu'];
$pembayar     = $_POST['pembayar'];
$divisi       = $_POST['divisi'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $selbay = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $s = mysqli_fetch_assoc($selbay);
  $noid = $s['noid'];

  $selfirst = mysqli_query($koneksi, "SELECT max(noid) FROM bpu WHERE no='$no' AND waktu='$waktu' AND status='Belum Di Bayar' AND persetujuan !='Belum Disetujui'");
  $selsec = mysqli_fetch_assoc($selfirst);

  if (mysqli_num_rows($selfirst) == 0) {
    echo "<script language='javascript'>";
    echo "alert('GAGAL, Tidak Ada BPU atau BPU belum disetujui Direksi')";
    echo "</script>";
    echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
  } else {
    $numb = $selsec['max(noid)'];
    $update = mysqli_query($koneksi, "UPDATE bpu SET status = 'Telah Di Bayar', namabank = '$namabank', norek = '$norek', namapenerima = '$namapenerima',
                                          jumlahbayar = '$jumlahbayar',
                                          novoucher = '$nomorvoucher',
                                          tanggalbayar = '$tanggalbayar',
                                          pembayar = '$pembayar',
                                          divpemb = '$divisi' WHERE no='$no' AND waktu='$waktu' AND term='$minterm'");
  }
}

if ($update) {
  echo "<script language='javascript'>";
  echo "alert('Bayar Budget Berhasil')";
  echo "</script>";
  echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
} else {
  echo "Bayar Budget Gagal";
}

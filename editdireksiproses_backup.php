<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$noidbpu        = isset($_POST['noidbpu']) != "" ? $_POST['noidbpu'] : "";
$no             = $_POST['no'];
$waktu          = $_POST['waktu'];
$jumlah         = $_POST['jumlah'];
$realisasi      = $_POST['realisasi'];
$uangkembali    = $_POST['uangkembali'];
$statusbpu      = $_POST['statusbpu'];
$namabank       = $_POST['namabank'];
$namapenerima   = $_POST['namapenerima'];
$norek          = $_POST['norek'];
$tglcair        = $_POST['tglcair'];
$alasan         = $_POST['alasan'];
$lastedit       = $_POST['lastedit'];
$term           = $_POST['term'];
$jumlahbayar    = $_POST['jumlahbayar'];
$tanggalnow     = date("Y-m-d");
$realkemb       = $realisasi + $uangkembali;

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

//periksa apakah udah submit
if (isset($_POST['submit'])) {


  if ($statusbpu == 'Vendor/Supplier' || $statusbpu == 'Honor Eksternal' || $statusbpu = 'Biaya Lumpsum' || $statusbpu = 'Biaya') {

    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah = '$jumlah', statusbpu='$statusbpu', namabank = '$namabank', namapenerima = '$namapenerima', norek = '$norek',
                                            tglcair = '$tglcair', alasan = '$alasan', lastedit = '$lastedit', realisasi='$realisasi', statusbpu='$statusbpu',
                                            uangkembali='$uangkembali', tanggalrealisasi='$tanggalnow'
                                            WHERE no = '$no' AND waktu = '$waktu' AND term = '$term'");
  } else {


    $cekbpuum = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$namapenerima' AND status != 'Realisasi (Direksi)' AND statusbpu='UM'");
    $rc = mysqli_fetch_array($cekbpuum);
    $bpuum = $rc['sumi'];

    $saldo = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user='$namapenerima'");
    $sld = mysqli_fetch_assoc($saldo);
    $saldobpu = $sld['saldo'];

    $jadi = $jumlah + $bpuum;

    if ($jadi > $saldobpu) {
      echo "<script language='javascript'>";
      echo "alert('EDIT GAGAL!!, Saldo BPU $namapenerima Sisa Rp. $saldosisa')";
      echo "</script>";
      echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
    } else if ($jumlahbayar < $realkemb) {

      $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah = '$jumlah', statusbpu='$statusbpu', namabank = '$namabank', namapenerima = '$namapenerima', norek = '$norek',
                                              tglcair = '$tglcair', alasan = '$alasan', lastedit = '$lastedit', status ='Telah Di Bayar',realisasi='$realisasi',
                                              uangkembali='$uangkembali', tanggalrealisasi='$tanggalnow' WHERE no = '$no'
                                              AND waktu = '$waktu' AND term = '$term'");
    } else {

      $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah = '$jumlah', statusbpu='$statusbpu', namabank = '$namabank', namapenerima = '$namapenerima', norek = '$norek',
                                            tglcair = '$tglcair', alasan = '$alasan', lastedit = '$lastedit', realisasi='$realisasi',
                                            uangkembali='$uangkembali', tanggalrealisasi='$tanggalnow' WHERE no = '$no'
                                            AND waktu = '$waktu' AND term = '$term'");
    }
  }
  //jika sudah berhasil
  if ($update) {

    $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];

    if ($_SESSION['nama_user'] == 'SRI DEWI MARPAUNG') {
      echo "<script language='javascript'>";
      echo "alert('Edit BPU Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Edit BPU Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  }
}

<?php

error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$pengaju      = $_POST['pengaju'];
$divisi       = $_POST['divisi'];
$statusbpu    = $_POST['statusbpu'];
$jumlah       = $_POST['jumlah'];
$tglcair      = $_POST['tglcair'];
$namapenerima = $_POST['namapenerima'];
$norek        = $_POST['norek'];
$bank         = $_POST['bank'];
$project      = $_POST['project'];
$jatuhtempo   = $_POST['jatuhtempo'];
$nama_gambar  = $_FILES['gambar']['name'];
$lokasi       = $_FILES['gambar']['tmp_name']; // Menyiapkan tempat nemapung gambar yang diupload
$lokasitujuan = "fileupload/"; // Menguplaod gambar kedalam folder ./image
$upload       = move_uploaded_file($lokasi, $lokasitujuan . "/" . $nama_gambar);

if (isset($_POST['submit'])) {
  $ceksaldo = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM BPU WHERE namapenerima='$namapenerima' AND statusbpu='UM' AND status != 'Realisasi (Direksi)'");
  $cs = mysqli_fetch_array($koneksi, $ceksaldo);
  $umproses = $cs['sumjum'];

  $ceklimit  = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user='$namapenerima'");
  $cl        = mysqli_fetch_assoc($ceklimit);
  $limit     = $cl['saldo'];

  $umtotal   = $umproses + $jumlah;

  if ($umtotal > $limit) {
    echo "<script language='javascript'>";
    echo "alert('GAGAL!!, Saldo UM $namapenerima tidak mencukupi untuk pengajuan UM Burek')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
  } else {

    $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu' AND namapenerima='$namapenerima'");
    $m = mysqli_fetch_assoc($selterm);
    $termterm = $m['MAX(term)'];
    $termfinal = $termterm + 1;


    $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,project,jatuhtempo)
                                        VALUES ('$no','$jumlah','$tglcair','$bank','$norek','$namapenerima','$pengaju','$divisi','$waktu',
                                                'Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar','$project','$jatuhtempo')");
  }

  if ($insert) {
    echo "<script language='javascript'>";
    echo "alert('Pembuatan BPU UM Burek Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='list-direksi.php'; </script>";
  } else {
    echo "<script language='javascript'>";
    echo "alert('GAGAL (^o^)')";
    echo "</script>";
    echo "<script> document.location.href='list-direksi.php'; </script>";
  }
}

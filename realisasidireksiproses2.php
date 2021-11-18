<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$no               = $_POST['no'];
$waktu            = $_POST['waktu'];
$tanggalrealisasi = $_POST['tanggalrealisasi'];
$totalbpu         = $_POST['totalbpu'];
$totalrealisasi   = $_POST['totalrealisasi'];
$totalrealisasi = (int) filter_var($totalrealisasi, FILTER_SANITIZE_NUMBER_INT);
$uangkembali      = $_POST['uangkembali'];
$uangkembali = (int) filter_var($uangkembali, FILTER_SANITIZE_NUMBER_INT);

// var_dump($uangkembali);
//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $selnumb = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $sn = mysqli_fetch_assoc($selnumb);
  $numb = $sn['noid'];
  $kembreal = $totalrealisasi + $uangkembali;

  // var_dump($totalrealisasi);
  // var_dump($totalbpu);
  // var_dump($kembreal);
  // die;

  if ($totalrealisasi > $totalbpu) {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! Total Realiasi tidak bisa lebih besar dari Total BPU')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else if ($kembreal > $totalbpu) {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! Total Realiasi dan Uang Kembali tidak bisa lebih besar dari Total BPU')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {

    if ($kembreal < $totalbpu) {
      $status = "Telah Di Bayar";
    } else {
      $status = "Realisasi (Direksi)";
    }
    $term_arr = $_POST['term'];
    $count = count($term_arr);
    $bagireal = $totalrealisasi / $count;
    $bagikemb = $uangkembali / $count;

    if (gettype($_POST['term']) == "array") {

      foreach ($_POST['term'] as $val) {

        $id_c = $val;

        $update = mysqli_query($koneksi, "UPDATE bpu SET realisasi= realisasi + $bagireal, uangkembali= uangkembali + $bagikemb, status='$status' , tanggalrealisasi='$tanggalrealisasi'
                                WHERE no='$no' AND waktu='$waktu' AND term='$id_c'");
      }
    }

    $checkBox = implode(',', $_POST['term']);
    $insert = mysqli_query($koneksi, "INSERT INTO realisasi (no, waktu, term, totalbpu, totalrealisasi, uangkembali, tanggal)
                               VALUES ('$no','$waktu','" . $checkBox . "','$totalbpu','$totalrealisasi','$uangkembali','$tanggalrealisasi')");

    if ($insert) {
      echo "<script language='javascript'>";
      echo "alert('Realisasi Berhasil!!')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  }
}

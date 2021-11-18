<?php
//error_reporting(0);
include('koneksi.php');

if (isset($_POST['submit'])) {

  $waktu     = $_POST['waktu'];
  $rincian   = $_POST['rincian'];
  $kota      = $_POST['kota'];
  $status    = $_POST['status'];
  $penerima  = $_POST['penerima'];
  $harga     = $_POST['harga'];
  $quantity  = $_POST['quantity'];
  $total     = $_POST['total'];
  $pengaju   = $_POST['pengaju'];
  $divisi    = $_POST['divisi'];


  //periksa apakah udah submit

  $selfirst = mysqli_query($koneksi, "SELECT MAX(no) FROM selesai WHERE waktu = '$waktu'");



  $ea = mysqli_fetch_assoc($selfirst);
  $nomax = $ea['MAX(no)'] + 1;

  $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
  $countData = mysqli_fetch_array($queryCountData)[0];
  $nomaxTemp = $nomax;
  // var_dump($countData);
  // die();
  for ($i = 0; $i < $countData; $i++) {
    $insertdulu = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar) VALUES
                                            ('$nomax','$rincian','$kota','$status','$penerima','$harga','$quantity','$total','Belum Dibayar','$pengaju','$divisi','$waktu',NULL)");
    var_dump($i);
  }
  // die();

  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];


  if ($insertdulu && $status != 'UM Burek') {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);

    $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
    $countData = mysqli_fetch_array($queryCountData)[0];

    $totaljadi = $total = $row[0];
    $totaljadi /= $countData;


    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");
  } else if ($insertdulu && $status == 'UM Burek') {
    $updatetotal = 1;
  }


  if ($updatetotal) {
    if ($divisi == 'Direksi') {
      echo "<script language='javascript'>";
      echo "alert('Tambah Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    } else {
      if ($_POST['link'] == 'disapprove') {
        echo "<script language='javascript'>";
        echo "alert('Tambah Budget Berhasil')";
        echo "</script>";
        echo "<script> document.location.href='view-disapprove.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('Tambah Budget Berhasil')";
        echo "</script>";
        echo "<script> document.location.href='view.php?code=" . $numb . "'; </script>";
      }
    }
  } else {
    echo "Tambah Budget Gagal";
  }
}

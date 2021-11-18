<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
error_reporting(0);

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $no        = $_POST['no'];
  $waktu     = $_POST['waktu'];
  $rincian   = $_POST['rincian'];
  $kota      = $_POST['kota'];
  $status    = $_POST['status'];
  $penerima  = $_POST['penerima'];
  $harga     = $_POST['harga'];
  $quantity  = $_POST['quantity'];
  $total     = $_POST['total'];
  $divisi    = $_POST['divisi'];

  $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
  $countData = mysqli_fetch_array($queryCountData)[0];

  $totaludah = $harga * $quantity;
  $checktotal = mysqli_query($koneksi, "SELECT sum(total) AS sumt FROM selesai WHERE waktu='$waktu' AND no !='$no'");
  $st = mysqli_fetch_array($checktotal);
  $tn = $st['sumt'];
  $jadi = ($totaludah + $tn) / $countData;



  $cekbudgettotal = mysqli_query($koneksi, "SELECT totalbudgetnow FROM pengajuan WHERE waktu='$waktu'");
  $cb = mysqli_fetch_assoc($cekbudgettotal);
  $totbud = $cb['totalbudgetnow'];

  $sel1 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];
  $jenis = $uc['jenis'];

  $liatjumlah = mysqli_query($koneksi, "SELECT harga,quantity,total FROM selesai WHERE no='$no' AND waktu='$waktu'");
  $lj = mysqli_fetch_array($liatjumlah);
  $totalselesai     = $lj['total'];
  $hargaselesai     = $lj['harga'];
  $quantityselesai  = $lj['quantity'];

  $query10 = "SELECT sum(jumlah) AS sumbpu FROM bpu WHERE waktu='$waktu' AND no='$no'";
  $result10 = mysqli_query($koneksi, $query10);
  $row10 = mysqli_fetch_array($result10);
  $totalbpunya = $total10 = $row10[0];
  $totalbpunya /= $countData;

  $totalHarga = ($harga * $quantity) / $countData;
  if ($jenis == 'Rutin') {

    $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
  } else {


    if ($totalselesai == $totaludah) {

      $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                  total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
    } else if ($totalbpunya > $totaludah) {
      echo "<script language='javascript'>";
      echo "alert('GAGAL!! , Total Item Budget Lebih kecil Dari BPU yang sudah dibuat')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    } else if ($jadi > $totbud) {
      // if($divisi == 'FINANCE'){
      // echo "<script language='javascript'>";
      // echo "alert('GAGAL!! , Total Budget Lebih Besar Dari Yang Disetujui')";
      // echo "</script>";
      // echo "<script> document.location.href='view-finance-manager.php?code=".$numb."'; </script>";
      // }else{
      echo "<script language='javascript'>";
      echo "alert('GAGAL!! , Total Budget Lebih Besar Dari Yang Disetujui')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
    // }else{


    // if($divisi == 'FINANCE'){
    //   echo "<script language='javascript'>";
    //   echo "alert('Gagal!!, Untuk Edit Budget Harap Hubungi Ibu Ina')";
    //   echo "</script>";
    //   echo "<script> document.location.href='views-direksi.php?code=".$numb."'; </script>";
    //   }

    else {

      $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");

      // }
    }
  }
  // }

  //jika sudah berhasil
  if ($update) {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);

    $totaljadi = $total = $row[0];
    $totaljadi /= $countData;

    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");
  }

  if ($updatetotal) {

    if ($divisi == 'FINANCE') {
      echo "<script language='javascript'>";
      echo "alert('Edit Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Edit Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  } else {
    echo "Edit Budget Gagal";
  }
}

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

  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];

  $cariuangkembali = mysqli_query($koneksi, "SELECT SUM(uangkembali) AS sumkem FROM bpu WHERE waktu='$waktu'");
  $cu = mysqli_fetch_array($cariuangkembali);
  $cusumkem = $cu['sumkem'];

  $cariuangkembali2 = mysqli_query($koneksi, "SELECT SUM(total) AS sumuse FROM selesai WHERE waktu='$waktu' AND uangkembaliused='Y'");
  $cu2 = mysqli_fetch_array($cariuangkembali2);
  $cusumkem2 = $cu2['sumuse'];

  $totalkembali = $cusumkem - $cusumkem2;

  if ($total > $totalkembali) {
    echo "<script language='javascript'>";
    echo "alert('Gagal!! Total Item Budget Lebih Besar Dari Uang Kembali')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {

    $selfirst = mysqli_query($koneksi, "SELECT MAX(no) FROM selesai WHERE waktu = '$waktu'");

    $ea = mysqli_fetch_assoc($selfirst);
    $nomax = $ea['MAX(no)'] + 1;

    $insertdulu = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused) VALUES
                                            ('$nomax','$rincian','$kota','$status','$penerima','$harga','$quantity','$total','Belum Dibayar','$pengaju','$divisi','$waktu',NULL,'Y')");

    if ($insertdulu) {
      $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
      $result = mysqli_query($koneksi, $query);
      $row = mysqli_fetch_array($result);

      $totaljadi = $total = $row[0];

      $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");
    }
  }

  if ($updatetotal) {
    echo "<script language='javascript'>";
    echo "alert('Tambah Budget Berhasil')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {
    echo "Tambah Budget Gagal";
  }
}

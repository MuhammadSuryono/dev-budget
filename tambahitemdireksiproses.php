<?php
//error_reporting(0);
include('koneksi.php');

if (isset($_POST['submit'])) {

  $nomax          = $_POST['nomax'];
  $waktu          = $_POST['waktu'];
  $rincian        = $_POST['rincian'];
  $kota           = $_POST['kota'];
  $status         = $_POST['status'];
  $penerima       = $_POST['penerima'];
  $harga          = $_POST['harga'];
  $quantity       = $_POST['quantity'];
  $total          = $_POST['total'];
  $pengaju        = $_POST['pengaju'];
  $divisi         = $_POST['divisi'];
  $totalbudget    = $_POST['totalbudget'];
  $totalbudgetnow = $_POST['totalbudgetnow'];

  $selisih = $totalbudgetnow - $totalbudget;

  $selectnoid = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $sn = mysqli_fetch_assoc($selectnoid);
  $numb = $sn['noid'];

  if ($total > $selisih) {
    echo "<script language='javascript'>";
    echo "alert('Tambah Item Gagal, Total item lebih besar dari budget yang disetujui')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {

    $insertsel = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pengaju,divisi,waktu)
                                           VALUES ('$nomax','$rincian','$kota','$status','$penerima','$harga','$quantity','$total','$pengaju','$divisi','$waktu')");
  }


  if ($insertsel) {
    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);
    $totaljadi = $total = $row[0];

    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totaljadi WHERE waktu='$waktu'");
  }


  if ($updatetotal) {
    echo "<script language='javascript'>";
    echo "alert('Penambahan item budget <b>Berhasil</b>!!')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  } else {
  }
}

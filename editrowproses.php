<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
error_reporting(0);

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
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

  // Pengambilan total pengajuan yang sama
  $queryCountData = mysqli_query($koneksi, "SELECT COUNT(noid) FROM pengajuan WHERE waktu='$waktu'");
  $countData = mysqli_fetch_array($queryCountData)[0];

  // Menghitung total dikali harga yang berubah
  $totalQuantityKaliHarga = $harga * $quantity;
  if ($totalQuantityKaliHarga == 0) {
      $quantity = 0;
  }

  // Total semua item yang sekarang + yang diganti
  $checktotal = mysqli_query($koneksi, "SELECT sum(total) AS sumt FROM selesai WHERE waktu='$waktu' AND no !='$no'");
  $st = mysqli_fetch_array($checktotal);
  $totalNominalItem = $st['sumt'];
  $totalNominalDiganti = ($totalQuantityKaliHarga + $totalNominalItem) / $countData;

  // Ambil total budget Now
  $cekbudgettotal = mysqli_query($koneksi, "SELECT totalbudgetnow,totalbudget FROM pengajuan WHERE waktu='$waktu'");
  $cb = mysqli_fetch_assoc($cekbudgettotal);
  $totalBudgetSekarang = $cb['totalbudgetnow'];
  $totalBudget = $cb['totalbudget'];

  if ($totalBudgetSekarang == '') $totalBudgetSekarang = $totalBudget;

  // Ambil data pengajuan
  $sel1 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];
  $jenis = $uc['jenis'];

  // Ambil data item
  $liatjumlah = mysqli_query($koneksi, "SELECT harga,quantity,total FROM selesai WHERE no='$no' AND waktu='$waktu'");
  $lj = mysqli_fetch_array($liatjumlah);
  $totalselesai     = $lj['total'];
  $hargaselesai     = $lj['harga'];
  $quantityselesai  = $lj['quantity'];

  // Menjumlahkan jumlah bpu item
  $query10 = "SELECT sum(jumlah) AS sumbpu FROM bpu WHERE waktu='$waktu' AND no='$no'";
  $result10 = mysqli_query($koneksi, $query10);
  $row10 = mysqli_fetch_array($result10);
  $totalbpunya = $row10[0];
  $totalbpunya /= $countData;

  $totalHarga = $totalQuantityKaliHarga / $countData;

  if ($totalBudgetSekarang < $totalNominalDiganti ) {
      echo "<script language='javascript'>";
      echo "alert('Total budget yang diganti melebihi Pagu. Selisih perubahannya Rp. ".number_format($totalNominalDiganti - $totalBudgetSekarang)." Total yang diganti Rp. ".number_format($totalNominalDiganti).", Total yang di setujui Rp. ".number_format($totalBudgetSekarang)."')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  }elseif ($jenis == 'Rutin') {

    $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
  } else {
      // $totalselesai == Total sum pada item
    if ($totalselesai == $totalQuantityKaliHarga) {

      $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                  total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
    } else if ($totalbpunya > $totalQuantityKaliHarga) {
      echo "<script language='javascript'>";
      echo "alert('GAGAL!! , Total Item Budget Lebih kecil Dari BPU yang sudah dibuat')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }else {
      $update = mysqli_query($koneksi, "UPDATE selesai SET rincian = '$rincian', kota = '$kota', status = '$status', penerima = '$penerima', harga = '$harga', quantity = '$quantity',
                                                total= $harga * $quantity WHERE no ='$no' AND waktu='$waktu'");
    }
  }

//  if ($update) {
//    $query = "SELECT sum(total) AS sum FROM selesai WHERE waktu='$waktu'";
//    $result = mysqli_query($koneksi, $query);
//    $row = mysqli_fetch_array($result);
//
//    $totaljadi = $total = $row[0];
//    $totaljadi /= $countData;
//    $updatetotal = mysqli_query($koneksi, "UPDATE pengajuan SET totalbudget = $totalBudgetSekarang WHERE waktu='$waktu'");
//  }

  if ($update) {

      if ($totalNominalDiganti < $totalBudgetSekarang) {
          echo "<script language='javascript'>";
          echo "alert('Total budget lebih kecil dari Pagu')";
          echo "</script>";
          echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
      }elseif ($divisi == 'FINANCE') {
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

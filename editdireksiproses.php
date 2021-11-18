<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();

$noidbpu          = isset($_POST['noidbpu']) != "" ? $_POST['noidbpu'] : "";
$no               = $_POST['no'];
$waktu            = $_POST['waktu'];
$persetujuan      = $_POST['persetujuan'];
$jumlah           = $_POST['jumlah'];
$realisasi        = $_POST['realisasi'];
$uangkembali      = $_POST['uangkembali'];
$tanggalrealisasi = $_POST['tanggalrealisasi'];
$arrnamabank     = $_POST['namabank'];
$arrnorek        = $_POST['norek'];
$arrnamapenerima = $_POST['namapenerima'];
$arremailpenerima = $_POST['email'];
$tanggalbayar     = $_POST['tanggalbayar'];
$alasan           = $_POST['alasan'];
$lastedit         = $_POST['lastedit'];
$term             = $_POST['term'];
$statusbayar      = $_POST['status'];
$tanggalnow       = date("Y-m-d");
$realkemb         = $realisasi + $uangkembali;


for ($i = 0; $i < count($arrnamapenerima); $i++) {
  $namapenerima .= $arrnamapenerima[$i];
  if ($i < count($arrnamapenerima) - 1)
    $namapenerima .= ', ';
}
for ($i = 0; $i < count($arrnorek); $i++) {
  $norek .= $arrnorek[$i];
  if ($i < count($arrnorek) - 1)
    $norek .= ', ';
}
for ($i = 0; $i < count($arrnamabank); $i++) {
  $namabank .= $arrnamabank[$i];
  if ($i < count($arrnamabank) - 1)
    $namabank .= ', ';
}
for ($i = 0; $i < count($arremailpenerima); $i++) {
  $emailpenerima .= $arremailpenerima[$i];
  if ($i < count($arremailpenerima) - 1)
    $emailpenerima .= ', ';
}

$aksesSes = $_SESSION['hak_akses'];

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
  $aw = mysqli_fetch_assoc($pilihtotal);
  $hargaah = $aw['total'];

  $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu' AND term !='$term'";
  $result = mysqli_query($koneksi, $query);
  $row = mysqli_fetch_array($result);
  $total = $row['sum'];

  $jadinya = $total + $jumlah;

  $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];


  if ($jadinya > $hargaah) {
    echo "<script language='javascript'>";
    echo "alert('GAGAL, Jumlah tidak bisa di edit lebih dari sisa pembayaran')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
  } else if ($jumlah > $realkemb && $statusbayar == 'Belum Di Bayar') {
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah ='$jumlah', namabank ='$namabank', namapenerima ='$namapenerima', emailpenerima ='$emailpenerima', norek ='$norek',
                                          tanggalbayar='$tanggalbayar', alasan='$alasan'
                                          WHERE no ='$no' AND waktu ='$waktu' AND term ='$term'");
  } else if ($jumlah > $realkemb && $persetujuan == 'Disetujui (Direksi)') {
    $status = "Telah Di Bayar";
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah ='$jumlah', realisasi ='$realisasi', uangkembali ='$uangkembali', tanggalrealisasi ='$tanggalrealisasi',
                                            jumlahbayar='$jumlah', namabank ='$namabank', namapenerima ='$namapenerima', emailpenerima ='$emailpenerima', norek ='$norek', tanggalbayar='$tanggalbayar', alasan='$alasan',
                                            status ='$status'
                                            WHERE no ='$no' AND waktu ='$waktu' AND term ='$term'");
  } else {
    $status = "Realisasi (Direksi)";
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah ='$jumlah', realisasi ='$realisasi', uangkembali ='$uangkembali', tanggalrealisasi ='$tanggalrealisasi',
                                            jumlahbayar='$jumlah', namabank ='$namabank', namapenerima ='$namapenerima', emailpenerima ='$emailpenerima', norek ='$norek', tanggalbayar='$tanggalbayar', alasan='$alasan',
                                            status ='$status'
                                            WHERE no ='$no' AND waktu ='$waktu' AND term ='$term'");
  }
}

//jika sudah berhasil
if ($update) {
  if ($_SESSION['divisi'] == 'FINANCE') {
    if ($_SESSION['hak_akses'] == 'Manager') {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan BPU Eksternal Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan BPU Eksternal Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
    }
  } else {
    echo "<script language='javascript'>";
    echo "alert('Pembuatan BPU Eksternal Berhasil!!')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  }
} else {
  echo "<script language='javascript'>";
  echo "alert('Edit BPU Berhasil')";
  echo "</script>";
  if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
  else echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
}

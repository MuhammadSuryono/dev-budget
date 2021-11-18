<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
//error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}


if (isset($_POST['submit'])) {

  $jenis             = $_POST['jenis'];
  $nama              = $_POST['nama'];
  $tahun             = $_POST['tahun'];
  $status            = $_POST['status'];
  $idpengaju         = $_POST['idpengaju'];
  $kodepro           = $_POST['kodepro'];
  $katnon            = $_POST['katnon'];
  $pembuat           = $_SESSION['nama_user'];


  $carinamdiv = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$idpengaju'");
  $cn = mysqli_fetch_assoc($carinamdiv);
  $pengaju           = $cn['nama_user'];
  $divisi            = $cn['divisi'];


  $carirutinnya = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Rutin' ORDER BY noid DESC LIMIT 1");
  $crnya = mysqli_fetch_assoc($carirutinnya);
  $wakturutin = $crnya['waktu'];


  $insertkepengaju = mysqli_query($koneksi, "INSERT INTO pengajuan(jenis,nama,tahun,pengaju,divisi,status,pembuat,kodeproject,katnonrut)
                                                    VALUES ('$jenis','$nama','$tahun','$pengaju','$divisi','$status','$pembuat','$kodepro','$katnon')");


  if ($insertkepengaju) {

    $cariwaktunya = mysqli_query($koneksi, "SELECT waktu FROM pengajuan ORDER BY noid DESC LIMIT 1");
    $waktu = mysqli_fetch_assoc($cariwaktunya);
    $waktunya = $waktu['waktu'];

    if ($jenis == 'B1') {

      $inserkeselesai = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                     VALUES ('1','Honor Jakarta','Jabodetabek','Honor Jakarta','Shopper/PWT','0','0','0','','$pengaju','$divisi','$waktunya','','')");

      $inserkeselesai2 = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                     VALUES ('2','Honor Luar Kota','Luar kota','Honor Luar Kota','Shopper/PWT','0','0','0','','$pengaju','$divisi','$waktunya','','')");

      $inserkeselesai3 = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                     VALUES ('3','STKB Transaksi Jakarta','Jabodetabek','STKB TRK Jakarta','TLF','0','0','0','','$pengaju','$divisi','$waktunya','','')");

      $inserkeselesai4 = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                    VALUES ('4','STKB Transaksi Luar Kota','Luar Kota','STKB TRK Luar Kota','TLF','0','0','0','','$pengaju','$divisi','$waktunya','','')");

      $inserkeselesai5 = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                    VALUES ('5','STKB OPS','Jabodetabek dan Luar Kota','STKB OPS','TLF','0','0','0','','$pengaju','$divisi','$waktunya','','')");
    } else if ($jenis == 'Rutin') {

      $rutinselesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$wakturutin'");
      while ($rsnya = mysqli_fetch_array($rutinselesai)) {

        $rutininsert = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi)
                                                     VALUES ('$rsnya[no]','$rsnya[rincian]','$rsnya[kota]','$rsnya[status]','$rsnya[penerima]','$rsnya[harga]','$rsnya[quantity]',
                                                             '$rsnya[total]','$rsnya[pembayaran]','$rsnya[pengaju]','$rsnya[divisi]')");
      }

      $inserkeselesai5 = TRUE;
    } else {

      $inserkeselesai5 = TRUE;
    }
  }

  // var_dump($wakturutin);
  // die;

  if ($inserkeselesai5) {
    if ($_SESSION['divisi'] == 'Direksi') {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='../home-direksi.php'; </script>";
    } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='../home-finance.php'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Berhasil')";
      echo "</script>";
      echo "<script> document.location.href='../home.php'; </script>";
    }
  }
}

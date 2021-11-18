<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$waktu          = $_POST['waktu'];
$nomorawal      = $_POST['nomorawal'];
$hargaawal      = $_POST['hargaawal'];
$quantityawal   = $_POST['quantityawal'];
$totalawal      = $_POST['totalawal'];
$nomorakhir     = $_POST['nomorakhir'];
$hargaakhir     = $_POST['hargaakhir'];
$quantityakhir  = $_POST['quantityakhir'];
$totalakhir     = $_POST['totalakhir'];


  if (isset($_POST['submit'])){

    $insert = mysqli_query($koneksi, "INSERT INTO moveharga (waktu,nomorawal,hargaawal,quantityawal,totalawal,nomorakhir,quantityakhir,hargaakhir)
                                          VALUES ('$waktu','$nomorawal','$hargaawal','$quantityawal','$totalawal','$nomorakhir','$quantityakhir','$hargaakhir')");

    if ($insert){
        $hargajadi    = $hargaawal - $hargaakhir;
        $quantityjadi = $quantityawal - $quantityjadi;
        $totaljadi    = $totalawal - $totalakhir;
      if($quantityawal == 1){

        $updateawal = mysqli_query($koneksi, "UPDATE selesai SET harga='$hargajadi', total='$totaljadi' WHERE waktu='$waktu' AND no='$nomorawal'");

      }else{

        $updateawal = mysqli_query($koneksi, "UPDATE selesai SET harga='$hargajadi', quantity='$quantityjadi', total='$totaljadi' WHERE waktu='$waktu' AND no='$nomorawal'");

      }
    }


    if ($updateawal) {
      if($quan)

    }




    if ($updatebpu){
      echo "<script language='javascript'>";
      echo "alert('BPU Berhasil Di Pindahkan Ke Pengajuan Kas!!')";
      echo "</script>";
      echo "<script> document.location.href='rekap-finance.php'; </script>";
    }else{
      echo "Move BPU Gagal";
    }
  }
?>

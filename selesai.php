<?php

//error_reporting(0);
session_start();
if(!isset($_SESSION['nama_user'])){
  header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}


if(isset($_POST['submit'])){

$jenis		         = $_POST['jenis'];
$nama              = $_POST['nama'];
$tahun             = $_POST['tahun'];
$pengaju           = $_POST['pengaju'];
$divisi            = $_POST['divisi'];
$status            = $_POST['status'];

      $done=mysqli_query($koneksi, "INSERT INTO pengajuan(jenis,nama,tahun,pengaju,divisi,status)
                                      VALUES ('$jenis','$nama','$tahun','$pengaju','$divisi','$status')");

			if($done){
				echo "<script language='javascript'>";
				echo "alert('Pembuatan Pengajuan Budget Berhasil')";
				echo "</script>";
				echo "<script> document.location.href='home.php'; </script>";

			   }
		    }

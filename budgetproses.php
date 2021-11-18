<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (isset($_POST['submit'])) {

	$rincian		  =	$_POST['rincian'];
	$kota				  = $_POST['kota'];
	$status				=	$_POST['status'];
	$penerima     = $_POST['penerima'];
	$harga        = $_POST['harga'];
	$quantity			= $_POST['quantity'];
	$total     		= $_POST['total'];
	$pembayaran		= $_POST['pembayaran'];
	$pengaju			= $_POST['pengaju'];
	$divisi				=	$_POST['divisi'];

	$sql	=	mysqli_query($koneksi, "INSERT INTO tampungan(rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi)
                    	VALUES ('$rincian','$kota','$status','$penerima','$harga','$quantity','$total','$pembayaran','$pengaju','$divisi')");

	if ($sql) {
		echo "<script> document.location.href='home.php?page=2'; </script>";
	}
}

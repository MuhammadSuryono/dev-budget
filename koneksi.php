<?php

$host = "localhost";
$host2 = "192.168.10.240";
$user = "adam";
$pass = "Ad@mMR1db";
$db = "budget";
$dbJay = "jay2";
$dbCuti = "db_cuti";
$dbDigitalMarket = "digitalisasimarketing";
$dbTransfer = "bridgetransfer";
$dbMriTransfer = "mritransfer";
$dbDevelop = "develop";

$koneksi = mysqli_connect($host, $user, $pass, $db, 35728);

if (!$koneksi) {
	die("Connection error: " . mysqli_connect_errno());
}

$koneksiJay = mysqli_connect($host, $user, $pass, $dbJay);

if (!$koneksiJay) {
	die("Connection Jay error: " . mysqli_connect_errno());
}

$koneksiCuti = mysqli_connect($host, $user, $pass, $dbCuti);

if (!$koneksiCuti) {
	die("Connection Cuti error: " . mysqli_connect_errno());
}

$koneksiTransfer = mysqli_connect($host, $user, $pass, $dbTransfer);

if (!$koneksiTransfer) {
	die("Connection Transfer error: " . mysqli_connect_errno());
}

$koneksiMriTransfer = mysqli_connect($host, $user, $pass, $dbMriTransfer);

if (!$koneksiMriTransfer) {
	die("Connection Transfer error: " . mysqli_connect_errno());
}

$koneksiDigitalMarket = mysqli_connect($host2, $user, $pass, $dbDigitalMarket);

if (!$koneksiDigitalMarket) {
	die("Connection Transfer error: " . mysqli_connect_errno());
}

$koneksiDevelop = mysqli_connect($host, $user, $pass, $dbDevelop);

if (!$koneksiDevelop) {
	die("Connection Transfer error: " . mysqli_connect_errno());
}

$tanggal = date('d-M-y');
/*
if ($koneksi)
{
	echo "berhasil : )";
}else{
	echo "Gagal !";
}
*/

<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$jenis = $_POST['jenis'];

$query = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$jenis'") or die(mysqli_error($koneksiMriTransfer));
$result = mysqli_fetch_assoc($query);
echo json_encode($result);

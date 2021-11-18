<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

session_start();

// var_dump($_POST);
$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$kode = $_POST['code'];

$update = mysqli_query($koneksi, "UPDATE bpu SET batas_tanggal_bayar = NULL WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));

if ($update) {
    echo "<script language='javascript'>";
    echo "alert('Akses pembayaran bpu berhasil dibuka')";
    echo "</script>";
    echo "<script> document.location.href='view-finance-manager.php?code=" . $kode . "'; </script>";
} else {
    echo "<script language='javascript'>";
    echo "alert('Akses pembayaran bpu gagal dibuka')";
    echo "</script>";
    echo "<script> document.location.href='view-finance-manager.php?code=" . $kode . "'; </script>";
}

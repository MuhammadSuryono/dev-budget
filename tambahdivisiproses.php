<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$namaDivisi = $_POST['namaDivisi'];

if ($_POST['submit'] == 'save') {
    $divisi = $_POST['namaDivisi'];
    $insert = mysqli_query($koneksi, "INSERT INTO divisi VALUES('', '$divisi')");
    if ($insert) {
        echo "<script language='javascript'>";
        echo "alert('Input Divisi Berhasil!')";
        echo "</script>";
        echo "<script> document.location.href='saldobpu.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Input Divisi Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='saldobpu.php'; </script>";
    }
}

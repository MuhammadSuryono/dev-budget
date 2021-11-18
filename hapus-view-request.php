<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_GET['id']) {
    $id = $_GET['id'];



    // mengambil data berdasarkan id
    // dan menampilkan data ke dalam form modal bootstrap
    $delete = mysqli_query($koneksi, "DELETE FROM pengajuan_request WHERE id = '$id'") or die(mysqli_error($koneksi));

    if ($delete) {
        echo "<script language='javascript'>";
        echo "alert('Data berhasil dihapus.')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Data gagal dihapus.')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    }
}

$koneksi->close();

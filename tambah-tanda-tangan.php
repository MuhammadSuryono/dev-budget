<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
session_start();

$id_user = $_POST['id_user'];
$divisi = $_SESSION['divisi'];

function random_bytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}
if ($_FILES["gambar"]['size'] < 200000) {
    $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = random_bytes(20) . "." . $extension;
    $target_file = "uploads/sign/" . $nama_gambar;
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

    $update = mysqli_query($koneksi, "UPDATE tb_user SET e_sign = '$nama_gambar' WHERE id_user='$id_user'");
} else {
    if ($divisi == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload, ukuran melebihi 200kb')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else if ($divisi == 'Direksi') {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload, ukuran melebihi 200kb')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload, ukuran melebihi 200kb')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    }
    die;
}

if ($update) {
    if ($divisi == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Gambar berhasil diupload')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else if ($divisi == 'Direksi') {
        echo "<script language='javascript'>";
        echo "alert('Gambar berhasil diupload')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gambar berhasil diupload')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    }
} else {
    if ($divisi == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else if ($divisi == 'Direksi') {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gambar gagal diupload')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    }
}

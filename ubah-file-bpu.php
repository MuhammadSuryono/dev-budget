<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

session_start();

function random_bytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$kode = $_POST['code'];


$sel1 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$uc = mysqli_fetch_assoc($sel1);
$numb = $uc['noid'];

if ($uc['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($_FILES["gambar"]["name"]) {
    $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = random_bytes(20) . "." . $extension;
    $target_file = "uploads/" . $nama_gambar;
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    $update = mysqli_query($koneksi, "UPDATE bpu SET fileupload='$nama_gambar' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
}

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

if ($update) {
    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('File berhasil diubah.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('File berhasil diubah.')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
    }
} else {
    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('File gagal diubah!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('File gagal diubah!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
    }
}

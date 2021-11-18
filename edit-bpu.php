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
$kode = $_POST['kode'];
$total = $_POST['jumlah'];
$penerima = ($_POST['namapenerimaAjukanKembali']) ? $_POST['namapenerimaAjukanKembali'] : $_POST['namapenerima'];
$bank = ($_POST['namabankAjukanKembali']) ? $_POST['namabankAjukanKembali'] : $_POST['namabank'];
$norek = ($_POST['norekAjukanKembali']) ? $_POST['norekAjukanKembali'] : $_POST['norek'];
$pengaju = $_SESSION['nama_user'];

// var_dump($_POST);
// die;

$pengajuan = mysqli_fetch_assoc($queryPengajuan);

if ($_FILES["gambar"]["name"]) {
    $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = random_bytes(20) . "." . $extension;
    $target_file = "uploads/" . $nama_gambar;
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$total, fileupload='$nama_gambar', namapenerima='$penerima', namabank='$bank', norek='$norek' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
} else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$total, namapenerima='$penerima', namabank='$bank', norek='$norek' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
}
$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

if ($update) {
    echo "<script language='javascript'>";
    echo "alert('BPU Berhasil diubah')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
} else {
    echo "<script language='javascript'>";
    echo "alert('Pengajuan Kembali BPU Gagal!')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
}

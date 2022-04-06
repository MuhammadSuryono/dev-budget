<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$id_user    = $_POST['id_user'];
$id_tb_user    = $_POST['id_tb_user'];
$nama    = $_POST['nama'];
$nama_user_old    = $_POST['nama_user_old'];
$bank    = $_POST['bank'];
$norek      = $_POST['norek'];
$action = $_POST['action'];
$status = $_POST['status'];

// $getBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE namapenerima='$nama_user_old' ORDER BY noid DESC LIMIT 1");
// $bpu = mysqli_fetch_assoc($getBpu);

// $getBudget = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$bpu[waktu]' AND no='$bpu[no]' AND (status='UM' OR status='UM Burek')");
// $budget = mysqli_fetch_assoc($getBudget);

$queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank='$bank'");
$kode_bank = mysqli_fetch_assoc($queryBank);

//periksa apakah udah submit
if (isset($_POST['submit'])) {
    if ($action == 'edit') {
        $update = mysqli_query($koneksi, "UPDATE bpu SET namabank='$bank', norek='$norek' WHERE rekening_id='$id_user'");

        // $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET pemilik_rekening='$nama_user', kode_bank='$bank', norek='$norek', bank='$kode_bank[namabank]' WHERE noid_bpu='$bpu[noid]'");
        if ($status == 'internal') {
            $update = mysqli_query($koneksi, "UPDATE rekening SET user_id = '$id_tb_user', status = '$status', nama = '$nama', bank='$bank', rekening='$norek' WHERE no='$id_user'");
        } else {
            $update = mysqli_query($koneksi, "UPDATE rekening SET user_id = null, status = '$status', nama = '$nama', bank='$bank', rekening='$norek' WHERE no='$id_user'");
        }

        if ($update) {
            echo "<script language='javascript'>";
            echo "alert('Edit Bank & Rekening Berhasil')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Edit Bank & Rekening Gagal')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        }
    }

    if ($action == 'hapus') {
        $update = mysqli_query($koneksi, "DELETE FROM rekening WHERE no = '$id_user'");

        if ($update) {
            echo "<script language='javascript'>";
            echo "alert('Data berhasil dihapus')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Data gagal dihapus')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        }
    }

    if ($action == 'tambah') {
        $insert = mysqli_query($koneksi, "INSERT INTO rekening VALUES('', '$id_tb_user', '$nama', '$norek','$bank', '$status', '')") or die(mysqli_error($koneksi));
        // die;
        if ($insert) {
            echo "<script language='javascript'>";
            echo "alert('Data berhasil ditambah')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Data gagal ditambah')";
            echo "</script>";
            echo "<script> document.location.href='saldobpu.php'; </script>";
        }
    }
}

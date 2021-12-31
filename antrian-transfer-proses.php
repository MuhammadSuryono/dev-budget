<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$koneksiTransfer = $con->connect();

$time = date("Y-m-d H:i:s");
$user = $_SESSION['nama_user'];

$id = $_POST['id'];
$button = $_POST['button'];
$from = $_POST['from'];
$ket_tambahan = $_POST['ket_tambahan'];
$jadwal_transfer = date('Y-m-d H:i:s', strtotime($_POST['jadwal_transfer']));
// var_dump($jadwal_transfer);
// die;

$getData = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer WHERE transfer_id = $id");
$data = mysqli_fetch_assoc($getData);

if ($button == 'edit') {
    $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET jadwal_transfer = '$jadwal_transfer', updated_at='$time', updated_by='$user', ket_tambahan='$ket_tambahan' WHERE transfer_id = '$id'");
}

if ($button == 'cancel') {
    $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET ket_transfer = 'Cancel', updated_at='$time', updated_by='$user', hasil_transfer = '4', ket_tambahan='$ket_tambahan', jadwal_transfer = null WHERE transfer_id = '$id'");
}

if ($button == 'antri') {
    $jadwal_transfer = $_POST['jadwaltransfer'];

    $date = date('my');
    $countQuery = mysqli_query($koneksiTransfer, "SELECT transfer_req_id FROM data_transfer WHERE transfer_req_id LIKE '$date%' ORDER BY transfer_req_id DESC LIMIT 1");
    $count = mysqli_fetch_assoc($countQuery);
    $count = (int)substr($count['transfer_req_id'], -4);

    $formatId = $date . sprintf('%04d', $count + 1);

    $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET ket_transfer = 'Antri', updated_at='$time', updated_by='$user', transfer_req_id = '$formatId', jadwal_transfer = '$jadwal_transfer', hasil_transfer = '1' WHERE transfer_id = '$id'");
}
if ($update) {
    echo "<script language='javascript'>";
    echo "alert('Data berhasil diperbarui')";
    echo "</script>";
    echo "<script> document.location.href='" . $from . "'; </script>";
} else {
    echo "<script language='javascript'>";
    echo "alert('Data gagal diperbarui')";
    echo "</script>";
    echo "<script> document.location.href='" . $from . "'; </script>";
}

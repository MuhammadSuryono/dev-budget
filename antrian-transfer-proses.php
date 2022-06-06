<?php
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();


$time = date("Y-m-d H:i:s");
$user = $_SESSION['nama_user'];

$id = $_POST['id'];
$button = $_POST['button'];
$from = $_POST['from'];
$ket_tambahan = $_POST['ket_tambahan'];
$jadwal_transfer = date('Y-m-d H:i:s', strtotime($_POST['jadwaltransfer']));
// var_dump($jadwal_transfer);
// die;

$getData = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer WHERE transfer_id = '$id'") or die(mysqli_errno($koneksiTransfer));
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

if ($button == 'antri-laporan') {
    $querybank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$_POST[bank]'");
    $bank = mysqli_fetch_assoc($querybank);

    $date = date('my');
    $countQuery = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer WHERE transfer_req_id LIKE '$date%' ORDER BY transfer_req_id DESC LIMIT 1");
    $transfer = mysqli_fetch_assoc($countQuery);
    $count = (int)substr($transfer['transfer_req_id'], -4);

    $jadwal = date('Y-m-d', strtotime($_POST['jadwaltransfer']));
    if ($transfer["multiple_bpu"] != null) {
        $explode = explode("|", $transfer["multiple_bpu"]);
        foreach($explode as $key => $value) {
            $queryBpu = mysqli_query($koneksi, "UPDATE bpu SET namabank = '$_POST[bank]', namapenerima = '$_POST[bank_account_name]', tanggalbayar='$jadwal' WHERE noid = '$value'");
        }
    } else {
        $queryBpu = mysqli_query($koneksi, "UPDATE bpu SET namabank = '$_POST[bank]', namapenerima = '$_POST[bank_account_name]', tanggalbayar='$jadwal' WHERE noid = '$transfer[noid_bpu]'");
    }

    $formatId = $date . sprintf('%04d', $count + 1);
    $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET ket_transfer = 'Antri', updated_at='$time', bank='$bank[namabank]', kode_bank='$_POST[bank]', pemilik_rekening='$_POST[bank_account_name]', updated_by='$user', transfer_req_id = '$formatId', jadwal_transfer = '$jadwal_transfer', hasil_transfer = '1' WHERE transfer_id = '$id'");
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

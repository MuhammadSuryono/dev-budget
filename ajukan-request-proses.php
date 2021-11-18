<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$arrNama = ['Honor Jakarta', 'Honor Luar Kota', 'STKB Transaksi Jakarta', 'STKB Transaksi Luar Kota', 'STKB OPS'];
$arrKota = ['Jabodetabek', 'Luar kota', 'Jabodetabek', 'Luar Kota', 'Jabodetabek dan Luar Kota'];
$arrStatus = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS'];
$arrPenerima = ['Shopper/PWT', 'Shopper/PWT', 'TLF', 'TLF', 'TLF'];

$id = $_GET['id'];
$keterangan = ($_GET['ket']) ? $_GET['ket'] : 'Tidak ada';
$kodepro = explode(',', $_GET['kodepro']);

$queryGetData = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id");
$data = mysqli_fetch_assoc($queryGetData);
$waktu = $data['waktu'];
$pembuatG = $data['pembuat'];
$pengaju = $data['pengaju'];
$namaProject = $data['nama'];
$divisi = $data['divisi'];
$totalbudget = $data['totalbudget'];
$jenis = $data['jenis'];

if ($jenis == 'B1' && $_GET['kodepro'] != 'undefined') {
    if (count($kodepro) == 1) {
        $kode = $kodepro[0];
        $updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET kode_project='$kode', waktu='$waktu' WHERE id=$id");
    } else {
        $queryPengajuanRequest = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id");
        $pengajuanRequest = mysqli_fetch_assoc($queryPengajuanRequest);
        $jenis = $pengajuanRequest['jenis'];
        $nama = $pengajuanRequest['nama'];
        $tahun = $pengajuanRequest['tahun'];
        $pembuat = $pengajuanRequest['pembuat'];
        $pengaju = $pengajuanRequest['pengaju'];
        $divisi = $pengajuanRequest['divisi'];
        $totalbudget = $pengajuanRequest['totalbudget'];
        $status_request = $pengajuanRequest['status_request'];
        $on_revision_status = $pengajuanRequest['on_revision_status'];
        $waktu = $pengajuanRequest['waktu'];
        for ($i = 0; $i < count($kodepro); $i++) {
            $kode = $kodepro[$i];
            if ($i == 0) {
                $updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET kode_project='$kode', waktu='$waktu' WHERE id=$id") or die(mysqli_error($koneksi));
            } else {
                $insertPengajuanRequest = mysqli_query($koneksi, "INSERT INTO pengajuan_request(jenis, nama, tahun, pembuat, pengaju, divisi, totalbudget, status_request, kode_project, on_revision_status, waktu) VALUES (
                                            '$jenis', 
                                            '$nama', 
                                            '$tahun',
                                            '$pembuat',
                                            '$pengaju',
                                            '$divisi',
                                            '$totalbudget',
                                            '$status_request',
                                            '$kode',
                                            '$on_revision_status',
                                            '$waktu')") or die(mysqli_error($koneksi));
            }
        }

        for ($j = 0; $j < count($kodepro) - 1; $j++) {
            $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
            $checkId = mysqli_fetch_assoc($queryCheckId)["id"];
            $checkId -= $j;
            for ($i = 0; $i  < count($arrNama); $i++) {
                $nama = $arrNama[$i];
                $kota = $arrKota[$i];
                $status = $arrStatus[$i];
                $pUang = $arrPenerima[$i];
                $checkId = (int)$checkId;
                $urutan = $i + 1;
                if ($nama != "") {
                    $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
                                                        '$urutan',
                                                        '$checkId',
                                                        '$nama',
                                                        '$kota',
                                                        '$status',
                                                        '$pUang',
                                                        '0',
                                                        '0',
                                                        '0',
                                                        '$namaUser',
                                                        '$divisiUser',
                                                        '$waktu')                           
                                                        ") or die(mysqli_error($koneksi));
                }
            }
        }
    }
}

$name = random_bytes(15);
if (@unserialize($data['document'])) {

    $document = unserialize($data['document']);
    if (is_array($document)) {
        $arrDocument = [];
        foreach ($document as $d) {
            array_push($arrDocument, $d);
        }
        array_push($arrDocument, $name);
        $document = serialize($arrDocument);
    } else {
        $arrDocument = [$document];
        array_push($arrDocument, $name);
        $document = serialize($arrDocument);
    }
} else {
    $document = serialize($name);
}

$email = [];
$nama = [];
$queryGetEmail = mysqli_query($koneksi, "SELECT email,nama_user from tb_user WHERE nama_user='$pembuatG' AND aktif='Y'");
$getEmail =  mysqli_fetch_assoc($queryGetEmail);
array_push($email, $getEmail['email']);
array_push($nama, $getEmail['nama_user']);

$queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
while ($e = mysqli_fetch_assoc($queryEmail)) {
    if ($e['email']) {
        array_push($email, $e['email']);
        array_push($nama, $e['nama_user']);
    }
}

$updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET status_request='Di Ajukan', waktu='$waktu', submission_note='$keterangan', document='$document' WHERE waktu='$waktu'") or die(mysqli_error($koneksi));
$queryGetAllId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request WHERE waktu='$waktu'");
saveDoc($koneksi, $id, $name);
while ($row = mysqli_fetch_array($queryGetAllId)) {
    $id = $row['id'];
    $updateSelesaiRequest = mysqli_query($koneksi, "UPDATE selesai_request SET waktu='$waktu' WHERE id_pengajuan_request=$id");
}

if ($updatePengajuanRequest) {

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $msg = "Dear $pembuatG, <br><br>
    Budget telah diajukan dengan keterangan sebagai berikut:<br><br>
    Nama Project    : <strong>$namaProject</strong><br>
    Pengaju         : <strong>$pengaju</strong><br>
    Divisi          : <strong>$divisi</strong><br>
    Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br>
    ";
    if ($keterangan) {
        $msg .= "Keterangan:<strong> $keterangan </strong><br><br>";
    } else {
        $msg .= "<br>";
    }
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Untuk Pengajuan Budget";
    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, 'multiple');
    }

    $notifikasi = "Data Berhasil Diajukan. Pemberitahuan via email telah terkirim ke ";
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notifikasi .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notifikasi .= ', ';
        else $notifikasi .= '.';
    }

    if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('$notifikasi')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notifikasi')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    }
} else {
    if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Data Gagal Diajukan')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Data Gagal Diajukan')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    }
}

function random_bytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}

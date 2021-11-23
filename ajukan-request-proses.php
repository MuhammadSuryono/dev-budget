<?php

session_start();
require "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/helper.php";
require_once "application/controllers/Cuti.php";

$messageHelper = new Message();

$helper = new Helper();
$host = $helper->getHostUrl();

$con = new Database();
$koneksi = $con->connect();
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

$name = randomBytes(15);
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

$phoneNumbers = [];
$nama = [];
$idUsersNotification = [];

if ($totalbudget > 1000000) {
    $queryEmail = mysqli_query($koneksi, "SELECT id_user,phone_number,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
    while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['phone_number']) {
            array_push($phoneNumbers, $e['phone_number']);
            array_push($nama, $e['nama_user']);
            array_push($idUsersNotification, $e['id_user']);
        }
    }
} else {
    $queryGetEmail = mysqli_query($koneksi, "SELECT id_user, email,nama_user, phone_number, divisi, level from tb_user WHERE nama_user='$pembuatG' AND aktif='Y'");
    $getEmail =  mysqli_fetch_assoc($queryGetEmail);
    array_push($phoneNumbers, $getEmail['phone_number']);
    array_push($nama, $getEmail['nama_user']);
    array_push($idUsersNotification, $getEmail['id_user']);

    $cuti = new Cuti();
    $struktural = ["Manager", 'Direksi'];
    if ($getEmail["divisi"] != "Finance") {
        $getDataUserFinance = false;
        foreach ($struktural as $struktur) {
            $queryEmail = mysqli_query($koneksi, "SELECT id_user,phone_number,nama_user FROM tb_user WHERE divisi='Finance' AND level = '$struktur' AND aktif='Y'");
            $e = mysqli_fetch_assoc($queryEmail);
           
            if (!$cuti->checkStatusCutiUser($e["nama_user"])) {
                $getDataUserFinance = true;
                while ($e) {
                    if ($e['phone_number']) {
                        array_push($phoneNumbers, $e['phone_number']);
                        array_push($nama, $e['nama_user']);
                        array_push($idUsersNotification, $getEmail['id_user']);
                    }
                }
            }
        }

    } else if ($getEmail["divisi"] == "Finance" && ($getEmail["level"] != "Manager" || $getEmail["level"] != "Senior Manager") && $cuti->checkStatusCutiUser($getEmail["nama_user"])) {
        $queryEmail = mysqli_query($koneksi, "SELECT id_user,phone_number,nama_user FROM tb_user WHERE divisi='Finance' AND aktif='Y'");
        $e = mysqli_fetch_assoc($queryEmail);
        if (!$cuti->checkStatusCutiUser($e["nama_user"])) {
            $getDataUserFinance = true;
            while ($e) {
                if ($e['phone_number']) {
                    array_push($phoneNumbers, $e['phone_number']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $getEmail['id_user']);
                }
            }
        }
    } else if ($getEmail["divisi"] == "Finance" && ($getEmail["level"] == "Manager" || $getEmail["level"] == "Senior Manager") && $cuti->checkStatusCutiUser($getEmail["nama_user"])) {
        $queryEmail = mysqli_query($koneksi, "SELECT id_user,phone_number,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number']) {
                array_push($phoneNumbers, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $getEmail['id_user']);
            }
        }
    }
}

// var_dump($nama);

$updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET status_request='Di Ajukan', waktu='$waktu', submission_note='$keterangan', document='$document' WHERE waktu='$waktu'") or die(mysqli_error($koneksi));
$queryGetAllId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request WHERE waktu='$waktu'");
saveDoc($koneksi, $id, $name);
while ($row = mysqli_fetch_array($queryGetAllId)) {
    $id = $row['id'];
    $updateSelesaiRequest = mysqli_query($koneksi, "UPDATE selesai_request SET waktu='$waktu' WHERE id_pengajuan_request=$id");
}

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$host = $url[0]. '/'. $url[1];

if ($updatePengajuanRequest) {
    $subject = "Notifikasi Untuk Pengajuan Budget";

    if ($phoneNumbers) {
        $wa = new Whastapp();

        for($i = 0; $i < count($phoneNumbers); $i++) {
            if ($phoneNumbers[$i] != "") {
                $url =  $host. '/view-request.php?id='.$id.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
                $msg = $messageHelper->messageAjukanBudget($nama[$i], $pengaju, $namaProject, $divisi, $totalbudget, $keterangan, $url);
                $wa->sendMessage($phoneNumbers[$i], $msg);
                if ($name != "") {
                    $wa->sendDocumentMessage($phone, $msg, getPathFile($host, $name));
                }
            }
        }
    }

    $notification = "Data Berhasil Diajukan. Pemberitahuan via whatsapp sedang dikirimkan ke ";
    for ($i = 0; $i < count($phoneNumbers); $i++) {
        if ($phoneNumbers[$i] != "") {
          $notification .= ($nama[$i] . ' (' . $phoneNumbers[$i] . ')');
          if ($i < count($phoneNumbers) - 1) $notification .= ', ';
          else $notification .= '.';
        }
    }

    if ($_SESSION['divisi'] == 'FINANCE') {
        echo $messageHelper->alertMessage($notification, "home-finance.php");
    } else {
        echo $messageHelper->alertMessage($notification, "home.php");
    }
} else {
    if ($_SESSION['divisi'] == 'FINANCE') {
        echo $messageHelper->alertMessage("Terjadi kesalahan sistem ketika melakukan pengajuan Budget. Silahkan ulangi kembali. Jika masih mengalami kendala, silahkan kontak Tim IT", "home-finance.php");
    } else {
        echo $messageHelper->alertMessage("Terjadi kesalahan sistem ketika melakukan pengajuan Budget. Silahkan ulangi kembali. Jika masih mengalami kendala, silahkan kontak Tim IT", "home.php");
    }
}

function randomBytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}

function getPathFile($host, $name, $ext = ".pdf")
{
    return $host."document/".$name.$ext;
}
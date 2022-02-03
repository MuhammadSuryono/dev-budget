<?php
session_start();
require "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/helper.php";
require_once "application/controllers/Cuti.php";
require_once "application/config/messageEmail.php";
require_once "application/config/email.php";

$messageHelper = new Message();
$messageEmail = new MessageEmail();
$emailSender = new Email();

$helper = new Helper();
$host = $helper->getHostUrl();

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$arrNama = ['Honor Jakarta', 'Honor Luar Kota', 'STKB Transaksi Jakarta', 'STKB Transaksi Luar Kota', 'STKB OPS'];
$arrKota = ['Jabodetabek', 'Luar kota', 'Jabodetabek', 'Luar Kota', 'Jabodetabek dan Luar Kota'];
$arrStatus = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS'];
$arrPenerima = ['Shopper/PWT', 'Shopper/PWT', 'TLF', 'TLF', 'TLF'];

$id = $_GET['id'];
$keterangan = isset($_GET['ket']) ? $_GET['ket'] : 'Tidak ada';
$kodepro = explode(',', $_GET['kodepro']);

$data = $con->select()->from('pengajuan_request')->where('id', '=', $id)->first();

$waktu = $data['waktu'];
$pembuatG = $data['pembuat'];
$pengaju = $data['pengaju'];
$namaProject = $data['nama'];
$divisi = $data['divisi'];
$totalbudget = $data['totalbudget'];
$jenis = $data['jenis'];

$duplciate = [];

if ($jenis == 'B1' && $_GET['kodepro'] != 'undefined') {
    if (count($kodepro) == 1) {
        $kode = $kodepro[0];
        $updatePengajuanRequest = $con->update('pengajuan_request')
            ->set_value_update('kode_project', $kode)
            ->set_value_update('waktu', $waktu)
            ->where('id', '=', $id)
            ->save_update();
    } else {
        $pengajuanRequest = $con->select()->from('pengajuan_request')->where('id', '=', $id)->first();
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
                $updatePengajuanRequest = $con->update('pengajuan_request')
                    ->set_value_update('kode_project', $kode)
                    ->set_value_update('waktu', $waktu)
                    ->where('id', '=', $id)
                    ->save_update();
            } else {
                $insertPengajuanRequest = $con->insert('pengajuan_request')
                    ->set_value_insert('jenis', $jenis)
                    ->set_value_insert('nama', $nama)
                    ->set_value_insert('tahun', $tahun)
                    ->set_value_insert('pembuat', $pembuat)
                    ->set_value_insert('pengaju', $pengaju)
                    ->set_value_insert('divisi', $divisi)
                    ->set_value_insert('totalbudget', $totalbudget)
                    ->set_value_insert('status_request', $status_request)
                    ->set_value_insert('kode_project', $kode)
                    ->set_value_insert('on_revision_status', $on_revision_status)
                    ->set_value_insert('waktu', $waktu)
                    ->save_insert();
            }
        }

        for ($j = 0; $j < count($kodepro) - 1; $j++) {
            $dataPengajuanRequestId = $con->select('id')->from('pengajuan_request')->order_by('ID', 'DESC')->first();
            $checkId = $dataPengajuanRequestId["id"];
            $checkId -= $j;
            for ($i = 0; $i  < count($arrNama); $i++) {
                $nama = $arrNama[$i];
                $kota = $arrKota[$i];
                $status = $arrStatus[$i];
                $pUang = $arrPenerima[$i];
                $checkId = (int)$checkId;
                $urutan = $i + 1;
                if ($nama != "") {
                    $insertSelesaiRequest = $con->insert('selesai_request')
                        ->set_value_insert('urutan', $urutan)
                        ->set_value_insert('id_pengajuan_request', $checkId)
                        ->set_value_insert('rincian', $nama)
                        ->set_value_insert('kota', $kota)
                        ->set_value_insert('status', $status)
                        ->set_value_insert('penerima', $pUang)
                        ->set_value_insert('harga', 0)
                        ->set_value_insert('quantity', 0)
                        ->set_value_insert('total', 0)
                        ->set_value_insert('pengaju', $namaUser)
                        ->set_value_insert('divisi', $divisiUser)
                        ->set_value_insert('waktu', $waktu)
                        ->save_insert();
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
$emails= [];
$namaUser = [];
$idUsersNotification = [];

if ($totalbudget > 1000000) {
    $dataUserDireksi = $con->select()->from('tb_user')->where('divisi', '=', 'Direksi')->where('aktif', '=', 'Y')->first();
    if ($dataUserDireksi['phone_number']) {
        array_push($emails, $dataUserDireksi['email']);
        array_push($phoneNumbers, $dataUserDireksi['phone_number']);
        array_push($namaUser, $dataUserDireksi['nama_user']);
        array_push($idUsersNotification, $dataUserDireksi['id_user']);
    }
} else {
    $cuti = new Cuti();
    $userFinance = $con->select()->from('tb_user')
        ->where('divisi', '=', 'Finance')
        ->where('hak_akses', '=', 'Manager')
        ->where('aktif', '=', 'Y')
        ->first();

    if ($cuti->checkStatusCutiUser($userFinance['nama_user'])) {
        $dataUserDireksi = $con->select()->from('tb_user')->where('divisi', '=', 'Direksi')->where('aktif', '=', 'Y')->first();
        if ($dataUserDireksi['phone_number']) {
            array_push($emails, $dataUserDireksi['email']);
            array_push($phoneNumbers, $dataUserDireksi['phone_number']);
            array_push($namaUser, $dataUserDireksi['nama_user']);
            array_push($idUsersNotification, $dataUserDireksi['id_user']);
        }
    } else {
        if ($userFinance['phone_number'] && !in_array($userFinance['nama_user'], $duplciate)) {
            array_push($emails, $userFinance['email']);
            array_push($phoneNumbers, $userFinance['phone_number']);
            array_push($namaUser, $userFinance['nama_user']);
            array_push($idUsersNotification, $userFinance['id_user']);
            array_push($duplciate, $userFinance['phone_number']);
        }
    }
}
$updatePengajuanRequest = $con->update('pengajuan_request')
    ->set_value_update('status_request', 'Di Ajukan')
    ->set_value_update('waktu', $waktu)
    ->set_value_update('submission_note', $keterangan)
    ->set_value_update('document', $document)
    ->where('waktu','=', $waktu)
    ->save_update();

saveDoc($koneksi, $id, $name);

$row = $con->select('id')->where('waktu', '=', $waktu)->first();

$id = $row['id'];
$updateSelesaiRequest = $con->update('selesai_request')->set_value_update('waktu', $waktu)->where('id_pengajuan_request', '=', $id)->save_update();

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "" || $port != "80") {
    $hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

if ($updatePengajuanRequest) {
    if ($phoneNumbers) {
        $wa = new Whastapp();

        for($i = 0; $i < count($phoneNumbers); $i++) {
            if ($phoneNumbers[$i] != "") {
                $url =  $host. '/view-request.php?id='.$id.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
                $msg = $messageHelper->messageAjukanBudget($namaUser[$i], $pengaju, $namaProject, $divisi, $totalbudget, $keterangan, $url);
                $msgEmail = $messageEmail->applyBudget($namaUser[$i], $pengaju, $namaProject, $divisi, $totalbudget, $keterangan, $url);
                $wa->sendMessage($phoneNumbers[$i], $msg);
                $emailSender->sendEmail($msgEmail, 'Notifikasi Pengajuan Budget ' . $namaProject, $emails[$i]);
            }
        }
    }

    $notification = "Data Berhasil Diajukan. Pemberitahuan via whatsapp sedang dikirimkan ke ";
    for ($i = 0; $i < count($phoneNumbers); $i++) {
        if ($phoneNumbers[$i] != "") {
          $notification .= ($namaUser[$i] . ' (' . $phoneNumbers[$i] . ')');
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
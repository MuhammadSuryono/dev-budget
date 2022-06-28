<?php
error_reporting(0);
session_start();
require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
require_once "application/controllers/Cuti.php";
require_once "application/config/messageEmail.php";
require_once "application/config/email.php";

$helper = new Message();
$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

$messageEmail = new MessageEmail();
$emailSender = new Email();

require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$user = $_SESSION['nama_user'];
$time = date("Y-m-d h:i:sa");
$id = $_GET['id'];

$dataPengajuan = $con->select()->from('pengajuan_request')->where('id', '=', $id)->first();
$waktu = $dataPengajuan['waktu'];
$gPengaju = '';
$gNamaProject = '';
$gTotalBudget = '';
$gPembuat = $_SESSION['nama_user'];

$duplicateStatus = 0;
$idPengajuan = 0;

$updatePengajuanRequest = $con->update('pengajuan_request')
    ->set_value_update('status_request', 'Disetujui')
    ->set_value_update('waktu', $waktu)
    ->set_value_update('on_revision_status', 0)
    ->where('waktu', '=', $waktu)
    ->save_update();

if ($updatePengajuanRequest) {
    $waktu = $con->select('waktu')->from('pengajuan_request')->where('id', '=', $id)->order_by('id', 'desc')->first();
    $waktunya = $waktu['waktu'];

    $queryGetAllId = $con->select('id')->from('pengajuan_request')->where('waktu', '=', $waktunya)->get();
    foreach ($queryGetAllId as $row) {

        $idGet = $row['id'];
        $pengajuanRequest = $con->select()->from('pengajuan_request')->where('id', '=', $idGet)->first();
        $jenis = $pengajuanRequest['jenis'];
        $nama = $pengajuanRequest['nama'];
        $gNamaProject = $nama;
        $tahun = $pengajuanRequest['tahun'];
        $pengaju = $pengajuanRequest['pengaju'];
        $pembuat = $pengajuanRequest['pembuat'];
        $gPengaju = $pengaju;
        $divisi = $pengajuanRequest['divisi'];
        $kodepro = $pengajuanRequest['kode_project'];
        $totalbudget = $pengajuanRequest['totalbudget'];
        $gTotalBudget = $totalbudget;
        $document = $pengajuanRequest['document'];

        $checkData = $con->select('COUNT(*) AS count_check')->from('pengajuan')->where('kodeproject', '=', $kodepro)->where('waktu', '=', $waktunya)->first();
        if ($checkData['count_check'] == 0) {
            $insertkepengaju = $con->insert('pengajuan')
                ->set_value_insert('jenis', $jenis)
                ->set_value_insert('nama', $nama)
                ->set_value_insert('tahun', $tahun)
                ->set_value_insert('pengaju', $pengaju)
                ->set_value_insert('divisi', $divisi)
                ->set_value_insert('status', 'Disetujui')
                ->set_value_insert('kodeproject', $kodepro)
                ->set_value_insert('totalbudget', $totalbudget)
                ->set_value_insert('totalbudgetnow', $totalbudget)
                ->set_value_insert('pembuat', $pembuat)
                ->set_value_insert('waktu', $waktunya)
                ->set_value_insert('penyetuju', $user)
                ->set_value_insert('date_approved', $time)
                ->set_value_insert('document', $document)
                ->set_value_insert('on_revision_status', 0)
                ->save_insert();

            $idPengajuan = $con->get_id_insert();
        } else {
            $duplicateStatus = 1;
        }
    }

    if (!$insertkepengaju && $duplicateStatus == 1) {
        $updatePengajuanRequestDouble = $con->update('pengajuan_request')
            ->set_value_update('ket', 'Data Double')
            ->set_value_update('waktu', $waktu['waktu'])
            ->set_value_update('on_revision_status', 0)
            ->where('waktu', '=', $waktu['waktu'])
            ->save_update();

        if ($_SESSION['divisi'] == 'FINANCE') {
            echo $helper->alertMessage('Proses dihentikan, Terindikasi data double', 'home-finance.php');
        } else {
            echo $helper->alertMessage('Proses dihentikan, Terindikasi data double', 'home-direksi.php');
        }
        die;
    }

    $dataSelesaiRequest = $con->select()->from('selesai_request')->where('waktu', '=', $waktunya)->get();
    $selesaiRequest = [];
    $checkName = [];
    foreach ($dataSelesaiRequest as $row ) {
        if (!in_array($row["rincian"], $checkName)) {
            array_push($selesaiRequest, $row);
            array_push($checkName, $row['rincian']);
        }
    }

    $countTrue = 0;
    foreach ($selesaiRequest as $sr) {
        $urutan = $sr['urutan'];
        $rincian = $sr['rincian'];
        $kota = $sr['kota'];
        $status = $sr['status'];
        $penerima = $sr['penerima'];
        $harga = $sr['harga'];
        $quantity = $sr['quantity'];
        $total = $sr['total'];
        $pengaju = $sr['pengaju'];
        $divisi = $sr['divisi'];


        if ($rincian != "") {
            $insertSelesai = $con->insert('selesai')
                ->set_value_insert('no', $urutan)
                ->set_value_insert('rincian', $rincian)
                ->set_value_insert('kota', $kota)
                ->set_value_insert('status', $status)
                ->set_value_insert('penerima', $penerima)
                ->set_value_insert('harga', $harga)
                ->set_value_insert('quantity', $quantity)
                ->set_value_insert('total', $total)
                ->set_value_insert('pembayaran', '')
                ->set_value_insert('pengaju', $pengaju)
                ->set_value_insert('divisi', $divisi)
                ->set_value_insert('waktu', $waktunya)
                ->set_value_insert('komentar', '$document')
                ->set_value_insert('uangkembaliused', '')
                ->save_insert();
        }
        if ($insertkepengaju) $countTrue++;
    }
    if ($countTrue >= count($selesaiRequest)) {

        $phoneNumbers = [];
        $namaUserSendNotifications = [];
        $idUsersNotification = [];
        $dataDivisi = [];
        $dataLevel = [];
        $emails = [];

        $dataPembuat = $con->select()->from('tb_user')->where('nama_user', '=', $pembuat)->where('aktif', '=', 'Y')->first();
        $divisi = $dataPembuat['divisi'];
        array_push($phoneNumbers, $dataPembuat['phone_number']);
        array_push($namaUserSendNotifications, $dataPembuat['nama_user']);
        array_push($idUsersNotification, $dataPembuat['id_user']);
        array_push($dataDivisi, $dataPembuat['divisi']);
        array_push($dataLevel, $dataPembuat['level']);
        array_push($emails, $dataPembuat['email']);

        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $port = $_SERVER['SERVER_PORT'];
        $url = explode('/', $url);
        $hostProtocol = $url[0];
        if ($port != "") {
        $hostProtocol = $hostProtocol . ":" . $port;
        }
        $host = $hostProtocol. '/'. $url[1];

        $notification = 'Persetujuan Berhasil. Pemberitahuan via whatsapp sedang dikirimkan ke ';
        if (count($phoneNumbers) > 0) {
            $whatsapp = new Whastapp();
            $cuti = new Cuti();
            for($i = 0; $i < count($phoneNumbers); $i++) {
                if (!$cuti->checkStatusCutiUser($namaUserSendNotifications[$i]) || $namaUserSendNotifications[$i] != $_SESSION['nama_user'])
                {
                    $notification .= ($namaUserSendNotifications[$i] . ' (' . $phoneNumbers[$i] . ')');
                    if ($i < count($phoneNumbers) - 1) $notification .= ', ';
                    else $notification .= '.';

                    $path = '/views.php';
                    if ($dataDivisi[$i] == 'FINANCE') {
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                        $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                        $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                    } else if ($dataDivisi[$i] == 'Direksi') {
                        $path = '/views-direksi.php';
                    }
                    $url =  $host. $path.'?code='.$idPengajuan.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
                    $msg = $helper->messagePersetujuanBudget($namaUserSendNotifications[$i], $pengaju, $gNamaProject, $divisi, $gTotalBudget, $gPembuat, $_SESSION['nama_user'], $url);
                    $msgEmail = $messageEmail->approvedBudget($namaUserSendNotifications[$i], $pengaju, $gNamaProject, $divisi, $gTotalBudget, $gPembuat, $url);
                    if($phoneNumbers[$i] != "") $whatsapp->sendMessage($phoneNumbers[$i], $msg);
                    if ($emails[$i] != "") $emailSender->sendEmail($msgEmail, "Notifikasi Persetujuan Budget", $emails[$i]);
                }

            }
          }

        $document = $con->select('noid, document')->from('pengajuan')->first();
        $doc = unserialize($document['document']);
        $noid = $document['noid'];

        if (is_array($doc)) {
            $name = $doc[count($doc) - 1];
        } else {
            $name = $doc;
        }

        saveDocApproved($koneksi, $noid, $name);
        if ($_SESSION['divisi'] == 'FINANCE') {
            echo $helper->alertMessage($notification, 'home-finance.php');

        } else {
            echo $helper->alertMessage($notification, 'home-direksi.php');
        }
    } else {
        if ($_SESSION['divisi'] == 'FINANCE') {
            echo $helper->alertMessage('Peresetujuan Gagal', 'home-finance.php');
        } else {
            echo $helper->alertMessage('Peresetujuan Gagal', 'home-direksi.php');
        }
    }
} else {
    if ($_SESSION['divisi'] == 'FINANCE') {
        echo $helper->alertMessage('Peresetujuan Gagal', 'home-finance.php');
    } else {
        echo $helper->alertMessage('Peresetujuan Gagal', 'home-direksi.php');
    }
}

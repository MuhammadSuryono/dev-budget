<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require_once "application/config/whatsapp.php";
require_once "application/config/helper.php";
require_once "application/config/message.php";
require_once "application/config/email.php";
require_once "application/config/messageEmail.php";

session_start();
$helper = new Helper();
$messageHelper = new Message();
$whatsapp = new Whastapp();
$emailHelper = new Email();
$messageEmail = new MessageEmail();

$userCheck = $_SESSION['nama_user'];
$now = date_create('now')->format('Y-m-d H:i:s');

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$kode = $_POST['kode'];

$update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu = 1, acknowledged_by='$userCheck', tgl_acknowledged='$now' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");
$bpu = mysqli_fetch_assoc($queryBpu);

$queryPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$pengajuan = mysqli_fetch_assoc($queryPengajuan);
$numb = $pengajuan['noid'];


$email = [];
$nama = [];
$idUsersNotification = [];
$dataDivisi = [];
$dataLevel = [];
$arremailpenerima = [];
$emails = [];

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
    if ($bpu['pengajuan_jumlah'] > 1000000) {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number']) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
                array_push($emails, $e['email']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
                $buttonAkses = unserialize($e['hak_button']);
                if (in_array("verifikasi_bpu", $buttonAkses)) {
                    if ($e['phone_number']) {
                        array_push($email, $e['phone_number']);
                        array_push($nama, $e['nama_user']);
                        array_push($idUsersNotification, $e['id_user']);
                        array_push($dataDivisi, $e['divisi']);
                        array_push($dataLevel, $e['level']);
                        array_push($emails, $e['email']);
                    }
                }
            }
        }
    }
    
} else {
    if ($bpu['pengajuan_jumlah'] > 1000000) {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number']) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
                array_push($emails, $e['email']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
                $buttonAkses = unserialize($e['hak_button']);
                if (in_array("verifikasi_bpu", $buttonAkses)) {
                    if ($e['phone_number']) {
                        array_push($email, $e['phone_number']);
                        array_push($nama, $e['nama_user']);
                        array_push($idUsersNotification, $e['id_user']);
                        array_push($dataDivisi, $e['divisi']);
                        array_push($dataLevel, $e['level']);
                        array_push($emails, $e['email']);
                    }
                }
            }
        }
    }
    
}


$queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$pengajuan[pembuat]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    if ($emailUser['divisi'] != 'FINANCE' && ($emailUser['level'] != "Manager" || $emailUser['level'] != "Senior Manager")) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($dataLevel, $emailUser['level']);
        array_push($emails, $e['email']);
    }
}

$queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    array_push($email, $emailUser['phone_number']);
    array_push($nama, $emailUser['nama_user']);
    array_push($idUsersNotification, $emailUser['id_user']);
    array_push($dataDivisi, $emailUser['divisi']);
    array_push($dataLevel, $emailUser['level']);
    array_push($emails, $e['email']);
}

$subject = "Notifikasi Aplikasi Budget";

array_unique($email);
array_unique($nama);


$notification = 'Persetujuan BPU Sukses. Pemberitahuan via whatsapp sedang dikirimkan ke ';
$i = 0;
for ($i = 0; $i < count($email); $i++) {
    if ($email[$i] != "" || $nama[$i] != $_SESSION['nama_user']) {
        $path = '/views.php';
        if ($dataDivisi[$i] == 'FINANCE') {
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
            $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
        } else if ($dataDivisi[$i] == 'Direksi') {
            $path = '/views-direksi.php';
        }
        $url =  $host. $path.'?code='.$id.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
        $msg = $messageHelper->messagePengajuanBPUKadiv($nama[$i], $bpu['pengaju'], $namaProject, $bpu['namapenerima'], $bpu['pengajuan_jumlah'], $keterangan, $url);
        $msgEmail = $messageEmail->applyBpuKadiv($nama[$i], $bpu['pengaju'], $namaProject, $bpu['namapenerima'], $bpu['pengajuan_jumlah'], $keterangan, $url);
        $emailHelper->sendEmail($msgEmail, "Informasi BPU Di Ketahui KADIV", $emails[$i]);
        $whatsapp->sendMessage($email[$i], $msg);

        if ($i++ < count($email) - 1) $notification .= $nama[$i].'('.$email[$i].'),';
        else $notification .= '.';
    }
}

if ($update) {
    echo "<script language='javascript'>";
    echo "alert('$notification')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
} else {
    echo "<script language='javascript'>";
    echo "alert('Verifikasi BPU Gagal!')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
}

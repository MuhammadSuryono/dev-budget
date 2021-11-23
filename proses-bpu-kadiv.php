<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require_once "application/config/whatsapp.php";
require_once "application/config/helper.php";
require_once "application/config/message.php";

session_start();
$helper = new Helper();
$messageHelper = new Message();
$whatsapp = new Whastapp();

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

if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
    while ($e = mysqli_fetch_assoc($queryEmail)) {
        if (@unserialize($e['hak_button'])) {
            $buttonAkses = unserialize($e['hak_button']);
            if (in_array("verifikasi_bpu", $buttonAkses)) {
                if ($e['phone_number']) {
                    array_push($email, $e['phone_number']);
                    array_push($nama, $e['nama_user']);
                }
            }
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
                }
            }
        }
    }
}

$queryEmail = mysqli_query($koneksi, "SELECT phone_number,nama_user,divisi FROM tb_user WHERE nama_user = '$pengajuan[pembuat]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    array_push($email, $emailUser['phone_number']);
    array_push($nama, $emailUser['nama_user']);
}

$queryEmail = mysqli_query($koneksi, "SELECT phone_number,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    array_push($email, $emailUser['phone_number']);
    array_push($nama, $emailUser['nama_user']);
}

$queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
$namaProject = mysqli_fetch_array($queryProject)[0];

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

// $msg = "Notifikasi BPU, <br><br>
//   BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
//   Nama Project      : <strong>$namaProject</strong><br>
//   Nama Pengaju      : <strong>" . $bpu['pengaju'] . "</strong><br>
//   Nama Penerima     : <strong>" . $bpu['namapenerima'] . "</strong><br>
//   Jumlah Diajukan   : <strong>Rp. " . number_format($bpu['pengajuan_jumlah'], 0, '', ',') . "</strong><br>
//   ";
// if ($keterangan) {
//     $msg .= "Keterangan:<strong> $keterangan </strong><br><br>";
// } else {
//     $msg .= "<br>";
// }
// $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
$subject = "Notifikasi Aplikasi Budget";



$notification = 'Persetujuan BPU Sukses. Pemberitahuan via whatsapp sedang dikirimkan ke ';
$i = 0;
for ($i = 0; $i < count($email); $i++) {
    if ($email[$i] != "") {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        $whatsapp->sendMessage($email[$i], $messageHelper->messagePengajuanBPU($bpu['pengaju'], $namaProject, $bpu['namapenerima'], $bpu['pengajuan_jumlah'], $keterangan, $url));
        if ($i++ < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }
}

// if ($email) {
//     $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
// }

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

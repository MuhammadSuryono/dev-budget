<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$no               = $_POST['no'];
$waktu            = $_POST['waktu'];
$term             = $_POST['term'];
$realisasi        = $_POST['realisasi'];
$uangkembali      = $_POST['uangkembali'];
$tanggalrealisasi = $_POST['tanggalrealisasi'];
$status           = $_POST['status'];
$totalBpu         = $_POST['totalbpu'];
$sisa         = $_POST['sisa'];


$extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
$newName = random_bytes(20) . "." . $extension;
$target_file = "uploads/" . $newName;
move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

$queryPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$pengajuan = mysqli_fetch_assoc($queryPengajuan);

//periksa apakah udah submit
if (isset($_POST['submit'])) {

    $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];

    if (($realisasi + $uangkembali) > $sisa) {
        echo "<script language='javascript'>";
        echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebihi BPU.')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
        die;
    }

    if (!$realisasi || !$tanggalrealisasi) {
        echo "<script language='javascript'>";
        echo "alert('Pengajuan Realisasi Gagal, Harap isi semua data.')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
        die;
    }

    if (($realisasi > $totalBpu) || ($uangkembali > $totalBpu)) {
        echo "<script language='javascript'>";
        echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebih BPU.')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
        die;
    }

    $update = mysqli_query($koneksi, "UPDATE bpu SET pengajuan_realisasi ='$realisasi',
                                          pengajuan_uangkembali ='$uangkembali',
                                          pengajuan_tanggalrealisasi ='$tanggalrealisasi',
                                          fileupload_realisasi = '$newName',
                                          status_pengajuan_realisasi = 3
                                          WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));

    if ($uc['jenis'] == 'B1' || $uc['jenis'] == 'B2') {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
                $buttonAkses = unserialize($e['hak_button']);
                if (in_array("verifikasi_bpu", $buttonAkses)) {
                    if ($e['email']) {
                        array_push($email, $e['email']);
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
                    if ($e['email']) {
                        array_push($email, $e['email']);
                        array_push($nama, $e['nama_user']);
                    }
                }
            }
        }
    }

    $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
    $namaProject = mysqli_fetch_array($queryProject)[0];

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $email = [];
    $nama = [];
    while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
            array_push($email, $e['email']);
            array_push($nama, $e['nama_user']);
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
    while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
            array_push($email, $e['email']);
            array_push($nama, $e['nama_user']);
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengajuan[pembuat]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['email']);
        array_push($nama, $emailUser['nama_user']);
    }

    $msg = "Notifikasi BPU, <br><br>
        Realisasi telah diajukan dengan keterangan sebagai berikut:<br><br>
        Nama Project          : <strong>$namaProject</strong><br>
        Item No.              : <strong>$no</strong><br>
        Term                  : <strong>$term</strong><br>
        Realisasi Diajukan    : <strong>Rp. " . number_format($realisasi, 0, '', ',') . "</strong><br>
        Uang Kembali Diajukan : <strong>Rp. " . number_format($uangkembali, 0, '', ',') . "</strong><br>
        Tanggal Realisasi     : <strong>$tanggalrealisasi</strong><br>
        ";
    if ($keterangan) {
        $msg .= "Keterangan:<strong> $keterangan </strong><br><br>";
    } else {
        $msg .= "<br>";
    }
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Aplikasi Budget";

    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    $notification = 'Realisasi Berhasil. Pemberitahuan via email telah terkirim ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }

    //jika sudah berhasil
    if ($update) {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Edit Budget Gagal!!')";
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

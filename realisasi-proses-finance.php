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
$sisa           = $_POST['sisa'];
$alasanTolakRealisasi           = $_POST['alasanTolakRealisasi'];
$submit           = $_POST['submit'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

    $sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];

    if (($realisasi + $uangkembali) > $sisa) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebihi BPU.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
            die;
        } else {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebihi BPU.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
            die;
        }
    }

    if (!$realisasi || !$tanggalrealisasi) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Harap isi semua data.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
            die;
        } else {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Harap isi semua data.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
            die;
        }
    }

    if (($realisasi > $totalBpu) || ($uangkembali > $totalBpu)) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebih BPU.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
            die;
        } else {
            echo "<script language='javascript'>";
            echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebih BPU.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
            die;
        }
    }


    $queryBpu = mysqli_query($koneksi, "SELECT pengaju,namapenerima FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    $bpu = mysqli_fetch_assoc($queryBpu);
    $pengaju = $bpu['pengaju'];
    $namapenerima = $bpu['namapenerima'];

    if ($submit == 1) {
        $update = mysqli_query($koneksi, "UPDATE bpu SET 
                                          status_pengajuan_realisasi = 3
                                          WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");

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

        $msg = "Notifikasi BPU, <br><br>
        Realisasi telah disetujui dengan keterangan sebagai berikut:<br><br>
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

        $notification = 'Status Berhasil Diubah. Pemberitahuan via email telah terkirim ke ';
        $i = 0;
        for ($i = 0; $i < count($email); $i++) {
            $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
            if ($i++ < count($email) - 1) $notification .= ', ';
            else $notification .= '.';
        }
    } else {
        $update = mysqli_query($koneksi, "UPDATE bpu SET
                                          status_pengajuan_realisasi = 2,
                                          alasan_tolak_realisasi = '$alasanTolakRealisasi'
                                          WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));

        $queryEmail = mysqli_query($koneksi, "SELECT email FROM tb_user WHERE nama_user='$namapenerima' AND aktif='Y'");
        $email = mysqli_fetch_array($queryEmail)[0];

        $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
        $namaProject = mysqli_fetch_array($queryProject)[0];

        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $url = explode('/', $url);
        $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

        $msg = "Notifikasi BPU, <br><br>
        Realisasi dengan keterangan sebagai berikut:<br><br>
        Nama Project          : <strong>$namaProject</strong><br>
        Term                  : <strong>$term</strong><br>
        Realisasi Diajukan    : <strong>Rp. " . number_format($realisasi, 0, '', ',') . "</strong><br>
        Uang Kembali Diajukan : <strong>Rp. " . number_format($uangkembali, 0, '', ',') . "</strong><br>
        Tanggal Realisasi     : <strong>$tanggalrealisasi</strong><br><bn
        ";
        if ($alasanTolakRealisasi) {
            $msg .= "Ditolak dengan alasan <strong> $alasanTolakRealisasi </strong>.<br><br>";
        } else {
            $msg .= "Ditolak tanpa alasan.<br><br>";
        }
        $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
        $subject = "Notifikasi Untuk Pengajuan Budget";

        if ($email) {
            $message = sendEmail($msg, $subject, $email, $name, $address = "single");
        }

        $notification = "Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke $namapenerima ($email)";
    }

    //jika sudah berhasil
    if ($update) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
        }
    } else {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Status Gagal Diubah.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $numb . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Status Gagal Diubah.')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $numb . "'; </script>";
        }
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

<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

session_start();

function random_bytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$kode = $_POST['kode'];
$total = $_POST['jumlah'];
$penerima = ($_POST['namapenerimaAjukanKembali']) ? $_POST['namapenerimaAjukanKembali'] : $_POST['namapenerima'];
$bank = ($_POST['namabankAjukanKembali']) ? $_POST['namabankAjukanKembali'] : $_POST['namabank'];
$norek = ($_POST['norekAjukanKembali']) ? $_POST['norekAjukanKembali'] : $_POST['norek'];
$pengaju = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];


$extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
$nama_gambar = random_bytes(20) . "." . $extension;
$target_file = "uploads/" . $nama_gambar;
move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

$queryPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$pengajuan = mysqli_fetch_assoc($queryPengajuan);

$email = [];
$nama = [];
if ($divisi == 'FINANCE') {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=1, pengajuan_jumlah=$total, fileupload='$nama_gambar', namapenerima='$penerima', namabank='$bank', norek='$norek' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
    if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['email']) {
                array_push($email, $e['email']);
                array_push($nama, $e['nama_user']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['email']) {
                array_push($email, $e['email']);
                array_push($nama, $e['nama_user']);
            }
        }
    }
} else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=3, pengajuan_jumlah=$total, fileupload='$nama_gambar', namapenerima='$penerima', namabank='$bank', norek='$norek' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['email']);
        array_push($nama, $emailUser['nama_user']);
    }

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    if ($user) {
        array_push($email, $user['email']);
        array_push($nama, $user['nama_user']);
    }
}


$queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
$namaProject = mysqli_fetch_array($queryProject)[0];

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

// $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
// while ($e = mysqli_fetch_assoc($queryEmail)) {
//     if ($e['email']) {
//         array_push($email, $e['email']);
//         array_push($nama, $e['nama_user']);
//     }
// }

$queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengajuan[pembuat]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);
}

$msg = "Notifikasi BPU, <br><br>
              BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
              Nama Project      : <strong>$namaProject</strong><br>
              Nama Pengaju      : <strong>$pengaju</strong><br>
              Nama Penerima     : <strong>$penerima</strong><br>
              Jumlah Diajukan   : <strong>Rp. " . number_format($total, 0, '', ',') . "</strong><br>
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

if ($update) {
    $notification = 'Pengajuan Kembali BPU Sukses. Pemberitahuan via email telah terkirim ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }

    if ($_SESSION['divisi'] == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance-manager.php?code=" . $kode . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance.php?code=" . $kode . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
    }

    // echo "<script language='javascript'>";
    // echo "alert('$notification')";
    // echo "</script>";
    // echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
} else {
    echo "<script language='javascript'>";
    echo "alert('Pengajuan Kembali BPU Gagal!')";
    echo "</script>";
    echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
}

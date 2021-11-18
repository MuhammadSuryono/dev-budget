<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$kode = $_POST['kode'];
$submit = $_POST['submit'];
$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$totalBpu = $_POST['totalbpu'];
$realisasi = $_POST['realisasi'];
$uangKembali = $_POST['uangkembali'];
$tanggalRealisasi = $_POST['tanggalrealisasi'];
$sisaRealisasi = $_POST['sisa'];
$alasanTolakRealisasi = $_POST['alasanTolakRealisasi'];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu = '$waktu' AND term='$term'");
$bpu = mysqli_fetch_assoc($queryBpu);
$getRealisasi        = $bpu['realisasi'];
$getUangkembali      = $bpu['uangkembali'];
$getJumlbayar        = $bpu['jumlah'];
$kembreal         = $getRealisasi + $getUangkembali;
$sisarealisasi    = $getJumlbayar - $kembreal;
$newRealisasi = $getRealisasi + $realisasi;
$newUangKembali = $getUangkembali + $uangKembali;

$queryBpu = mysqli_query($koneksi, "SELECT pengaju,namapenerima,pengajuan_jumlah FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
$bpu = mysqli_fetch_assoc($queryBpu);
$namapenerima = $bpu['namapenerima'];

$selbay = mysqli_query($koneksi, "SELECT noid,jenis,pengaju FROM pengajuan WHERE waktu='$waktu'");
$s = mysqli_fetch_assoc($selbay);


if (!$realisasi || !$tanggalRealisasi) {
    echo "<script language='javascript'>";
    echo "alert('Realisasi Gagal, Harap isi semua data.')";
    echo "</script>";
    echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    die;
}

if ($submit == 0) {
    $queryUpdateBpu = mysqli_query($koneksi, "UPDATE bpu SET fileupload='', status_pengajuan_realisasi = '2', alasan_tolak_realisasi = '$alasanTolakRealisasi' WHERE no='$no' AND waktu = '$waktu' AND term='$term'") or die(mysqli_error($koneksi));

    $email = [];
    $nama = [];

    $queryBpu = mysqli_query($koneksi, "SELECT pengaju,namapenerima,jumlah FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    $bpu = mysqli_fetch_assoc($queryBpu);
    $namapenerima = $bpu['namapenerima'];
    $jumlah = $bpu['jumlah'];

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$s[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);

    if ($s['jenis'] == 'B1' || $s['jenis'] == 'B2') {
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

    // $queryGetEmail = mysqli_query($koneksi, "SELECT email,divisi,nama_user from tb_user WHERE nama_user='$namapenerima'");
    // $data = mysqli_fetch_assoc($queryGetEmail);
    // $divisi = $data['divisi'];
    // array_push($email, $data['email']);
    // array_push($nama, $data['nama_user']);

    // $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager')") or die(mysqli_error($koneksi));
    // if ($queryUserByDivisi) {
    //     $user = mysqli_fetch_assoc($queryUserByDivisi);
    //     array_push($email, $user['email']);
    //     array_push($nama, $user['nama_user']);
    // }

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
        Uang Kembali Diajukan : <strong>Rp. " . number_format($uangKembali, 0, '', ',') . "</strong><br>
        Tanggal Realisasi     : <strong>$tanggalRealisasi</strong><br><br>
        ";
    if ($alasanTolakRealisasi) {
        $msg .= "Ditolak dengan alasan <strong> $alasanTolakRealisasi </strong>.<br><br>";
    } else {
        $msg .= "Ditolak tanpa alasan.<br><br>";
    }
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Untuk Pengajuan Budget";

    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    $notification = "Realisasi berhasil ditolak. Pemberitahuan via email telah terkirim ke ";

    for ($i = 0; $i < count($email); $i++) {
        // var_dump($notification);
        // echo "<br>";
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }
    // var_dump($notification);
    // die;
    if ($queryUpdateBpu) {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Realisasi Gagal Ditolak')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    }
} else if ($submit == 1) {

    // if ($realisasi > $sisarealisasi) {
    //     echo "<script language='javascript'>";
    //     echo "alert('Realisasi Gagal, Total Realisasi melebihi sisa realisasi.')";
    //     echo "</script>";
    //     echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    //     die;
    // }

    if (($realisasi + $uangKembali) > $sisaRealisasi) {
        echo "<script language='javascript'>";
        echo "alert('Pengajuan Realisasi Gagal, Total Realisasi/Uang Kembali Melebihi BPU.')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
        die;
    }

    if (($realisasi > $totalBpu) || ($uangKembali > $totalBpu)) {
        echo "<script language='javascript'>";
        echo "alert('Realisasi Gagal, Total Realisasi/Uang Kembali Melebih BPU.')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
        die;
    }

    if ($totalBpu == $newRealisasi + $newUangKembali) {
        $queryUpdateBpu = mysqli_query($koneksi, "UPDATE bpu SET status = 'Realisasi (Finance)', realisasi='$newRealisasi',uangkembali = '$newUangKembali', tanggalrealisasi='$tanggalRealisasi', status_pengajuan_realisasi = '4' WHERE no='$no' AND waktu = '$waktu' AND term='$term'") or die(mysqli_error($koneksi));

        // $queryEmail = mysqli_query($koneksi, "SELECT email FROM tb_user WHERE nama_user='$namapenerima'");
        // $email = mysqli_fetch_array($queryEmail)[0];



        // var_dump($notification);
        // die;

        // $notification = "Realisasi Berhasil Disetujui. Pemberitahuan via email telah terkirim ke $namapenerima ($email)";
    } else {
        $queryUpdateBpu = mysqli_query($koneksi, "UPDATE bpu SET realisasi='$newRealisasi',uangkembali = '$newUangKembali', tanggalrealisasi='$tanggalRealisasi',status_pengajuan_realisasi = '2' WHERE no='$no' AND waktu = '$waktu' AND term='$term'") or die(mysqli_error($koneksi));
    }
    $email = [];
    $nama = [];
    $queryBpu = mysqli_query($koneksi, "SELECT pengaju,namapenerima,jumlah FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    $bpu = mysqli_fetch_assoc($queryBpu);
    // $namapenerima = $bpu['namapenerima'];
    // $jumlah = $bpu['jumlah'];

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$s[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);

    if ($s['jenis'] == 'B1' || $s['jenis'] == 'B2') {
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

    $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
    $namaProject = mysqli_fetch_array($queryProject)[0];

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $msg = "Notifikasi BPU, <br><br>
        Realisasi telah disetujui dengan keterangan sebagai berikut:<br><br>
        Nama Project          : <strong>$namaProject</strong><br>
        Item No.              : <strong>$no</strong><br>
        Term                  : <strong>$term</strong><br>
        Realisasi Diajukan    : <strong>Rp. " . number_format($realisasi, 0, '', ',') . "</strong><br>
        Uang Kembali Diajukan : <strong>Rp. " . number_format($uangKembali, 0, '', ',') . "</strong><br>
        Tanggal Realisasi     : <strong>$tanggalRealisasi</strong><br>
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

    $notification = 'Realisasi Berhasil Disetujui. Pemberitahuan via email telah terkirim ke ';
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }

    if ($queryUpdateBpu) {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Realisasi Gagal Disetujui')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $kode . "'; </script>";
    }
}

<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
session_start();

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$userCheck = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$kode = $_POST['kode'];
$total = $_POST['totalbpu'];
$penerima = $_POST['penerima'];
$bank = $_POST['bank'];
$norek = $_POST['norek'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$berita_transfer = $_POST['berita_transfer'];
if ($_POST['status_sumber_rekening'] == 'pal-um') {
    $rekening_sumber = $_POST['rekening_sumber_mri_pal_um'];
} else if ($_POST['status_sumber_rekening'] == 'pal') {
    $rekening_sumber = $_POST['rekening_sumber_mri_pal'];
} else if ($_POST['status_sumber_rekening'] == 'kas') {
    $rekening_sumber = $_POST['rekening_sumber_mri_kas'];
}
// $keterangan_pembayaran = $_POST['keterangan_pembayaran'];
$alasanTolakBpu = $_POST['alasanTolakBpu'];
$umo_biaya_kode_id = $_POST['umo_biaya_kode_id'];
$submit = $_POST['submit'];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
$bpu = mysqli_fetch_assoc($queryBpu);
$pengaju = $bpu['pengaju'];
$namapenerima = $bpu['namapenerima'];
$jumlah = $bpu['pengajuan_jumlah'];

$queryRekening = mysqli_query($koneksi, "SELECT * FROM rekening WHERE no = '$bpu[rekening_id]'");
$rekening = mysqli_fetch_assoc($queryRekening);

if ($rekening['nama']) {
    $nama_pemilik_rekening = $rekening['nama'];
} else {
    $nama_pemilik_rekening = $namapenerima;
}

$sel1 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$uc = mysqli_fetch_assoc($sel1);
$numb = $uc['noid'];

$selbay = mysqli_query($koneksi, "SELECT noid,jenis,pengaju,nama FROM pengajuan WHERE waktu='$waktu'");
$s = mysqli_fetch_assoc($selbay);
$noid = $s['noid'];
$jenis = $s['jenis'];

if ($uc['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($uc['jenis'] == 'B1') {
    $isB1 = '-b1';
} else {
    $isB1 = '';
}

$nm_project = '"' . $s['nama'] . '", "item ke ' . $bpu['no'] . '", "BPU ke ' . $bpu['term'] . '"';

if ($submit == 1) {
    if ($bpu['statusbpu'] == 'UM' || $bpu['statusbpu'] == 'UM Burek') {
        $saldo = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user='$namapenerima'");
        $sld = mysqli_fetch_assoc($saldo);
        $saldobpu = $sld['saldo'];

        $query2 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$namapenerima' AND statusbpu IN ('UM', 'UM Burek') AND status IN ('Telah Di Bayar', 'Belum Di Bayar')";
        $result2 = mysqli_query($koneksi, $query2);
        $row2 = mysqli_fetch_array($result2);
        $totalUm = $row2['sumi'];

        // $query3 = "SELECT sum(realisasi) AS sumreal FROM bpu WHERE namapenerima='$namapenerima' AND statusbpu='UM Burek'";
        // $result3 = mysqli_query($koneksi, $query3);
        // $row3 = mysqli_fetch_array($result3);
        // $totalBurek = $row3['sumreal'];

        $saldosisa = $saldobpu - $totalUm;
        if ($jumlah > $saldosisa) {
            if ($divisi == 'FINANCE') {
                if ($_SESSION['hak_akses'] == 'Manager') {
                    echo "<script language='javascript'>";
                    echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
                    echo "</script>";
                    echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
                } else {
                    echo "<script language='javascript'>";
                    echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
                    echo "</script>";
                    echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
                }
            } else {
                echo "<script language='javascript'>";
                echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
                echo "</script>";
                echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
            }
            die;
        }
    }

    if ($metode_pembayaran == 'MRI PAL') {

        $arrNamaPenerima = explode(',', $bpu['namapenerima']);
        $arrBank = explode(',', $bpu['namabank']);
        $arrNorek = explode(',', $bpu['norek']);
        $arrEmail = explode(',', $bpu['emailpenerima']);

        for ($i = 0; $i < count($arrNamaPenerima); $i++) {
            $temp_namapenerima = trim($arrNamaPenerima[$i]);
            $temp_bank = trim($arrBank[$i]);
            $temp_norek = trim($arrNorek[$i]);
            $temp_email = trim($arrEmail[$i]);

            $date = date('my');
            $countQuery = mysqli_query($koneksiTransfer, "SELECT transfer_req_id FROM data_transfer WHERE transfer_req_id LIKE '$date%' ORDER BY transfer_req_id DESC LIMIT 1");
            $count = mysqli_fetch_assoc($countQuery);
            $count = (int)substr($count['transfer_req_id'], -4);

            $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$temp_bank'");
            $bank = mysqli_fetch_assoc($queryBank);

            $formatId = $date . sprintf('%04d', $count + 1);

            if ($bank['kodebank'] == "CENAIDJA") {
                $biayaTrf = 0;
            } else {
                $biayaTrf = 2900;
            }
            $insert = mysqli_query($koneksiTransfer, "INSERT INTO data_transfer (transfer_req_id, transfer_type, jenis_pembayaran_id, keterangan, waktu_request, norek, pemilik_rekening, bank, kode_bank, berita_transfer, jumlah, terotorisasi, hasil_transfer, ket_transfer, nm_pembuat, nm_validasi, nm_manual, jenis_project, nm_project, noid_bpu, biaya_trf, rekening_sumber, email_pemilik_rekening) 
                VALUES ('$formatId', '3', '1', '$bpu[statusbpu]', '$waktu', '$temp_norek', '$nama_pemilik_rekening','$bank[namabank]', '$bank[kodebank]', '$berita_transfer','$total', '2', '1', 'Antri', '$bpu[pengaju]', '$userCheck', '', '$s[jenis]', '$nm_project', '$bpu[noid]', $biayaTrf, '$rekening_sumber', '$temp_email')") or die(mysqli_error($koneksiTransfer));
        }

        if (!$insert) {
            if ($_SESSION['hak_akses'] == 'Manager') {
                echo "<script language='javascript'>";
                echo "alert('Verifikasi BPU Gagal!')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
            } else {
                echo "<script language='javascript'>";
                echo "alert('Verifikasi BPU Gagal!')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
            }
        }
    }

    $now = date_create('now')->format('Y-m-d H:i:s');
    $user = $_SESSION['nama_user'];

    if ($_FILES["gambar"]["name"]) {
        $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $nama_gambar = random_bytes(20) . "." . $extension;
        $target_file = "uploads/" . $nama_gambar;
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
        $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$total, status_pengajuan_bpu = 0, checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran', umo_biaya_kode_id = '$umo_biaya_kode_id' fileupload='$nama_gambar', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
    } else {
        $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$total, status_pengajuan_bpu = 0, checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran', umo_biaya_kode_id = '$umo_biaya_kode_id', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE no='$no' AND waktu='$waktu' AND term='$term'") or die(mysqli_error($koneksi));
    }

    $email = [];
    $nama = [];
    if ($jumlah <= $setting['plafon']) {
        if ($uc['jenis'] == 'B1' || $uc['jenis'] == 'B2') {
            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3') AND hak_akses='Manager'");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                }
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3') AND hak_akses='Manager'");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                }
            }
        }
    } else {
        if ($jenis != 'Rutin') {
            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                }
            }
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['email']);
        array_push($nama, $emailUser['nama_user']);
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['email']);
        array_push($nama, $emailUser['nama_user']);
    }

    $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
    $namaProject = mysqli_fetch_array($queryProject)[0];

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';


    $msg = "Notifikasi BPU, <br><br>
        BPU telah di verifikasi oleh Finance dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>$namaProject</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju   : <strong>$pengaju</strong><br>
        Nama Penerima  : <strong>$namapenerima</strong><br>
        Total Diajukan : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br>
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

    if ($jenis != 'Rutin') {
        $notification = 'Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke ';
        $i = 0;
        for ($i = 0; $i < count($email); $i++) {
            $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
            if ($i++ < count($email) - 1) $notification .= ', ';
            else $notification .= '.';
        }
    } else {
        $notification = 'Verifikasi BPU Sukses.';
    }
} else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

    $email = [];
    $nama = [];
    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user='$pengaju' AND aktif='Y'");
    $query =  mysqli_fetch_array($queryEmail);
    array_push($nama, $query['nama_user']);
    array_push($email, $query['email']);

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$query[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    if ($user) {
        array_push($email, $user['email']);
        array_push($nama, $user['nama_user']);
    }

    if ($uc['jenis'] == 'B1' || $uc['jenis'] == 'B2') {
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
        BPU telah di tolak oleh Finance dengan keterangan sebagai berikut:<br><br>
        Nama Project      : <strong>$namaProject</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju      : <strong>$pengaju</strong><br>
        Nama Penerima     : <strong>$namapenerima</strong><br>
        Total Diajukan    : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br><br>
        ";
    if ($alasanTolakBpu) {
        $msg .= "Ditolak dengan alasan <strong> $alasanTolakBpu </strong>.<br><br>";
    } else {
        $msg .= "Ditolak tanpa alasan.<br><br>";
    }
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Aplikasi Budget";

    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }
    $notification = 'Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }
    // $notification = "Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke $pengaju ($email)";
}
// die;

if ($update) {
    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
    }
} else {
    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('Verifikasi BPU Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Verifikasi BPU Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
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

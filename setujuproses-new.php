<?php
//error_reporting(0);
require_once "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/email.php";

$emailHelper = new Email();

$con = new Database();
$koneksi = $con->connect();

$messageHelper = new Message();
$whatsapp = new Whastapp();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();


require "vendor/email/send-email.php";

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$userSetuju = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];
$aksesSes = $_SESSION['hak_akses'];
date_default_timezone_set("Asia/Bangkok");

$time = date('Y-m-d H:i:s');
$finance      = $_SESSION['divisi'];

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$urgent       = $_POST['urgent'];
$term       = $_POST['term'];
$tanggalbayar = $_POST['tanggalbayar'];
$alasanTolakBpu = $_POST['alasanTolakBpu'];

$dt = new DateTime($tanggalbayar);

$queryItemBudget = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu' AND no = '$no'");
$itemBudget = mysqli_fetch_assoc($queryItemBudget);

$queryBudget = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu = '$waktu'");
$budget = mysqli_fetch_assoc($queryBudget);
$idBudget = $budget['noid'];

if ($budget['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($urgent == 'Urgent') {
    $tanggalbayar = $time;
} else {
    $d = mktime(8, 15, 0);
    $hour = date("H:i:s", $d);
    $tanggalbayar = $_POST['tanggalbayar'] . ' ' .  $hour;
}

$dt = new DateTime($tanggalbayar);

$email = [];
$nama = [];
$arrPenerima = [];
$arrJumlah = [];
$pengaju = '';
$arrPembayaran = [];
$idUsersNotification = [];
$dataDivisi = [];
$dataLevel = [];
$arremailpenerima = [];


$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no = '$no' AND waktu = '$waktu' AND term = '$term'");

if ($_POST['submit'] == 1) {
    while ($item = mysqli_fetch_assoc($queryBpu)) {
        array_push($arrPembayaran, $item['metode_pembayaran']);
        array_push($arrPenerima, $item['namapenerima']);
        array_push($arrJumlah, "Rp. " . number_format($item['jumlah'], 0, ",", "."));
        $pengaju = $item['pengaju'];

        if ($item['metode_pembayaran'] == 'MRI PAL') {
            $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET jadwal_transfer ='$tanggalbayar', nm_otorisasi = '$userSetuju' WHERE noid_bpu = '$item[noid]'") or die(mysqli_error($koneksiTransfer));

            if (!$update) {
                echo "<script language='javascript'>";
                echo "alert('BPU Gagal Disetujui!')";
                echo "</script>";
                if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
                if ($aksesSes == 'Manager') echo "<script> document.location.href='views-finance-manager.php?code=" . $idBudget . "'; </script>";
                else echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
            }
        }

        $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$item[namabank]'");
        $bank = mysqli_fetch_assoc($queryBank);
        if ($itemBudget['status'] == 'Vendor/Supplier' || $itemBudget['status'] == 'Honor Eksternal') {
            $explodeString = explode('.', $bpu['ket_pembayaran']);
            array_push($arremailpenerima, $item['emailpenerima']);
            if ($itemBudget['status'] == 'Vendor/Supplier') {
                $msg = "Kepada " . $item['namapenerima'] . ", <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                No.Invoice       : <strong>" . $explodeString[1] . "</strong><br>
                Tgl. Invoice     : <strong>" . $explodeString[2][0] . $explodeString[2][1] . "/" . $explodeString[2][2] .  $explodeString[2][3] . "/20" . $explodeString[2][4] . $explodeString[2][5] . "</strong><br>
                Term             : <strong>" . $explodeString[3][1] . " of " . $explodeString[3][3] . "</strong><br>
                Jenis Pembayaran : <strong>" . $explodeString[4] . "</strong><br>
                No. Rekening Anda : <strong>" . $item['norek'] . "</strong><br>
                Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                Nama Penerima    : <strong>" . $item['namapenerima'] . "</strong><br>
                Jumlah Dibayarkan : <strong>Rp. " . number_format($item['jumlah'], 0, '', '.') . "</strong><br>
                Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
                Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                Hormat kami,<br>
                Finance Marketing Research Indonesia
                ";
            } else {
                $msg = "Kepada " . $item['namapenerima'] . ", <br><br>
                Berikut informasi status pembayaran yang akan Anda terima:<br><br>
                Nama Pembayaran  : <strong>" . $item['ket_pembayaran'] . "</strong><br>
                No. Rekening Anda : <strong>" . $item['norek'] . "</strong><br>
                Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                Nama Penerima    : <strong>" . $item['namapenerima'] . "</strong><br>
                Jumlah Dibayarkan : <strong>Rp. " . number_format($item['jumlah'], 0, '', '.') . "</strong><br>
                Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
                Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                Hormat kami,<br>
                Finance Marketing Research Indonesia
                ";
            }
            $subject = "Informasi Pembayaran";

            if ($item['emailpenerima']) {
                $message = $emailHelper->sendEmail($msg, $subject, $item['emailpenerima'], $name = '', $address = "single");
            }
        }


        // $queryEmail = mysqli_query($koneksi, "SELECT phone_number,nama_user FROM tb_user WHERE nama_user = '$item[pengaju]' AND aktif='Y'");
        // $emailUser = mysqli_fetch_assoc($queryEmail);
        // if ($emailUser['phone_number'] != "") {
        //     array_push($email, $emailUser['phone_number']);
        //     array_push($nama, $emailUser['nama_user']);
        // }

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$item[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser['phone_number'] != "") {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$budget[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser['phone_number']) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataLevel, $emailUser['level']);
    }

    if ($budget['jenis'] == 'B1' || $budget['jenis'] == 'B2') {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number'] != "") {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number'] != "") {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
            }
        }
    }

    $nama = array_unique($nama);
    $email = array_unique($email);

    if ($divisi == 'FINANCE') {
        $persetujuan = 'Disetujui (Sri Dewi Marpaung)';
    } else {
        $persetujuan = 'Disetujui (Direksi)';
    }

    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu =0, persetujuan = '$persetujuan', tanggalbayar = '$tanggalbayar', urgent = '$urgent', approveby = '$userSetuju', tglapprove = '$time'
                               WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");

    if ($update) {
        $notification = 'BPU Telah Disetujui. Pemberitahuan via whatsapp telah terkirim ke ';
        $i = 0;
        for($i = 0; $i < count($email); $i++) {
            $path = '/views.php';
            if ($dataDivisi[$i] == 'FINANCE') {
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
            } else if ($dataDivisi[$i] == 'Direksi') {
                $path = '/views-direksi.php';
            }
          $url =  $host. $path.'?code='.$idBudget.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
          $msg = $messageHelper->messageApprovePengajuanBPU($userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url);
          if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);

          $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
          if ($i < count($email) - 1) $notification .= ', ';
          else $notification .= '.';
        }

        if (count($arremailpenerima) > 0) {
            $notification .= " Dan telah dikirim pemberitahuan ke penerima via email ke " . implode(",", $arremailpenerima);
            # code...
        }
    }
} else if ($submit == 0) {
    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah = null, status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    while ($bpu = mysqli_fetch_assoc($queryBpu)) {
        array_push($arrPenerima, $bpu['namapenerima']);
        array_push($arremailpenerima, $item['emailpenerima']);
        array_push($arrJumlah, "Rp. " . number_format($bpu['pengajuan_jumlah'], 0, ",", "."));

        $queryEmail = mysqli_query($koneksi, "SELECT* FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['email']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
        }

        $queryEmail = mysqli_query($koneksi, "SELECT* FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['email']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
        }

        if ($budget['jenis'] == 'B1' || $budget['jenis'] == 'B2') {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                }
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                }
            }
        }
    }

    $email = array_unique($email);
    $nama = array_unique($nama);

    $msg = "Notifikasi BPU, <br><br>
        BPU telah di tolak oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>" . $budget['nama'] . "</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju   : <strong>" . $bpu['pengaju'] . "</strong><br>
        Nama Penerima  : <strong>" . implode(', ', $arrPenerima) . "</strong><br>
        Total Diajukan : <strong>" . implode(', ', $arrJumlah) . "</strong><br>
        ";
    if ($alasanTolakBpu) {
        $msg .= "Ditolak dengan alasan <strong> $alasanTolakBpu </strong>.<br><br>";
    } else {
        $msg .= "Ditolak tanpa alasan.<br><br>";
    }
    $msg .= "Klik <a href='$host'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Aplikasi Budget";

    if ($arremailpenerima) {
        $message = $emailHelper->sendEmail($msg, $subject, $arremailpenerima, $name, $address = "multiple");
    }

    $notification = 'BPU Telah Disetujui. Pemberitahuan via whatsapp telah terkirim ke ';
    $i = 0;
    for($i = 0; $i < count($email); $i++) {
        $path = '/views.php';
        if ($dataDivisi[$i] == 'FINANCE') {
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
            $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
        } else if ($dataDivisi[$i] == 'Direksi') {
            $path = '/views-direksi.php';
        }
        $url =  $host. $path.'?code='.$idBudget.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
        $msg = $messageHelper->messageTolakPengajuanBPU($userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url);
        if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);

        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }

    if (count($arremailpenerima) > 0) {
        $notification .= " Dan telah dikirim pemberitahuan ke penerima via email ke " . implode(",", $arremailpenerima);
        # code...
    }
    
}

if ($update) {
    if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $idBudget . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
        }
    } else if ($aksesSes == 'HRD') {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
    }
} else {
    if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $idBudget . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
        }
    } else if ($aksesSes == 'HRD') {
        echo "<script language='javascript'>";
        echo "alert('Gagal')";
        echo "</script>";
        echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gagal')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
    }
}
